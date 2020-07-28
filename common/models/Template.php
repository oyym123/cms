<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "template".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $content
 * @property int|null $type 1=首页 2=类目列表  3=详情 4=自定义
 * @property string|null $en_name 英文名
 * @property string|null $intro 简介
 * @property int|null $status 10=正常  20=禁用
 * @property int|null $user_id 创建人id
 * @property string|null $created_at 创建时间
 * @property string|null $updated_at 修改时间
 */
class Template extends \yii\db\ActiveRecord
{

    const TYPE_HOME = 1;            //首页
    const TYPE_LIST = 2;            //列表页
    const TYPE_DETAIL = 3;          //详情页
    const TYPE_CUSTOMIZE = 4;       //自定义页面

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content', 'intro'], 'string'],
            [['type', 'status', 'user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'en_name'], 'string', 'max' => 255],
            [['en_name'], 'unique'],
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
            'content' => '网页内容',
            'type' => '类型',
            'en_name' => '唯一英文名称',
            'intro' => '简介',
            'status' => '状态',
            'user_id' => '创建者',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
