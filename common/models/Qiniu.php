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
    public function upToken($bucket, $policy = null)
    {
        $upToken = $this->auth->uploadToken($bucket, null, 3600, $policy);
        return $upToken;
    }

    /** 七牛云上传 */
    public function fileUpload($name, $bucket = 'aks-img01', $from = 0, $clean = 0)
    {
        if ($clean == 1) {
            $fileInfo = (new UploadForm())->cleanInfo($_FILES[$name], 'title_img');
        } else {
            $fileInfo = $_FILES[$name];
        }

        header('content-type:text/html;charset=utf-8');

        $allowExt = ['jpeg', 'jpg', 'png', 'gif'];
        $path = (new UploadForm())->moveFile($fileInfo, './img_tmp', false, $allowExt);
        $newName = str_replace('./img_tmp/', '', $path);

        //初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        $token = $this->upToken($bucket);
        //调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, 'wordImg/' . $newName, $path);

        if ($err !== null) {
            echo $newName;
            print_r($err);
            exit;
        } else {
            //传完七牛云删除临时图片
            unlink($path);
            if ($from == 1) {
                return [
                    'state' => 'SUCCESS',
                    'url' => Yii::$app->params['QiNiuHost'] . 'wordImg/' . $newName
                ];
            } else {
                echo json_encode([
                    'state' => 'SUCCESS',
                    'url' => Yii::$app->params['QiNiuHost'] . 'wordImg/' . $newName
                ]);
                exit;
            }
        }
    }

    /** 直接抓取远程文件到七牛云 */
    public function fetchFile($url, $bucket, $key)
    {
        $uploadMgr = new BucketManager($this->auth);
        list($ret, $error) = $uploadMgr->fetch($url, $bucket, 'wordImg/' . $key);
        if ($error !== null) {
            return [-1, $error];
        } else {
            return [1, Yii::$app->params['QiNiuHost'] . 'wordImg/' . $key];
        }
    }
}