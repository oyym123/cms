<?php


namespace common\models;

use Yii;

class NewsInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     * cms文章
     */
    public static function tableName()
    {
        return 'phome_ecms_news';
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

        $model = new NewsInfo();
        $model->classid = $data['classid'];
        $model->filename = $data['filename'];
        $model->userid = 1;
        $model->username = 'heshao';
        $model->ispic = 1;
        $model->truetime = $randTime;
        $model->lastdotime = $randTime;
        $model->havehtml = 1;
        $model->titleurl = $data['titleurl'];
        $model->stb = 1;
        $model->fstb = 1;
        $model->restb = 1;
        $model->keyboard = $data['keyboard'];
        $model->title = $data['title'];
        $model->newstime = $randTime;
        $model->titlepic = $data['titlepic'];
        $model->ftitle = $data['ftitle'];
        $model->smalltext = $data['smalltext'];
        if (!$model->save()) {
            return [-1, $model->getErrors()];
        }
    }
}