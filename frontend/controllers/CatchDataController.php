<?php


namespace frontend\controllers;

use common\models\BaiduKeywords;
use common\models\Tools;
use common\models\ZuoWenWang;
use Yii;
use common\models\WebDriver;
use common\models\WhiteArticle;
use yii\web\Controller;

class CatchDataController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        echo '<h1>欢迎来到 爬虫世界！</h1>';
    }

    /** webdriver 工具爬取数据 */
    public function actionSgdata()
    {
        set_time_limit(0);

        //查询所有的可用关键词
        $keyword = BaiduKeywords::find()->select('catch_status,id,keywords,m_pv')
//            ->where(['like', 'keywords', '英语'])
//            ->andWhere(['between', 'm_pv', 10, 10000])
            ->andWhere(['catch_status' => BaiduKeywords::CATCH_STATUS_START])
//            ->asArray()
            ->orderBy('m_pv desc')
            ->one();

        $errors = [];


        //标记关键词已被爬取完毕 后续不再被爬取
//        $keyword->catch_status = BaiduKeywords::CATCH_STATUS_OVER;
//        $keyword->save();

        Tools::writeLog(['开始抓取：' => $keyword->keywords]);

        for ($i = 1; $i <= 10; $i++) {
            list($code, $msg) = WebDriver::getSgwx($keyword->keywords, $i, $keyword->id);
            if ($code < 0) {
                $errors[] = $msg;
            }
        }

        $this->actionStartCatch();
        exit;
    }

    /** 触发爬虫 */
    public function actionStartCatch()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/index.php?r=catch-data/sgdata';
        Tools::curlGet($url);
    }

    /**
     * @OA\Get(
     *     path="/upload/article",
     *     summary="内容上传接口 【爬虫】",
     *     tags={"内容"},
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
    public function actionUploadArticle()
    {
        list($code, $msg) = WhiteArticle::createOne(Yii::$app->request->post());
        if ($code < 0) {
            exit($msg);
        } else {
            exit('success ' . $msg->id);
        }
    }

    /** 作文网数据爬取 & 翻译 */
    public function actionZww()
    {
        (new ZuoWenWang())->catchData();
    }
}