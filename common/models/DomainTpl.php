<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "domain_tpl".
 *
 * @property int $id
 * @property int|null $domain_id
 * @property int|null $template_id
 * @property int|null $column_id 分类id
 * @property int|null $type 模板类型
 * @property int|null $status 10=正常  20=禁用
 * @property int|null $user_id 创建人id
 * @property string|null $created_at 创建时间
 * @property string|null $updated_at 修改时间
 */
class DomainTpl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'domain_tpl';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['domain_id', 'template_id', 'column_id', 'type', 'status', 'user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'domain_id' => 'Domain ID',
            'template_id' => 'Template ID',
            'column_id' => 'Column ID',
            'type' => 'Type',
            'status' => 'Status',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

//    /** 通过当前域名获取对应的模板 */
//    public static function getOne()
//    {
//
//
//    }
}