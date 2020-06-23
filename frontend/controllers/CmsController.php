<?php

namespace frontend\controllers;

use common\models\CmsAction;
use common\models\DbName;
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
//        list($code, $msg) = $tag->result();
        list($code, $msg) = $tag->result2();
        exit;
        $model = new  CmsAction();
        list($code, $msg) = $model->result();
        list($code, $msg) = $model->result2();
    }

    /**
     *开始跑所有数据库
     * http://yii.com/index.php?r=cms/start-run
     */
    public function actionStartRun()
    {
        $res = DbName::find()->all();
        $arr = [];
        //遍历每个数据库，推送
        foreach ($res as $re) {
            $url = 'http://116.193.169.122:89/index.php?r=cms&db_name=' . $re->name;
            $arr[] = $url;
            Tools::curlGet($url);
        }
        echo '<pre>';
        print_r($arr);
    }
}