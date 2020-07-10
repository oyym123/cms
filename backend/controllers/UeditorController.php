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
            $policy = array(
                'callbackUrl' => 'http://172.30.251.210/callback.php',
                'callbackBody' => '{"fname":"$(fname)", "fkey":"$(key)", "desc":"$(x:desc)", "uid":' . $uid . '}'
            );

            $res = (new Qiniu())->upToken('aks-img01', $policy);
            return json_encode([
                'state' => 'SUCCESS',
                'token' => $res
            ]);
        }

        if ($action == 'uploadimage') {
            $res = (new Qiniu())->fileUpload('file');
        }
    }

    public function getConfig()
    {
        return file_get_contents(__DIR__ . '/../../common/widgets/ueditor/vendor/php/config.json');
    }

}