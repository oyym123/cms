<?php


namespace common\models;

use Yii;

class NewsClass extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'phome_enewsclass';
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
     * 获取所有的文章分类
     */
    public function getAll()
    {
        return self::findAll();
    }
}