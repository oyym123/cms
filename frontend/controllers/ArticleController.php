<?php


namespace frontend\controllers;


use common\models\WhiteArticle;

class ArticleController extends BaseController
{

    public $enableCsrfValidation = false;

    /**
     * @OA\Post(
     *     path="/article/push",
     *     summary="内容上传接口 【爬虫】",
     *     tags={"内容"},
     *     description="展示模板参数 OYYM 2020/7/30 18:35",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="标题【不可超过30字】",
     *                     property="title",
     *                     type="string",
     *                     format="query",
     *
     *                     default="测试文章标题"
     *                 ),
     *                @OA\Property(
     *                     description="简介【不可超过200字】",
     *                     property="intro",
     *                     type="string",
     *                     format="query",
     *                     default="测试文章简介",
     *                 ),
     *                @OA\Property(
     *                     description="关键词表名",
     *                     property="key_table",
     *                     type="string",
     *                     format="query",
     *                     default="keywords_pro",
     *                 ),
     *               @OA\Property(
     *                     description="关键词id",
     *                     property="key_id",
     *                     type="integer",
     *                     format="query",
     *                     default="key_id",
     *                 ),
     *               @OA\Property(
     *                     description="关键词名称【不可超过15字】",
     *                     property="keywords",
     *                     type="string",
     *                     format="query",
     *                     default="建房用地",
     *                 ),
     *               @OA\Property(
     *                     description="文章内容 【文章内容要求字数大于150字】",
     *                     property="content",
     *                     type="string",
     *                     format="query",
     *                     example="耕地是我国最宝贵的资源，耕地保护是关乎14亿人吃饭的大事，容不得半点闪失。但是，近年来一些农村未经批准违法乱占耕地建房，问题突出且呈蔓延势头，触碰了耕地保护红线，威胁国家粮食安全。。。。"
     *                 ),
     *                @OA\Property(
     *                     description="来路网址",
     *                     property="from_path",
     *                     type="integer",
     *                     format="query",
     *                     default="https://news.163.com/20/0805/11/FJ8UQRNV000189FH.html",
     *                 ),
     *             )
     *         )
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="返回码",
     *     @OA\JsonContent( type="json", example=
     *     {
     *       "title_img": "标题图片",
     *       "title": "标题",
     *       "intro": "简介",
     *       "push_time": "发布时间",
     *     }
     *     )
     *   ),
     * )
     */
    public function actionPush()
    {
        list($code, $msg) = WhiteArticle::createOne(\Yii::$app->request->post());
        if ($code < 0) {
            exit($msg);
        } else {
            exit('success ' . $msg->id);
        }
    }
}