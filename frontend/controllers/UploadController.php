<?php


namespace frontend\controllers;


use common\models\Qiniu;
use common\models\UploadForm;

class UploadController extends BaseController
{
    public $enableCsrfValidation = false;

    /**
     * @OA\Post(
     *     path="/upload/img",
     *     summary="图片上传接口",
     *     description="OYYM 2020/8/4 18:20",
     *     operationId="uploadFile",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="上传资源",
     *                     property="file",
     *                     type="file",
     *                     format="file",
     *                 ),
     *                 required={"file"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="successful operation",
     *     ),
     *     tags={
     *         "资源上传"
     *     }
     * )
     * */
    public function actionImg()
    {
        $imgInfo = (new Qiniu())->fileUpload('file', \Yii::$app->params['QiNiuBucketImg'], 1, 0, 'file');
        echo "<pre>";
        print_r($imgInfo);
    }

    /**
     * @OA\Post(
     *     path="/upload/others",
     *     summary="上传其他资源 【css、js、ttf...】",
     *     description="OYYM 2020/8/4 18:20",
     *     operationId="uploadFile",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="上传资源",
     *                     property="file",
     *                     type="file",
     *                     format="file",
     *                 ),
     *                 required={"file"}
     *             ),
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="数据展示",
     *                     property="debug",
     *                     type="string",
     *                     format="query",
     *                     default="1"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="successful operation",
     *     ),
     *     tags={
     *         "资源上传"
     *     }
     * )
     * */
    public function actionOthers()
    {
        $staticInfo = (new Qiniu())->fileUpload('file', \Yii::$app->params['QiNiuBucketStatic'], 1, 0, 'file');
        echo "<pre>";
        print_r($staticInfo);
//        self::showMsg($staticInfo);
    }
}