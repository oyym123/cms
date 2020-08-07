<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "template_tpl".
 *
 * @property int $id
 * @property int|null $cate 10=PC 20=mobile
 * @property string|null $t_customize 自定义模板id
 * @property int|null $t_tags 标签页模板id
 * @property int|null $t_detail 详情页模板id
 * @property int|null $t_list 列表页模板id
 * @property int|null $t_common 公共模板
 * @property int|null $t_home 首页模板id
 * @property int|null $type 模板类型
 * @property string|null $t_ids 模板id集合
 * @property int|null $status 10=正常  20=禁用
 * @property int|null $user_id 创建人id
 * @property string|null $created_at 创建时间
 * @property string|null $updated_at 修改时间
 */
class TemplateTpl extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'template_tpl';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 't_home', 't_common', 't_detail', 't_tags', 't_list', 'cate','t_inside'], 'required'],
            [['cate', 't_tags', 't_detail', 't_list', 't_common', 't_home', 'type', 'status', 'user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['t_ids','name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'domain_id' => '域名',
            'name' => '名称',
            'column_id' => '分类',
            't_inside' => '泛内页',
            'template_id' => '模板',
            't_home' => '首页',
            't_detail' => '详情页',
            't_tags' => '标签页',
            't_customize' => '自定义页面',
            't_list' => '列表页',
            't_common' => '公共页面页',
            'type' => '网页类型',
            'cate' => '类型',
            'status' => '状态',
            'user_id' => '创建者',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /** 根据类型获取模板 */
    public static function getTpl()
    {
        $dbs = self::find()->where([
            'status' => self::STATUS_BASE_NORMAL,

        ])->asArray()->all();
        return ArrayHelper::map($dbs, 'id', 'name');
    }

}
