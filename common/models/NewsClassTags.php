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
        $domain = Yii::$app->request->get('domain');
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
                'class_path' => $cl->classpath,
                'tags' => $tagArr
            ];
        }

        $tagStr = '';
        $tagsArray = NewsTags::find()->where(['not in', 'tagid', $tagsIds])->asArray()->all();

        foreach ($data as $item) {
            $tagStr .= '<dl><dt><a href="/' . $item['class_path'] . '" target="_blank">' . $item['class_name'] . '</a></dt><dd>';
            foreach ($item['tags'] as $tag) {
                $tagStr .= '<a href="' . $tag['url'] . '">' . $tag['name'] . '</a>';
            }
            $tagStr .= '</dd></dt></dl>';
        }

        $tagStr .= '<dl><dt>其他</dt><dd>';

        foreach ($tagsArray as $tag) {
            $tagStr .= '<a href="' . '/e/tags/?tagid=' . $tag['tagid'] . '">' . $tag['tagname'] . '</a>';
        }

        $tagStr .= '</dd></dl>';


        $path = '/www/wwwroot/' . $domain . '/tags.html';

        $res = file_get_contents($path);
        //复位
        $res = preg_replace('@<div id="content">(.*)?</div>@s', '<div id="content"></div>', $res);
        $str = str_replace('<div id="content"></div>', '<div id="content">' . $tagStr . '</div>', $res);
        file_put_contents($path, $str);

        $path2 = '/www/wwwroot/' . $domain . '/m/tags.html';
        $res2 = file_get_contents($path2);
        //复位
        $res2 = preg_replace('@<div id="content">(.*)?</div>@s', '<div id="content"></div>', $res2);
        $str2 = str_replace('<div id="content"></div>', '<div id="content">' . $tagStr . '</div>', $res2);
        file_put_contents($path2, $str2);

        echo '<h1 style="color: green">标签更新成功</h1>';
        echo '<a href="/"><h2>点击返回</h2></a>';
        exit;
    }

    /** 将tags插入到选中的数据库中 */
    public static function createOne($data)
    {
        //保存tags索引表
        $model = new NewsClassTags();
        $model->tagid = $data['tagid'];
        $model->classid = $data['classid'];
        $model->id = $data['id'];
        $model->newstime = time();
        $model->mid = 1;
        if (!$model->save()) {
            return [-1, $model->getErrors()];
        }
    }
}