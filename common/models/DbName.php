<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "db_name".
 *
 * @property int $id
 * @property string|null $name 数据库名称
 * @property int|null $status 0=禁用 1=正常
 * @property string|null $updated_at
 * @property string|null $created_at
 */
class DbName extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'db_name';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'status' => 'Status',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    //获取所有的数据库名称
    public static function getAll()
    {
        $dbs = self::find()->select('name')->where(['status' => 1])->asArray()->all();
        return array_column($dbs, 'name');
    }
    
}
