<?php


namespace common\models;

use Yii;

class NewsClassTags extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'phome_enewstagsdata';
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

    /**
     * 设置标签页面
     */
    public static function setTags()
    {
        //获取所有的 文章分类
        $class = NewsClass::find()->all();
        $data = [];
        foreach ($class as $cl) {
            $tags = self::find()->where(['classid' => $cl->classid])->all();

            $tagArr = [];
            foreach ($tags as $tag) {
                $tagName = NewsTags::find()->where(['tagid' => $tag->tagid])->one();
                $tagArr[] = [
                    'name' => $tagName ? $tagName->tagname : '',
                    'url' => '/e/tags/?tagid=' . $tag->id
                ];
            }
            $data[] = [
                'class_id' => $cl->classid,
                'class_name' => $cl->classname,
                'tags' => $tagArr
            ];
        }

        $tagStr = '';
        foreach ($data as $item) {
            $tagStr .= '<br/>';
            $tagStr .= '<div class="map w120 m0a mt90">' . $item['class_name'] . '</div>';
            $tagStr .= '<br/>';
            foreach ($item['tags'] as $tag) {
                $tagStr .= '<a href="' . $tag['url'] . '">' . $tag['name'] . '</a>';
                $tagStr .= '<br/>';
            }
        }

        $domain = Yii::$app->request->get('domain');
        $path = '/www/wwwroot/' . $domain . '/tags.html';
        $res = file_get_contents($path);
        $res = preg_replace('@<div id="content">(.*)?</div>@s', '<div id="content"></div>', $res);
        $str = str_replace('<div id="content"></div>', '<div id="content">' . $tagStr . '</div>', $res);
        file_put_contents($path, $str);
        exit;
    }
}