<?php

namespace console\controllers;

use common\models\BaiduKeywords;
use common\models\BlackArticle;
use common\models\DbName;
use common\models\FanUser;
use common\models\LongKeywords;
use common\models\Tools;

class CmsController extends \yii\console\Controller
{
    /**
     *开始跑所有数据库
     * http://yii.com/index.php?r=cms/start-run
     */
    public function actionStartRun()
    {
        //每分钟检测执行一次
        $res = DbName::find()->all();
        $arr = [];
        //遍历每个数据库，推送
        foreach ($res as $re) {
            //定时不为空
            if (!empty($re->mip_time)) {
                $date = date('Y-m-d', time());
                $time = $date . ' ' . $re->mip_time . ':00';
                $limitTime = strtotime($time) - time();

                //当到达执行时间时，开始执行
                if ($limitTime < 90 && $limitTime > 0) { //表示执行
                    print_r($limitTime);
                    Tools::writeLog($re->name . '已执行');
                    $url = 'http://' . $_SERVER['SERVER_ADDR'] . ':89/index.php?r=cms&db_name=' . $re->name;
                    $arr[] = $url;
                    Tools::curlGet($url);
                } else {
                    echo '当前时间:' . time();
                    echo '  时间差:' . $limitTime;
                    echo PHP_EOL;
                    echo $re->name . '  执行时间：' . $time;
                    echo PHP_EOL;
                }
            }
        }
        print_r($arr);
    }

    /**
     * 设置标签页
     */
    public function actionSetTags()
    {
        //每天检测执行一次
        $res = DbName::find()->all();
        $arr = [];
        //遍历每个数据库，推送
        foreach ($res as $re) {
            $domain = str_replace('m.', '', $re->domain);
            $url = 'http://' . $_SERVER['SERVER_ADDR'] . ':89/index.php?r=cms/set-tags&db_name=' . $re->name . '&domain=' . $domain;
            $arr[] = $url;
            Tools::curlGet($url);
        }
        print_r($arr);
    }

    /**
     * 抓取百度关键词
     */
    public function actionCatchBd()
    {
        set_time_limit(0);
        (new BaiduKeywords())->getSdkWords();
    }

    /**
     * 抓取百度长尾词
     */
    public function actionCatchBaidu()
    {
        LongKeywords::pushReptile();
        LongKeywords::getKeywords();
    }

    /** 生成泛目录缓存 */
    public function actionCacheFan()
    {
        $url = 'https://www.ysjj.org.cn/?index.php&catch_web=1';
        Tools::curlGet($url);
    }

    /** 生成泛目录缓存 */
    public function actionPushFan()
    {
        $url = 'https://www.ysjj.org.cn/?index.php&push=1';
        Tools::curlGet($url);
    }

    /** 推送黑帽文章
     * cms/push-black-article
     */
    public function actionPushBlackArticle()
    {
        (new BlackArticle())->pushArticle();
    }

    public function actionCreateUser()
    {
        (new FanUser())->createMany();
    }
}