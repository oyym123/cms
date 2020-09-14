<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "domain_column".
 *
 * @property int $id
 * @property string|null $name 名称
 * @property string|null $tags 标签名称 逗号隔开
 * @property int|null $domain_id 域名id
 * @property string|null $domain_name 域名名称
 * @property int|null $user_id 用户id
 * @property int|null $status 10=正常 20=禁用
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class DomainColumn extends Base
{

    public static function getType($key = 'all')
    {
        $data = [
            'SPO' => '体育运动',
            'MIL' => '国防军事',
            'HOU' => '房产装修',
            'CUL' => '文学艺术',
            'ITC' => '网络技术',
            'CAR' => '公司管理',
            'HEA' => '医疗健康',
            'ENT' => '影视娱乐',
            'LEA' => '教育培训',
            'AUT' => '汽车汽配',
            'BUS' => '金融财经',
            'MAC' => '仪器机械',
            'AGR' => '农业林园',
            'IND' => '化工轻工',
            'FOO' => '厨房美食',
            'GAM' => '游戏应用',
            'SER' => '生活服务',
            'SHO' => '购物败家',
            'UNC' => '其他',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'domain_column';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['domain_id', 'tags', 'name', 'type'], 'required'],
            [['domain_id', 'user_id', 'status', 'mobile_show', 'pc_show', 'sort', 'is_change'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'tags', 'domain_name', 'zh_name', 'type', 'title', 'keywords', 'intro'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '英文名称',
            'tags' => 'Tags',
            'type' => '类型',
            'zh_name' => '中文名称',
            'pc_show' => 'PC端是否显示',
            'mobile_show' => '移动端是否显示',
            'title' => '标题',
            'is_change' => '是否随机列表内容',
            'keywords' => '关键字',
            'intro' => '简介',
            'sort' => '排序',
            'domain_id' => '域名id',
            'domain_name' => '域名',
            'user_id' => '创建者',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    //根据当前域名获取 分类
    public static function getColumn($id = 0, $name = '', $from = '')
    {
        $name = $name ?: $_SERVER['HTTP_HOST'];

        if ($id) {
            //查询这个域名下的所有类目
            $domain = Domain::find()->where(['id' => $id])->one();
        } else {
            //查询这个域名下的所有类目
            $domain = Domain::find()->where(['name' => Tools::getDoMain($name)])->one();
        }

        if ($domain) {
            $andWhere = [];
            //表示真人浏览 则判断是否显示
            if ($from === 'person') {
                if (Tools::isFromMobile() || strpos($_SERVER['HTTP_HOST'], 'm.') !== false) {
                    $andWhere = ['mobile_show' => self::S_ON];
                } else {
                    $andWhere = ['pc_show' => self::S_ON];
                }
            }
            $column = DomainColumn::find()->select('id,zh_name,name')->where([
                'domain_id' => $domain->id,
                'status' => self::STATUS_BASE_NORMAL
            ])->orderBy('sort desc')->andWhere($andWhere)->asArray()->all();
            $arr = [];

            //将home 放第一位
            foreach ($column as $key => $item) {
                if ($item['name'] == 'home') {
                    //表示真人浏览 则判断是否显示
                    if ($from === 'person') {
                        $item['name'] = '/';
                    }
                    $arr[0] = $item;
                } else {
                    $arr[$key + 1] = $item;
                    if ($from != 'fan') {
                        $arr[$key + 1]['name'] = '/' . $item['name'];
                    }
                }
            }

            ksort($arr);

            return $arr;
        }

        return [];
    }


    /** 获取域名 */
    public function getDomain()
    {
        return $this->hasOne(Domain::className(), ['id' => 'domain_id']);
    }

    public static function createOne($data)
    {
        $old = DomainColumn::find()->where([
            'domain_id' => $data['domain_id'],
            'name' => $data['name']
        ])->one();

        if (!empty($old)) {
            return [-1, '类目名称不可相同'];
        }

        $model = new DomainColumn();
        $model->name = $data['name'];
        $model->tags = $data['tags'] ?? '';
        $model->domain_id = $data['domain_id'];
        $model->domain_name = '';
        $model->created_at = date('Y-m-d H:i:s');
        if (!$model->save(false)) {
            return [-1, $model->getErrors()];
        }
        return [1, $model];
    }

    public static function getColumnData($domainId)
    {
        $res = DomainColumn::find()->where(['domain_id' => $domainId])->asArray()->all();
        return ArrayHelper::map($res, 'id', 'name');
    }

}
