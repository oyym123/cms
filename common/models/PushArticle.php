<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "push_article".
 *
 * @property int $id
 * @property int|null $b_id 索引黑帽文章id
 * @property int|null $column_id 类目id
 * @property string|null $column_name 类名
 * @property int|null $rules_id 规则id
 * @property int|null $domain_id 域名id
 * @property string|null $domain 域名
 * @property string|null $from_path 来路地址
 * @property string|null $keywords 关键词
 * @property string|null $title_img 标题图片地址
 * @property int|null $status 10=状态有效 20=无效
 * @property string|null $content 内容
 * @property string|null $intro 文章简介
 * @property string|null $title 标题 
 * @property string|null $push_time 发布时间
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class PushArticle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'push_article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['b_id', 'column_id', 'rules_id', 'domain_id', 'status'], 'integer'],
            [['content'], 'string'],
            [['push_time', 'created_at', 'updated_at'], 'safe'],
            [['column_name', 'domain', 'from_path', 'keywords', 'title_img', 'intro', 'title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'b_id' => 'B ID',
            'column_id' => 'Column ID',
            'column_name' => 'Column Name',
            'rules_id' => 'Rules ID',
            'domain_id' => 'Domain ID',
            'domain' => 'Domain',
            'from_path' => 'From Path',
            'keywords' => 'Keywords',
            'title_img' => 'Title Img',
            'status' => 'Status',
            'content' => 'Content',
            'intro' => 'Intro',
            'title' => 'Title',
            'push_time' => 'Push Time',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
