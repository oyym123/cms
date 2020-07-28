<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "article_way".
 *
 * @property int $id
 * @property string|null $name 方法名称
 * @property int|null $sort 排序
 * @property string|null $function_name 代码中的方法名称
 * @property int|null $user_id
 * @property int|null $status 10=正常 20=禁用
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class ArticleWay extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_way';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort', 'user_id', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'function_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'sort' => '排序',
            'function_name' => '代码中的方法名称',
            'user_id' => '创建者',
            'status' => '状态',
            'updated_at' => '修改时间',
            'created_at' => '创建时间',
        ];
    }

    //获取键值对
    public static function getWayName()
    {
        $dbs = self::find()->asArray()->all();
        return ArrayHelper::map($dbs, 'id', 'name');
    }
}
