<?php

namespace frontend\controllers;

use common\models\BaiduKeywords;
use common\models\BlackArticle;
use common\models\Domain;
use common\models\DomainColumn;
use common\models\Fan;
use common\models\PushArticle;
use common\models\Template;
use common\models\Tools;
use yii\data\Pagination;
use yii\helpers\FileHelper;
use yii\web\Controller;
use Yii;

class FanController extends Controller
{
    /**
     * @OA\Get(
     *   path="/fan/detail",
     *   summary="网页详情 【前端】",
     *   tags={"网页"},
     *   description="展示模板参数 OYYM 2020/7/30 18:29",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="页面id",
     *     @OA\Schema(
     *        type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="返回码",
     *     @OA\JsonContent( type="json", example=
     *     {
     *       "title_img": "标题图片",
     *       "content": "内容",
     *       "title": "标题",
     *       "intro": "简介",
     *       "push_time": "发布时间",
     *     }
     *     )
     *   )
     * )
     */
    public function actionDetail()
    {
        $url = Yii::$app->request->url;
        if (preg_match('/\d+/', $url, $arr)) { //获取id
            $model = PushArticle::find()->select('title_img,content,title,intro,push_time')->where(['id' => $arr[0]])->asArray()->one();
            list($layout, $render) = Fan::renderView(Template::TYPE_DETAIL);
            $this->layout = $layout;
            $column = explode('/', $url)[1];
            $res = [
                'data' => $model,
                'pre' => '/' . $column . '/' . ($arr[0] - 1) . '.html',
                'next' => '/' . $column . '/' . ($arr[0] + 1) . '.html',
            ];
            return $this->render($render, ['models' => $res]);
        }
    }

    /**
     * @OA\Get(
     *     path="/fan/index",
     *     summary="列表页 【前端】",
     *     tags={"网页"},
     *     description="展示模板参数 OYYM 2020/7/30 18:35",
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
    public function actionIndex()
    {

        //url转换 分页
        $url = Yii::$app->request->url;
        if (strpos($url, 'index_') && preg_match('/\d+/', $url, $arr)) {
            $_GET['page'] = $arr[0];
        }

        $query = PushArticle::find()->select('id,title_img,title,intro,push_time')->limit(10)->orderBy('RAND()');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()->all();

        list($layout, $render) = Fan::renderView(Template::TYPE_LIST);
        $this->layout = $layout;

        foreach ($models as &$item) {
            $item['url'] = '/wen/' . $item['id'] . '.html';
        }

//        print_r( $this->layout );exit;
        $res = [
            'home_list' => $models,
        ];

        return $this->render($render, [
            'models' => $res,
            'pages' => $pages,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/fan/tags",
     *     summary="标签页 【前端】",
     *     tags={"网页"},
     *     description="展示模板参数 OYYM 2020/7/30 18:35",
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
    public function actionTagsList()
    {
        //url转换 分页
        $url = Yii::$app->request->url;
        if (strpos($url, 'index_') && preg_match('/\d+/', $url, $arr)) {
            $_GET['page'] = $arr[0];
        }

        $query = BaiduKeywords::find()->select('id,keywords')->limit(10);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => '120']);

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()->all();

        $domain = Domain::getDomainInfo();

        if ($domain) {
            foreach ($models as &$item) {
                $item['url'] = '/' . $domain->start_tags . $item['id'] . $domain->end_tags;
            }
        }

        $res = [
            'home_list' => $models
        ];

        list($layout, $render) = Fan::renderView(Template::TYPE_TAGS);
        $this->layout = $layout;

        return $this->render($render, [
            'column' => DomainColumn::getColumn(),
            'models' => $res,
            'pages' => $pages,
        ]);
    }

    public function actionTagsDetail()
    {
        $url = Yii::$app->request->url;
        if (preg_match('/\d+/', $url, $arr)) { //获取id
            $model = PushArticle::find()->select('id,title_img,content,title,intro,push_time')->where(['id' => 1])->asArray()->one();
            list($layout, $render) = Fan::renderView(Template::TYPE_INSIDE);
            $this->layout = $layout;
            $model['url'] = '/wen/' . $model['id'] . '.html';
            $res = [
                'data' => $model
            ];
            return $this->render($render, ['models' => $res]);
        }
    }
}