<?php


namespace frontend\controllers;


use common\models\Tools;
use yii\web\Controller;

class CatchDataController extends Controller
{
    public function actionIndex()
    {
        echo '<h1>欢迎来到 爬虫世界！</h1>';
    }


    /** 抓取搜狗微信数据 */
    public function actionSgwx()
    {
        $keywords = ['英语'];
        foreach ($keywords as $keyword) {
            $url = 'https://weixin.sogou.com/weixin?type=2&s_from=input&query=英语&ie=utf8&_sug_=y&_sug_type_=&w=01019900&sut=2456&sst0=1594457792745&lkt=1,1594457792644,1594457792644&page=' . '2';

            $res = Tools::curlGet($url);
            print_r($res);
        }
    }
}