<?php

namespace frontend\controllers;

use common\models\BaiduKeywords;
use common\models\BaiDuSdk;
use common\models\CmsAction;
use common\models\DbName;
use common\models\DirCatch;
use common\models\Keywords;
use common\models\LongKeywords;
use common\models\NewsClassTags;
use common\models\NewsTags;
use common\models\Tools;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class CmsController extends Controller
{
    /**
     * Displays homepage.
     * http://yii.com/index.php?r=cms
     * @return mixed
     */
    public function actionIndex()
    {
        $tag = new NewsTags();
        list($code, $msg) = $tag->result();
//        list($code, $msg) = $tag->result2();

        $model = new  CmsAction();
        list($code, $msg) = $model->result();
//        list($code, $msg) = $model->result2();
    }

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
                if ($limitTime < 63 && $limitTime > 0) { //表示执行
                    print_r($limitTime);
                    Tools::writeLog($re->name . '已执行');
                    $url = 'http://116.193.169.122:89/index.php?r=cms&db_name=' . $re->name;
                    $arr[] = $url;
                    Tools::curlGet($url);
                } else {
                    echo '执行时间：' . $time;
                }
            }
        }

        echo '<pre>';
        print_r($arr);
    }

    /** 爬取关键词
     * http://116.193.169.122:89/index.php?r=cms/catch-key
     */
    public function actionCatchKey()
    {
        Keywords::catchKeyWords();
    }

    /**
     * tags页面数据展示
     *
     */
    public function actionSetTags()
    {
        NewsClassTags::setTags();
    }

    /**
     * 抓取百度长尾词
     */
    public function actionCatchBaidu()
    {
        LongKeywords::getKeywords();
    }

    /**
     * 抓取文章目录
     */
    public function actionDirCatch()
    {
        (new DirCatch())->catchArticle();
    }
    
}