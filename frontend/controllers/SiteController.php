<?php

namespace frontend\controllers;

use common\models\AllBaiduKeywords;
use common\models\BaiduKeywords;
use common\models\BlackArticle;
use common\models\Domain;
use common\models\DomainColumn;
use common\models\Fan;
use common\models\FanUser;
use common\models\LoginForm;
use common\models\PushArticle;
use common\models\SiteMap;
use common\models\Template;
use common\models\Tools;
use frontend\models\ContactForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionSiteXml()
    {
        $update = Yii::$app->request->get('update', 0);
        SiteMap::setMap(0, SiteMap::TYPE_PC_XML, $update);
    }

    public function actionSiteMxml()
    {
        $update = Yii::$app->request->get('update', 0);
        SiteMap::setMap(0, SiteMap::TYPE_M_XML, $update);
    }

    public function actionSiteMtxt()
    {
        $update = Yii::$app->request->get('update', 0);
        SiteMap::setMap(0, SiteMap::TYPE_M_TXT, $update);
    }

    public function actionSiteTxt()
    {
        $update = Yii::$app->request->get('update', 0);
        SiteMap::setMap(0, SiteMap::TYPE_PC_TXT, $update);
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        //url转换 分页
        $url = Yii::$app->request->url;
        if (strpos($url, 'index_') && preg_match('/\d+/', $url, $arr)) {
            $_GET['page'] = $arr[0];
        }

        $lastId = PushArticle::find()->select('id')->orderBy('id desc')->one()->id;

        //获取当前栏目
        $columnName = 'home';

        $domain = Domain::getDomainInfo();

        $column = DomainColumn::find()->where(['name' => $columnName, 'domain_id' => $domain->id])->one();

        if ($column->is_change) {
            if ($lastId < 280) {
                $andWhere = [];
            } else {
                $maxRand = rand($lastId - 30, $lastId);
                $minRand = rand($lastId - 280, $lastId - 101);
                $andWhere = ['between', 'id', $minRand, $maxRand];
            }
        }


        $query = PushArticle::find()->select('id,column_id,column_name,user_id,key_id,keywords,title_img,title,intro,push_time')
            ->andWhere($andWhere)->orderBy('id desc')->limit(10);

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()->all();

        list($layout, $render) = Fan::renderView(Template::TYPE_HOME);
        $this->layout = $layout;
        $url = Tools::getLocalUrl(1);

        $columnZhName = '';

        if (!empty($models)) {
            $columnObj = DomainColumn::findOne($models[0]['column_id']);
            if (!empty($columnObj)) {
                $columnZhName = $columnObj->zh_name;
                $columnEnName = $columnObj->name;
            }
        }

        foreach ($models as &$item) {
            $item['url'] = '/' . $columnEnName . '/' . $item['id'] . '/';
            $item['title'] = Tools::getKTitle($item['title']);
            $item['user_url'] = '/user/index_' . $item['user_id'] . '.html';
            $item['keywords_url'] = '/' . $domain->start_tags . $item['key_id'] . $domain->end_tags . '/';
            if ($user = FanUser::findOne($item['user_id'])) {
                $item['nickname'] = $user->username;
                $item['avatar'] = $user->avatar;
                $item['is_hot'] = 1;
                $item['is_top'] = 1;
                $item['is_recommend'] = 1;
            } else {
                $item['nickname'] = '佚名';
                $item['avatar'] = 'http://img.thszxxdyw.org.cn/userImg/b4ae0201906141846584975.png';
            }

            $item['column_info'] = [
                'name' => $columnZhName,
                'url' => '/' . $columnEnName
            ];
        }

        $res = [
            'home_list' => $models,
            'column_info' => [
                'name' => $columnZhName,
                'url' => '/' . $columnEnName
            ],
        ];

        $view = Yii::$app->view;
        $view->params['list_tdk'] = [
            'title' => $column->title,
            'keywords' => $column->keywords ?: $column->zh_name,
            'intro' => $column->intro ?: $column->zh_name,
            'canonical' => 'https://' . $_SERVER['HTTP_HOST'],
        ];

        return $this->render($render, [
            'column' => DomainColumn::getColumn(),
            'models' => $res,
            'pages' => $pages,
        ]);
    }

    public function actionCustomize()
    {
        $url = str_replace(['/', '.html'], '', Yii::$app->request->url);
        //查询该模板是否属于当前域名，防止被贼人盗用
        $template = Template::find()->where([
            'en_name' => $url,
        ])->one();
        if ($template) {
            $model = [];
            list($layout, $render) = \common\models\Fan::renderView(\common\models\Template::TYPE_CUSTOMIZE);
            $this->layout = $layout;
            //获取数据库中php代码执行结果
            eval($template->php_func);
            return $this->render($render, ['model' => $model]);
        }
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionError()
    {
        exit('12321');
        return $this->render('error', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @return yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    public function actionFavicon()
    {
        $domainId = Domain::getDomainInfo()->id;
        header("Content-type: image/jpeg");
        readfile(Tools::getLocalUrl(1) . '/images/favicon/' . $domainId . ' favicon.ico');
    }
}
