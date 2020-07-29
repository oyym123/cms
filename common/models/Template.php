<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

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
class Template extends Base
{

    const TYPE_HOME = 1;            //首页
    const TYPE_LIST = 2;            //列表页
    const TYPE_DETAIL = 3;          //详情页
    const TYPE_TAGS = 4;          //详情页
    const TYPE_CUSTOMIZE = 5;       //自定义页面

    /** 获取所有的类型 */
    public static function getType($key = 'all')
    {
        $data = [
            self::TYPE_HOME => '首页',
            self::TYPE_LIST => '列表页',
            self::TYPE_DETAIL => '详情页',
            self::TYPE_TAGS => '标签页',
            self::TYPE_CUSTOMIZE => '自定义页面',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

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
            'content' => '网页内容【smarty  | php渲染】',
            'type' => '类型',
            'en_name' => '唯一英文名称',
            'intro' => '简介',
            'status' => '状态',
            'user_id' => '创建者',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /** 根据类型获取模板 */
    public static function getTemplate($type)
    {
        $dbs = self::find()->where([
            'status' => self::STATUS_BASE_NORMAL,
            'type' => $type,
        ])->asArray()->all();
        return ArrayHelper::map($dbs, 'id', 'name');
    }

    /** 按照域名生成模板 */
    public function setTmp()
    {
        
    }
}
