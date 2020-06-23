<?php

namespace common\models;

use Yii;
use yii\debug\models\search\Db;

/**
 * This is the model class for table "db_log".
 *
 * @property int $id
 * @property string|null $db_name 数据库名称
 * @property int|null $type 1=推送日志 2=
 * @property int|null $db_id 数据库id
 * @property string|null $intro 内容
 * @property string|null $created_at
 */
class DbLog extends \yii\db\ActiveRecord
{

    const TYPE_TAG = 1;
    const TYPE_TAG_FAST = 2;
    const TYPE_ARTICLE = 3;
    const TYPE_ARTICLE_FAST = 4;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'db_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'db_id'], 'integer'],
            [['intro'], 'string'],
            [['created_at'], 'safe'],
            [['db_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'db_name' => '数据库名称',
            'type' => '类型',
            'db_id' => '数据库id',
            'intro' => '内容',
            'created_at' => '创建时间',
        ];
    }

    /** 创建一个日志 */
    public static function createOne($data)
    {
        $model = new DbLog();
        $model->db_name = $data['db_name'];
        $model->type = $data['type'];
        $model->db_id = $data['db_id'];
        $model->intro = $data['intro'];
        $model->created_at = date('Y-m-d H:i:s');
        if (!$model->save()) {
            return $model->getErrors();
        }
    }
}
