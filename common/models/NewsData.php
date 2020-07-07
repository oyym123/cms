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

        $model = new NewsData();
        $model->id = $lastId + 1;
        $model->classid = $data['db_class_id'];
        $model->dokey = 1;
        $model->infotags = '';
        $model->writer = '何马';
        $model->befrom = 'Yii2';
        $model->newstext = $data['content'];

        //保存文章
        if (!$model->save()) {
            return [-1, $model->getErrors()];
        }

        //文章索引保存
        $info = [
            'classid' => $data['db_class_id'],
            'filename' => $model->id,
            'titleurl' => '',
            'keyboard' => '',
            'title' => '',
            'titlepic' => '',    //标题图片
            'ftitle' => '',
            'smalltext' => '',   //文章简介
        ];

        list($codeInfo, $msgInfo) = NewsInfo::createOne($info);
        if ($codeInfo < 0) {
            $error[] = $msgInfo;
        }

        if (!empty($data['db_tags_id'])) {
            $data['db_tags_id'] = json_decode($data['db_tags_id'], true);
            //保存所有的标签
            foreach ($data['db_tags_id'] as $item) {
                $tags = [
                    'tagname' => BaiduKeywords::find()->where(['id' => $item])->one()->keywords,
                ];
                //插入标签
                list($codeTags, $msgTags) = NewsTags::createOne($tags);

                if ($codeTags < -1) {
                    $error[] = $msgTags;
                } else { //成功了 则保存索引
                    $classTags = [
                        'tagid' => $msgTags->tagid,
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
            if (empty($error)) {
                return [1, 'success'];
            } else {
                return [-1, $error];
            }
        }
    }
}