<?php


namespace common\models;

use Yii;

class NewsInfoIndex extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     * cms文章
     */
    public static function tableName()
    {
        return 'phome_ecms_news_index';
    }

    public static function getDb()
    {
        return Yii::$app->get('db2');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

        ];
    }

    /** 将数据库中的文章插入到 选中的数据库中 */
    public static function createOne($data)
    {
        if (isset($data['flag'])) {
            $randTime = Tools::randomDate('20200501', '', false);
        } else {
            $randTime = time();
        }

        $model = new NewsInfoIndex();
        $model->classid = $data['db_class_id'];
        $model->checked = 1;
        $model->newstime = $randTime;
        $model->truetime = $randTime;
        $model->lastdotime = $randTime;
        $model->havehtml = 1;
        if (!$model->save()) {
            return [-1, $model->getErrors()];
        }
    }
}