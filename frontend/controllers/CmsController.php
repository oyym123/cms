<?php

namespace frontend\controllers;

use common\models\ArticleRules;
use common\models\BaiduKeywords;
use common\models\BaiDuSdk;
use common\models\BlackArticle;
use common\models\CmsAction;
use common\models\DbName;
use common\models\DirCatch;
use common\models\Domain;
use common\models\DomainColumn;
use common\models\DomainTpl;
use common\models\KeywordLongAll;
use common\models\Keywords;
use common\models\LongKeywords;
use common\models\MipFlag;
use common\models\NewsClass;
use common\models\NewsClassTags;
use common\models\NewsData;
use common\models\NewsTags;
use common\models\PushArticle;
use common\models\Template;
use common\models\Tools;
use common\models\ZuoWenWang;
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

    public $enableCsrfValidation = false;

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

        $model = new CmsAction();
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
                    $url = 'http://' . $_SERVER['SERVER_ADDR'] . ':89/index.php?r=cms&db_name=' . $re->name;
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

    /**
     * 抓取百度营销词
     */
    public function actionCatchYin()
    {
        set_time_limit(0);
        (new BaiduKeywords())->getSdkWords();
    }

    /** 爬取关键词
     * http://' . $_SERVER['SERVER_ADDR'] . ':89/index.php?r=cms/catch-key
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
        LongKeywords::pushReptile();
        LongKeywords::getKeywords();
    }

    /**
     * 抓取文章目录
     */
    public function actionDirCatch()
    {
        (new DirCatch())->catchHtmlArticle();
    }

    /** 获取所有的数据库的class分类 */
    public function actionGetClass()
    {
        $res = NewsClass::find()->where([])->asArray()->all();
        echo json_encode($res);
        exit;
    }

    /** 获取所有的数据库的tags分类 */
    public function actionGetTags()
    {
        $res = NewsClassTags::find()->where([])->asArray()->all();
        echo json_encode($res);
        exit;
    }

    /** 保存文章到CMS数据库 */
    public function actionSetArticle()
    {
        $res = Yii::$app->request->post();
        Tools::writeLog($res, 'article.log');
        list($code, $msg) = NewsData::createOne($res);
        if ($code < 0) {
            echo '<pre>';
            print_r($msg);
            exit;
        } else {
            NewsData::setStaticHtml($res['db_class_id'], $msg, $res['host_name']);
        }
    }

    /** 将百度营销关键词导入cms tags */
    public function actionImportTags()
    {
        NewsTags::import();
    }

    /** 推送黑帽文章
     * cms/push-black-article
     */
    public function actionPushBlackArticle()
    {
        (new BlackArticle())->pushArticle();
    }

    public function actionSetLong()
    {
        $data = [];

        $res = file_get_contents('./test01.txt');
        $data = explode(PHP_EOL, $res);

        foreach ($data as $datum) {
            KeywordLongAll::createOne(['keywors_name' => $datum]);
        }
    }

    /** 作文网数据爬取 & 翻译 */
    public function actionZww()
    {
        $url = 'http://www.zuowen.com/e/20200526/5ecd1d79af916.shtml';
        (new ZuoWenWang())->saveData([$url]);
    }

    public function actionPushArticle()
    {
        ArticleRules::dealData();
    }

    public function actionCreateTable()
    {
        for ($i = 1; $i <= 8; $i++) {
            (new PushArticle())->createTable($i);
        }
    }

    public function actionSetRules()
    {
        LongKeywords::setRules();
    }

    public function actionPushK()
    {
        return BaiduKeywords::pushKeywords();
    }


    public function  actionChangeTemp()
    {
        //更新模板
        $template = Template::find()
            ->where(['like', 'content', 'https://img.thszxxdyw.org.cn/wordImg/'])
            ->andWhere(['type' => 6])
            ->all();

        set_time_limit(0);
        foreach ($template as $item){
            sleep(2);
            DomainTpl::setTmp(0, $item->id);
        }
    }

    public function actionCleanData()
    {
        $template = Template::find()
            ->where(['like', 'content', 'http://img.thszxxdyw.org.cn/wordImg/'])
            ->andWhere(['type' => 6])
            ->all();


        foreach ($template as $item) {
            $item['content'] = str_replace('https://img.thszxxdyw.org.cn/wordImg/', 'https://static.thszxxdyw.org.cn', $item['content']);
//            echo '<pre>';
//            print_r($item);
//            exit;
            $item->content = $item['content'];
            $item->save(false);

        }
         exit;
//        $models = DomainColumn::find()->all();
//        foreach ($models as $column) {
//            if ($column->name == 'jaks') {
//                $domain = Domain::find()->where(['id' => $column->domain_id])->one();
//                if (!empty($domain)) {
//                    $column->name = $domain->start_tags;
//                    $column->save(false);
//                }
//            }
//        }
    }

    public function actionPushMip()
    {
        MipFlag::pushUrl(3);
    }

    public function actionTrans()
    {
        PushArticle::transArticle();
    }
}