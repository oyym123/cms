<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "article_rules".
 *
 * @property int $id
 * @property string|null $name 规则名称
 * @property int|null $category_id 分类id
 * @property int|null $domain_id 域名id
 * @property int|null $column_id 目录id
 * @property string|null $method_ids 方法id集合
 * @property int|null $one_page_num_min 一篇文章最低拼接数量
 * @property int|null $one_page_num_max 一篇文章最高拼接数量
 * @property int|null $one_page_word_min 一篇文章最少字数
 * @property int|null $one_page_word_max 一篇文章最高字数
 * @property int|null $one_day_push_num 一天推送的文章数量
 * @property string|null $push_time_sm 推送给神马的时间
 * @property string|null $push_time_bd 推送给百度的时间
 * @property int|null $user_id 用户id
 * @property int|null $status 10=正常 20=禁用
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class ArticleRules extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_rules';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'domain_id', 'column_id', 'one_page_num_min', 'one_page_num_max', 'one_page_word_min', 'one_page_word_max', 'one_day_push_num', 'user_id', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'method_ids', 'push_time_sm', 'push_time_bd'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '规则名称',
            'category_id' => '分类id',
            'domain_id' => '域名id',
            'column_id' => '目录id',
            'method_ids' => '方法id集合',
            'one_page_num_min' => '一篇文章最低拼接数量',
            'one_page_num_max' => '一篇文章最高拼接数量',
            'one_page_word_min' => '一篇文章最少字数',
            'one_page_word_max' => '一篇文章最高字数',
            'one_day_push_num' => '一天推送的文章数量',
            'push_time_sm' => '推送给神马的时间',
            'push_time_bd' => '推送给百度的时间',
            'user_id' => '用户id',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
