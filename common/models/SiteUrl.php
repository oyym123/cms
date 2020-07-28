<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "site_url".
 *
 * @property int $id
 * @property string|null $url 网址
 * @property int|null $type 类型
 * @property string|null $intro
 * @property int|null $status 10=正常 20=已经爬取完毕 30=已作废
 * @property string|null $tags 标签
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class SiteUrl extends \yii\db\ActiveRecord
{
    const STATUS_NORMAL = 10;
    const STATUS_DONE = 20;
    const STATUS_DISABLE = 30;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'site_url';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'status'], 'integer'],
            [['intro'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['url', 'tags'], 'string', 'max' => 255],
            [['url'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => 'Url',
            'type' => 'Type',
            'intro' => 'Intro',
            'status' => 'Status',
            'tags' => 'Tags',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /** 创建一个网址 */
    public static function createOne($data)
    {
        $model = new SiteUrl();
        $model->url = $data['url'];
        $model->type = $data['type'];
        $model->intro = $data['intro'] ?? '';
        $model->status = self::STATUS_NORMAL;
        $model->tags = $data['tags'] ?? '';
        $model->created_at = date('Y-m-d H:i:s');

        if (!$model->save()) {
            return [-1, $model->getErrors()];
        }

        return [1, 'success'];
    }
}
