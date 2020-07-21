<?php


namespace backend\controllers;

use common\models\Qiniu;
use Yii;
use yii\web\Controller;

class UeditorController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $action = Yii::$app->request->get('action');
        if ($action == 'config') {
            return $this->getConfig();
        }

        if ($action == 'getToken') {
            $res = (new Qiniu())->upToken('aks-img01');
            return json_encode([
                'state' => 'SUCCESS',
                'token' => $res
            ]);
        }

        if ($action == 'uploadimage') {
            $res = (new Qiniu())->fileUpload('file');
        }

        if ($action == 'listimage') {
            return json_encode([
                'state' => 'SUCCESS',
                'list' => ['http://img.thszxxdyw.org.cn/wordImg/08c86f81406d074e59f2eae5c1a9b4a6.jpg'],
                'start' => 0,
                'total' => 1
            ]);
        }
    }

    public function getConfig()
    {
        return file_get_contents(__DIR__ . '/../../common/widgets/ueditor/vendor/php/config.json');
    }

}