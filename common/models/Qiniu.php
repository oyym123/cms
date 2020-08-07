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

    /** 获取路径 */
    public function getPath($bucket, $column = 'wordImg/')
    {
        switch ($bucket) {
            case Yii::$app->params['QiNiuBucketStatic']:
                return ['', Yii::$app->params['QiNiuHostStatic']];
                break;
            case Yii::$app->params['QiNiuBucketImg']:
                return [$column, Yii::$app->params['QiNiuHost'] . $column];
                break;
        }
    }

    /** 七牛云上传 */
    public function fileUpload($name, $bucket = 'aks-img01', $from = 0, $clean = 0, $imgName = 'title_img')
    {
        if ($clean == 1) {
            $fileInfo = (new UploadForm())->cleanInfo($_FILES[$name], $imgName);
        } else {
            $fileInfo = $_FILES[$name];
        }

        header('content-type:text/html;charset=utf-8');

        $allowExt = ['jpeg', 'jpg', 'png', 'gif', 'js', 'css', 'ttf'];
        $path = (new UploadForm())->moveFile($fileInfo, './img_tmp', false, $allowExt);
        $newName = str_replace('./img_tmp/', '', $path);

        //初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        $token = $this->upToken($bucket);

        list($column, $qNPath) = $this->getPath($bucket);

        //调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $column . $newName, $path);

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
                    'url' => $qNPath . $newName
                ];
            } else {
                echo json_encode([
                    'state' => 'SUCCESS',
                    'url' => $qNPath . $newName
                ]);
                exit;
            }
        }
    }

    /** 直接抓取远程文件到七牛云 */
    public function fetchFile($url, $bucket, $key)
    {
        $uploadMgr = new BucketManager($this->auth);
        try {
            list($ret, $error) = $uploadMgr->fetch($url, $bucket, 'wordImg/' . $key);
            if ($error !== null) {
                return [-1, $error];
            } else {
                return [1, Yii::$app->params['QiNiuHost'] . 'wordImg/' . $key];
            }
        } catch (\Exception $e) {
            return [-1, $e->getMessage()];
        }
    }
}