<?php

namespace common\models;

use library\pepper\Modelx;
use Yii;
use yii\db\Migration;

/**
 * This is the model class for table "push_article".
 *
 * @property int $id
 * @property int|null $b_id 索引黑帽文章id
 * @property int|null $column_id 类目id
 * @property string|null $column_name 类名
 * @property int|null $rules_id 规则id
 * @property int|null $domain_id 域名id
 * @property string|null $domain 域名
 * @property string|null $from_path 来路地址
 * @property string|null $keywords 关键词
 * @property string|null $title_img 标题图片地址
 * @property int|null $status 10=状态有效 20=无效
 * @property string|null $content 内容
 * @property string|null $intro 文章简介
 * @property string|null $title 标题
 * @property string|null $push_time 发布时间
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class PushArticle extends Modelx
{

    //原始表名，表结构母版
    protected static $originalName = 'push_article';
    //redis表名set key
    protected static $tableSetKey = 'project:tableset';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        //根据域名请求，判断该使用哪个表
        $domain = Domain::getDomainInfo();
        if ($domain && Yii::$app->request->get('domain', 1)) {
            return 'push_article_' . $domain->id;
        }
        return static::$originalName . '_' . (static::$targetKey);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['b_id', 'column_id', 'rules_id', 'domain_id', 'status'], 'integer'],
            [['content'], 'string'],
            [['push_time', 'created_at', 'updated_at'], 'safe'],
            [['column_name', 'domain', 'from_path', 'keywords', 'title_img', 'intro', 'title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'b_id' => 'B ID',
            'column_id' => '栏目',
            'column_name' => 'Column Name',
            'rules_id' => '规则id',
            'domain_id' => '域名',
            'domain' => 'Domain',
            'from_path' => '来源',
            'keywords' => '关键词',
            'title_img' => '推荐图片',
            'status' => '状态',
            'content' => '内容',
            'intro' => '简介',
            'title' => '标题',
            'push_time' => '发布时间',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /** 发布文章 */
    public static function createOne($data)
    {
        $model = new PushArticle();
        foreach ($data as $key => $item) {
            $model->$key = $item;
        }

        $model->created_at = date('Y-m-d H:i:s');
        if (!$model->save(false)) {
            return [-1, $model->getErrors()];
        } else {
            return [1, $model];
        }
    }

    /** 热门文章 */
    public static function hotArticle($num = 10)
    {
        $article = PushArticle::find()
            ->select('key_id,id,user_id,title_img,push_time,title,column_id,column_name')
            ->orderBy('user_id desc')
            ->where(['like', 'title_img', 'http'])
            ->asArray()
            ->limit($num)
            ->all();

        $columnZhName = '';
        if (!empty($article)) {
            $columnObj = DomainColumn::findOne($article[0]['column_id']);
            if (!empty($columnObj)) {
                $columnZhName = $columnObj->zh_name;
                $columnEnName = $columnObj->name;
            }
        }

        foreach ($article as $key => &$item) {
            if ($user = FanUser::findOne($item['user_id'])) {
                $item['nickname'] = $user->username;
                $item['avatar'] = $user->avatar;
            } else {
                $item['nickname'] = '佚名';
                $item['avatar'] = 'http://img.thszxxdyw.org.cn/userImg/b4ae0201906141846584975.png';
            }
            $item['title'] = Tools::getKTitle($item['title']);
            $item['user_url'] = '/user/index_' . $item['user_id'] . '.html';
            $item['url'] = '/' . $columnEnName . '/' . $item['id'] . '.html';
        }

        return $article;
    }

    /** 获取用户名称 */
    public function getFanUser()
    {
        return $this->hasOne(FanUser::className(), ['id' => 'user_id']);
    }

    /** 最新文章 */
    public static function newArticle($num = 10)
    {
        $article = PushArticle::find()
            ->select('id,title_img,push_time,column_name,column_id,title')
            ->limit($num)
            ->orderBy('id desc')
            ->asArray()
            ->all();

        $columnZhName = '';
        if (!empty($article)) {
            $columnObj = DomainColumn::findOne($article[0]['column_id']);
            if (!empty($columnObj)) {
                $columnZhName = $columnObj->zh_name;
                $columnEnName = $columnObj->name;
            }
        }


        foreach ($article as &$item) {
            $item['title'] = Tools::getKTitle($item['title']);
            $item['url'] = '/' . $columnEnName . '/' . $item['id'] . '.html';
        }

        return $article;
    }

    /** 创建新的表 */
    public static function createTable($id)
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        $migrate = new Migration();
        $migrate->createTable('{{%push_article_' . $id . '}}', [
            'id' => $migrate->primaryKey(),
            'b_id' => $migrate->integer(11)->defaultValue(0)->comment('索引黑帽文章id'),
            'column_id' => $migrate->integer(11)->defaultValue(0)->comment('类目id'),
            'column_name' => $migrate->string(255)->defaultValue('')->comment('类名'),
            'domain' => $migrate->string(25)->defaultValue('')->comment('域名'),
            'domain_id' => $migrate->integer(11)->defaultValue(0)->comment('域名id'),
            'from_path' => $migrate->string(255)->defaultValue('')->comment('来路地址'),
            'fan_key_id' => $migrate->integer(11)->defaultValue(0)->comment('泛目录关键词id'),
            'key_id' => $migrate->integer(11)->defaultValue(0)->comment('关键词id'),
            'keywords' => $migrate->string(30)->defaultValue('')->comment('关键词'),
            'user_id' => $migrate->string(30)->defaultValue('')->comment('用户id'),
            'rules_id' => $migrate->integer(11)->defaultValue(0)->comment('规则id'),
            'content' => $migrate->text()->comment('内容'),
            'title_img' => $migrate->string(255)->defaultValue('')->comment('图片'),
            'all_part_content' => $migrate->text()->comment('中文内容'),
            'en_part_content' => $migrate->text()->comment('英文内容'),
            'fan_part_content' => $migrate->text()->comment('繁体内容'),
            'status' => $migrate->smallInteger()->defaultValue(10)->comment('10=状态有效 20=无效'),
            'intro' => $migrate->string(255)->defaultValue('')->comment('文章简介'),
            'title' => $migrate->string(255)->defaultValue('')->comment('标题'),
            'push_time' => $migrate->dateTime()->comment('发布时间'),
            'created_at' => $migrate->dateTime()->comment('创建时间'),
            'updated_at' => $migrate->dateTime()->comment('修改时间'),
        ], $tableOptions);

        //关键字id索引
        $migrate->createIndex('key_id-index', 'push_article_' . $id, ['key_id'], false);
        $initSql = file_get_contents(__DIR__ . '/../../frontend/web/init.sql');

        $initSql = str_replace('push_article_5', 'push_article_' . $id, $initSql);

        //插入初始数据
        $db = Yii::$app->db;
        $db->createCommand($initSql)->execute();
    }

    /** 替换掉栏目名称 */
    public static function replaceColumn($column)
    {
        $_GET['domain'] = 0;
        PushArticle::updateAllx($column->domain_id, ['column_name' => $column->name], ['column_id' => $column->id]);
    }

    //拉取文章数据
    public static function setArticle($data)
    {
        $_GET['domain'] = 0;

        //当第一个 id = 455709 清空表 因为是测试数据
        $first = PushArticle::findx($data['domain_id'])->select('id')->orderBy('id asc')->one();

        if ($first->id == 455709) {
            $db = Yii::$app->db;
            $sql1 = 'truncate table `push_article_' . $data['domain_id'] . '`;';
            $db->createCommand($sql1)->execute();

            //自增变为0开始
            $sql = ' alter table `push_article_' . $data['domain_id'] . '` auto_increment=0';
            $db->createCommand($sql)->execute();
        }

        $bd = AllBaiduKeywords::findOne($data['key_id']);

        if ($bd->column_id == 0) {
            $bd->domain_id = $data['domain_id'];
            $bd->column_id = $data['column_id'];
            $bd->save();
            PushArticle::batchInsertOnDuplicatex($data['domain_id'], [$data]);
        } else {
            echo '该词已被' . $bd->domain_id . '使用!';
        }
    }

    /** 有道翻译文章 */
    public static function transArticle($str = '')
    {
        //繁体
        $chinese = new Chinaese();
        $data = $chinese->cns('万事如意');

        $str = '翻译';

        //有道翻译 英文
        $ret = (new YouDaoApi())->startRequest($str);
        $ret = json_decode($ret, true);

        print_r($data);
        exit;

        $enRes = explode('{*}', $ret['translation'][0]);

        echo '<pre>';
        print_r($enRes);
        exit;
    }

    /** 获取域名 */
    public function getDomain()
    {
        return $this->hasOne(Domain::className(), ['id' => 'domain_id']);
    }

    public function fanti()
    {

    }

}
