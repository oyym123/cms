<?php


namespace console\controllers;


use common\models\BaiduKeywords;
use common\models\Tools;
use common\models\WebDriver;
use common\models\ZuoWenWang;

class CatchDataController extends \yii\console\Controller
{
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
        $keyword->catch_status = BaiduKeywords::CATCH_STATUS_OVER;
        $keyword->save();

        Tools::writeLog(['开始抓取：' => $keyword->keywords], 'sgwx.txt');

        for ($i = 1; $i <= 10; $i++) {
            list($code, $msg) = WebDriver::getSgwx($keyword->keywords, $i, $keyword->id);
            if ($code < 0) {
                $errors[] = $msg;
            }
        }

        $this->actionSgdata();
        exit;
    }

    /** 触发爬虫 */
    public function actionStartCatch()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/index.php?r=catch-data/sgdata';
        Tools::curlGet($url);
    }

    /** 作文网数据爬取 & 翻译 */
    public function actionZww()
    {
        (new ZuoWenWang())->saveData();
    }

    public function actionCatchZw()
    {
        (new ZuoWenWang())->catchData();
    }
}