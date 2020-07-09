<?php


namespace common\models;

use Qiniu\Storage\UploadManager;
use Yii;
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use yii\web\UploadedFile;

class Qiniu
{
    public $auth;

    public function __construct()
    {
        $accessKey = Yii::$app->params['QiNiuAccessKey'];
        $secretKey = Yii::$app->params['QiNiuSecretKey'];
        //初始化Auth状态
        $this->auth = new Auth($accessKey, $secretKey);
    }

    /** 上传验证码 */
    public function upToken($bucket)
    {
        $upToken = $this->auth->uploadToken($bucket);
        return $upToken;
    }

    /** 七牛云上传 */
    public function fileUpload($name, $bucket = 'aks_img01')
    {

        //初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        $token = $this->upToken($bucket);

        //调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, 'tes998.jpeg', $_FILES[$name]['tmp_name']);

        echo '<pre>';

        if ($err !== null) {
            echo $_FILES[$name]['tmp_name'];
            print_r($err);
        } else {
            print_r($ret);
        }
    }
}