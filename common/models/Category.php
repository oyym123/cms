<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string|null $name 分类名称
 * @property string|null $en_name 英文名称
 * @property string|null $intro 描述
 * @property int|null $pid 上级id
 * @property int|null $level 等级
 * @property int|null $user_id
 * @property int|null $status 10=正常 20=禁用
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pid', 'level', 'user_id', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'en_name', 'intro'], 'string', 'max' => 255],
            [['en_name'], 'unique'],
            [['name'], 'unique'],
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
            'en_name' => '英文名称',
            'intro' => '介绍',
            'pid' => '父类id',
            'level' => '等级',
            'user_id' => '创建者',
            'status' => '状态',
            'updated_at' => '修改时间',
            'created_at' => '创建时间',
        ];
    }
}
