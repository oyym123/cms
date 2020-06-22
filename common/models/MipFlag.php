<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mip_flag".
 *
 * @property int $id
 * @property int|null $db_id 数据库id
 * @property string|null $db_name 数据库名称
 * @property int|null $type 1=文章 2=tag
 * @property int|null $type_id 类型id
 * @property int|null $status 0=禁用 1=正常
 * @property string|null $created_at
 * @property string|null $updated_at 创建时间
 */
class MipFlag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mip_flag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['db_id', 'type', 'type_id', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['db_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'db_id' => 'Db ID',
            'db_name' => 'Db Name',
            'type' => 'Type',
            'type_id' => 'Type ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
