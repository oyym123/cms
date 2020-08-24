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
    const CATE_PC = 10;            //PC 端
    const CATE_MOBILE = 20;        //MOBILE 端

    const TYPE_HOME = 1;            //首页
    const TYPE_LIST = 2;            //列表页
    const TYPE_DETAIL = 3;          //详情页
    const TYPE_TAGS = 4;            //标签页
    const TYPE_CUSTOMIZE = 5;       //自定义页面
    const TYPE_COMMON = 6;          //公共页面模板
    const TYPE_INSIDE = 7;          //泛内页模板

    /** 获取所有的类型 */
    public static function getType($key = 'all')
    {
        $data = [
            self::TYPE_HOME => '首页',
            self::TYPE_LIST => '列表页',
            self::TYPE_DETAIL => '详情页',
            self::TYPE_TAGS => '标签页',
            self::TYPE_CUSTOMIZE => '自定义页面',
            self::TYPE_COMMON => '公共页面模板',
            self::TYPE_INSIDE => '泛内页模板',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    /** 获取所有的类型 */
    public static function getEnType($key = 'all')
    {
        $data = [
            self::TYPE_HOME => 'home',
            self::TYPE_LIST => 'list',
            self::TYPE_DETAIL => 'detail',
            self::TYPE_TAGS => 'tags',
            self::TYPE_CUSTOMIZE => 'customize',
            self::TYPE_COMMON => 'common',
            self::TYPE_INSIDE => 'inside',
        ];
        return $key === 'all' ? $data : $data[$key];
    }


    /** 获取所有的类别 */
    public static function getCate($key = 'all')
    {
        $data = [
            self::CATE_PC => 'PC端',
            self::CATE_MOBILE => '移动端',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    public static function getTmpIndex($key = 'all')
    {
        $data = [
            Template::TYPE_HOME => 'index.php',
            Template::TYPE_LIST => 'list.php',
            Template::TYPE_DETAIL => 'detail.php',
            Template::TYPE_TAGS => 'tags.php',
            Template::TYPE_INSIDE => 'inside.php',
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
            [['id', 'cate', 'type', 'content'], 'required'],
            [['content', 'intro', 'img'], 'string'],
            [['type', 'status', 'user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'en_name', 'img'], 'string', 'max' => 255],
            [['php_func'], 'string', 'max' => 255000],
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
            'content' => '网页内容【php渲染】',
            'type' => '网页类型',
            'img' => '网页截图',
            'cate' => '类型',
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
}
