<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "keywords".
 *
 * @property int $id
 * @property string|null $keywords 关键词
 * @property int|null $type 类型
 * @property int|null $sort 排序
 * @property string|null $form 来自地方 人工 = user 其他= other
 * @property string|null $rank 排名 来自爱站网
 * @property int|null $search_num 搜索量
 * @property string|null $title 网页标题
 * @property string|null $content 具体内容
 * @property string|null $note 备注
 * @property string|null $url 链接
 * @property string|null $created_at 创建时间
 * @property string|null $updated_at 修改时间
 */
class Keywords extends \yii\db\ActiveRecord
{

    const FROM_USER = 'user';   //来自人工
    const FROM_ROBOT = 'robot'; //来自机器人

    const TYPE_SHORT = 1;       //短尾词
    const TYPE_LONG = 2;        //长尾词


    /** 获取所有来源 */
    public static function getFrom($key = 'all')
    {
        $data = [
            self::FROM_USER => '人工',
            self::FROM_ROBOT => '机器人',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    /** 获取所有的类型 */
    public static function getType($key = 'all')
    {
        $data = [
            self::TYPE_SHORT => '短尾词',
            self::TYPE_LONG => '长尾词',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'keywords';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort', 'search_num', 'type'], 'integer'],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['keywords', 'form', 'rank', 'title', 'note', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'keywords' => '关键词',
            'sort' => '排序',
            'type' => '类型',
            'form' => '来自',
            'rank' => '爱站排名',
            'search_num' => '搜索数量',
            'title' => '网页标题',
            'content' => '内容页数据',
            'note' => '备注',
            'url' => '链接',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /** 创建一个关键词*/
    public static function createOne($data)
    {
        if (empty($data['keywords'])) {
            return [-1, '没有关键词'];
        }

        $keywords = Keywords::find()->where(['keywords' => $data['keywords']])->one();

        if (!empty($keywords)) {
            return [-1, '关键词重复'];
        }

        $model = new Keywords();
        $model->keywords = $data['keywords'];
        $model->sort = $data['sort'] ?? 0;
        $model->type = $data['type'];
        $model->form = $data['form'] ?? 'user';
        $model->rank = $data['rank'];
        $model->search_num = $data['search_num'];
        $model->title = $data['title'];
        $model->content = $data['content'];
        $model->note = $data['note'] ?? '';
        $model->url = $data['url'] ?? '';
        $model->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');

        if (!$model->save(false)) {
            return [-1, $model->getErrors()];
        } else {
            return [1, 'success'];
        }
    }

    /** 爬取爱站网的竞品关键词数据 */
    public static function catchKeyWords()
    {
        set_time_limit(0);
        //爱站网址 mobile =手机
        $aizhanUrl = 'https://baidurank.aizhan.com/mobile/';
        $targetUrl = 'tingclass.net';
        for ($i = 5; $i < 50; $i++) {
            $url = $aizhanUrl . $targetUrl . '/-1/0/' . ($i + 1) . '/position/1/';
            $res = Tools::curlGet($url);
//            file_put_contents('./test.php', $res);
//            $res = file_get_contents('./test.php');
            preg_match('@<td class="title">
							<a class="gray"(.*)?</td>
					</tr>@s', $res, $result);
            $result = array_filter(explode('<td class="title">', $result[0]));
            if (empty($result)) {
                exit('没有数据了！');
            }

            $error = [];
//            $result = array_splice($result,0,1);

            foreach ($result as $re) {
                sleep(1);
                preg_match('@第(.*)?位@', $re, $rank1);
                preg_match('@第(.*)?页@', $re, $rank2);
                preg_match('@/">(.*)?</td>
						<td class="owner">@s', $re, $searchNum);
                preg_match('@<a name="baiduLink" rel="nofollow" target="_blank" href="(.*)?" class="gray" title="(.*)?">@', $re, $info);
                preg_match('@<a class="gray" rel="nofollow" target="_blank" href="(.*)?" title="(.*)?">@', $re, $keywords);
                $content = Tools::curlGet($info[1]);
                $data = [
                    'rank' => $rank1[1] . ',' . $rank2[1],
                    'search_num' => intval($searchNum[1]),
                    'type' => self::TYPE_SHORT,
                    'keywords' => $keywords[2],
                    'url' => $info[1],
                    'title' => $info[2],
                    'form' => self::FROM_ROBOT,
                    'content' => $content,
                ];
                list($code, $msg) = self::createOne($data);
                if ($code < 0) {
                    $error[] = $keywords[2] . $msg;
                }
            }
        }
    }
}
