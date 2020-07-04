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
        $tagsIds = $data = [];
        foreach ($class as $cl) {
            $tags = self::find()->where(['classid' => $cl->classid])->all();
            $tagArr = [];
            foreach ($tags as $tag) {
                $tagName = NewsTags::find()->where(['tagid' => $tag->tagid])->one();
                $tagArr[] = [
                    'name' => $tagName ? $tagName->tagname : '',
                    'url' => '/e/tags/?tagid=' . $tag->tagid
                ];
                $tagsIds[] = $tag->tagid;
            }
            $data[] = [
                'class_id' => $cl->classid,
                'class_name' => $cl->classname,
                'tags' => $tagArr
            ];
        }

        $tagStr = '';
        $tagsArray = NewsTags::find()->where(['not in', 'tagid', $tagsIds])->asArray()->all();

        foreach ($data as $item) {
            $tagStr .= '<dl><dt>' . $item['class_name'] . '</dt><dd>';
            foreach ($item['tags'] as $tag) {
                $tagStr .= '<a href="' . $tag['url'] . '">' . $tag['name'] . '</a>';
            }
            $tagStr .= '</dd></dt></dl>';
        }

        $tagStr .= '<dt>其他</dt><dd>';

        foreach ($tagsArray as $tag) {
            $tagStr .= '<a href="' . '/e/tags/?tagid=' . $tag['tagid'] . '">' . $tag['tagname'] . '</a>';
        }

        $tagStr .= '</dd></dt></dl>';

        $domain = Yii::$app->request->get('domain');
        $path = '/www/wwwroot/' . $domain . '/tags.html';

        $res = file_get_contents($path);
        //复位
        $res = preg_replace('@<div id="content">(.*)?</div>@s', '<div id="content"></div>', $res);
        $str = str_replace('<div id="content"></div>', '<div id="content">' . $tagStr . '</div>', $res);
        file_put_contents($path, $str);

        $path2 = '/www/wwwroot/' . $domain . '/m/tags.html';
        $res2 = file_get_contents($path2);
        //复位
        $res2 = preg_replace('@<div class="tags">(.*)?</div>@s', '<div class="tags"></div>', $res2);
        $str2 = str_replace('<div class="tags"></div>', '<div class="tags">' . $tagStr . '</div>', $res2);
        file_put_contents($path2, $str2);

        exit;
    }
}