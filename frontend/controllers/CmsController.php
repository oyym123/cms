<?php

namespace frontend\controllers;

use common\models\CmsAction;
use common\models\NewsTags;
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
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $tag = new NewsTags();
        list($code, $msg) = $tag->result();
        list($code, $msg) = $tag->result2();

        $model = new  CmsAction();
        list($code, $msg) = $model->result();
        list($code, $msg) = $model->result2();
    }

}