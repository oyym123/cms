<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "black_article".
 *
 * @property int $id
 * @property string|null $title 标题 
 * @property int|null $type 1=文档导入 2=其他途径
 * @property int|null $type_id 来源id
 * @property int|null $key_id 关键词id
 * @property string|null $keywords 关键词
 * @property string|null $cut_word 切词
 * @property string|null $image_urls 图片地址 json格式
 * @property string|null $from_path 来路地址
 * @property int|null $word_count 文章字数
 * @property string|null $part_content 文章分段 json格式
 * @property string|null $content 内容
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class BlackArticle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'black_article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'type', 'type_id', 'key_id', 'word_count'], 'integer'],
            [['image_urls', 'part_content', 'content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'keywords', 'cut_word', 'from_path'], 'string', 'max' => 255],
            [['title'], 'unique'],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'type' => 'Type',
            'type_id' => 'Type ID',
            'key_id' => 'Key ID',
            'keywords' => 'Keywords',
            'cut_word' => 'Cut Word',
            'image_urls' => 'Image Urls',
            'from_path' => 'From Path',
            'word_count' => 'Word Count',
            'part_content' => 'Part Content',
            'content' => 'Content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
