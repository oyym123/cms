<?php


namespace common\models;


class BlackHat extends \yii\db\ActiveRecord
{
    /**  相关文章 */
    public function relatedArticle()
    {
        
    }

    /** 热门文章 */
    public static function popularArticle()
    {
        //搜索文章
        $article = BlackArticle::find()
            ->select('title,title_img,created_at')
            ->where([
                'type' => BlackArticle::TYPE_ZUO_WEN_WANG
            ])->limit(2)->asArray()->all();
        $data = [];
        foreach ($article as $key => $item) {
            $data[$key] = $item;
        }
        return $data;
    }

}