<?php


namespace common\models;

use Yii;

class NewsData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     * cms文章
     */
    public static function tableName()
    {
        return 'phome_ecms_news_data_1';
    }

    public static function getDb()
    {
        return Yii::$app->get('db2');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

        ];
    }

    /** 将数据库中的文章插入到 选中的数据库中 */
    public static function createOne($data)
    {
        $error = [];
//        echo '<pre>';
//        print_r($data['WhiteArticle']);
//        exit;

        //获取最后一个id
        $lastId = self::find()->orderBy('id desc')->one()->id;
        if (!empty($data['db_tags_name'])) {
            $data['db_tags_name'] = json_decode($data['db_tags_name'], true);
        }

        $model = new NewsData();
        $model->id = $lastId + 1;
        $model->classid = $data['db_class_id'];
        $model->dokey = 1;
        $model->infotags = $data['db_tags_name'][0];
        $model->writer = '小仲马';
        $model->befrom = '何马英语';
        $model->newstext = $data['content'];

        //保存文章
        if (!$model->save()) {
            return [-1, $model->getErrors()];
        }

        //保存索引
        list($indexCode, $indexMsg) = NewsInfoIndex::createOne($data);
        if ($indexCode < 0) {
            $error[] = $indexMsg;
        }

        //获取classname
        $clasPath = NewsClass::find()->where(['classid' => $data['db_class_id']])->one()->classpath;
        //获取纯文本
        $contentTxt = Tools::cleanHtml($data['content']);

        //文章内容保存
        $info = [
            'classid' => $data['db_class_id'],
            'filename' => $model->id,
            'titleurl' => '/' . $clasPath . '/' . $model->id . '.html',
            'keyboard' => $data['title'],
            'title' => $data['title'],
            'titlepic' => !empty($data['title_img']) ? $data['title_img'] : 'https://www.thszxxdyw.org.cn/d/file/p/2020/06-28/637035e2da1f0a3f541451cb96e2fe0e.jpg',    //标题图片
            'ftitle' => '',
            'smalltext' => mb_substr($contentTxt, 0, rand(45, 70)),   //文章简介
        ];

        list($codeInfo, $msgInfo) = NewsInfo::createOne($info);
        if ($codeInfo < 0) {
            $error[] = $msgInfo;
        }

        if (!empty($data['db_tags_name'])) {
            //保存所有的标签
            foreach ($data['db_tags_name'] as $item) {
                $tags = [
                    'tagname' => $item,
                ];

                //插入标签
                list($codeTags, $msgTags) = NewsTags::createOne($tags);

                if ($codeTags < 0) {
                    $error[] = $msgTags;
                } else { //成功了 则保存索引
                    $classTags = [
                        'tagid' => $msgTags,
                        'classid' => $data['db_class_id'],
                        'id' => $model->id,  //文章id
                    ];
                    //插入标签索引
                    list($codeClasstags, $msgClassTags) = NewsClassTags::createOne($classTags);
                    if ($codeClasstags < -1) {
                        $error[] = $msgClassTags;
                    }
                }
            }

            return [1, $model->id];

            if (empty($error)) {
                return [1, $model->id];
            } else {
                return [-1, $error];
            }
        }
    }

    /** 生成静态页面 */
    public static function setStaticHtml($classId, $newsId, $hostName)
    {
        $url = $hostName . '/e/heshao/ecmschtml.php?enews=ReNewsHtml&classid=&retype=&startday=&endday=&startid=&endid=&havehtml=1&reallinfotime=1594190732&tbname=news&yii2_msg=1&news_id=' . $newsId . '&classid=' . $classId . '&list_html=1';
        $res = Tools::curlGet($url);
        $tag = new CmsAction();
        
        //进行文章消息推送
        list($code, $msg) = $tag->result();

        echo '<pre>';
        print_r($url);
        echo '<hr/>';
        print_r($res);
        exit;
        echo '<pre>';
        print_r($res);
        exit;
    }
}