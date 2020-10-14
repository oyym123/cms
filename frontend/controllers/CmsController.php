<?php

namespace frontend\controllers;

use Cassandra\Date;
use common\models\AllBaiduKeywords;
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
        (new BaiduKeywords())->getSdkWords(233100);
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

    public function actionChangeTemp()
    {
        //更新模板
        $template = Template::find()
            ->where(['like', 'content', 'https://img.thszxxdyw.org.cn/wordImg/'])
            ->andWhere(['type' => 6])
            ->all();

        set_time_limit(0);
        foreach ($template as $item) {
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
            $item['content'] = str_replace('http://img.thszxxdyw.org.cn/wordImg/', 'https://img.thszxxdyw.org.cn/wordImg/', $item['content']);
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

    public function actionTransA()
    {
        //翻译文章
        LongKeywords::rulesTrans();
    }

    public function actionCountArticle()
    {
        set_time_limit(0);
        ignore_user_abort(0);
        ini_set("memory_limit", "-1");
        $listRes = Tools::curlGet('http://8.129.37.130/distribute/list-length');
        $listArr = json_decode($listRes, true);

        $domainIds = BaiduKeywords::getDomainIds();
        $articleRules = ArticleRules::find()->select('category_id,column_id,domain_id')->where(['in', 'domain_id', $domainIds])->asArray()->all();

        $itemData = [];
        $timeStart = Yii::$app->request->get('start', Date('Y-m-d') . ' 00:00:00');
        $timeEnd = Yii::$app->request->get('end', date("Y-m-d", strtotime("+1 day")) . ' 00:00:00');
        $_GET['domain'] = 0;
        $total = 0;
        $min = 500;
        $littleMin = 100;
        $little = $yesArr = $noArr = [];

        foreach ($articleRules as $key => $rules) {
            $tui = 0;
            $column = DomainColumn::find()
                ->where(['id' => $rules['column_id']])->one();

            $res = PushArticle::findx($column->domain_id)
                ->andWhere(['>', 'created_at', $timeStart])
                ->andWhere(['<', 'updated_at', $timeEnd])
                ->count();

            $lastArticle = PushArticle::findx($column->domain_id)->orderBy('id desc')->one();
            $total += $res;
            $lastUrl = 'https://' . $column->domain->name . '/' . $lastArticle->column_name . '/' . $lastArticle->id . '.html';

            $itemData = [
                '文章数量' => '<strong style="color: green;font-size: larger">' . $res . '</strong>',
                '百度推送数量' => '<strong style="color: greenyellow;font-size: larger">' . $tui . '</strong>',
                '域名' => $column->domain->name,
                '域名ID' => $column->domain_id,
                '栏目名称' => $column->name,
                '栏目中文名称' => $column->zh_name,
                '最后一条连接' => '<a href="' . $lastUrl . '" target="_blank">' . $lastUrl . '</a>',
                '开始时间' => $timeStart,
                '结束时间' => $timeEnd,
            ];

            if ($res < $min) {
                if ($res < $littleMin) {
                    $little[] = $itemData;
                } else {
                    $noArr[] = $itemData;
                }
            } else {
                $yesArr[] = $itemData;
            }
        }

        $catchNum = AllBaiduKeywords::find()
            ->where([
                'status' => 10,
            ])
            ->andWhere(['>', 'back_time', $timeStart])
            ->andWhere(['<', 'back_time', $timeEnd])
            ->count();

        $tuiTotal = MipFlag::find()
            ->andWhere(['>', 'created_at', $timeStart])
            ->andWhere(['<', 'created_at', $timeEnd])
            ->count();

        echo '<pre>';
        echo '<div style="background: black;color: white">';
        echo $timeStart . '  至 ' . $timeEnd . '<h1>期间的文章总量：' . $total . ' 篇</h1>';
//        echo '<h2> 关键词推入总量：' . $pushNum . ' 个</h2>';
        echo '<h2> Redis中剩余：' . Yii::$app->redis->llen('list_long_keywords') . ' 个</h2>';
        echo '<h2> 爬虫分发器中剩余：' . $listArr['data'][0] . ' 条</h2>';
        echo '<h2> 爬虫爬取关键词量：' . $catchNum . ' 个</h2>';
        echo '<h2> 百度推送总量：' . $tuiTotal . ' 个</h2>';
        echo '<h2> <a href="/cms/check-computer" target="_blank"><button>查看爬虫电脑运行状态：</button></a> </h2>';

        echo '<hr/>';
        echo '<hr/>';

        echo '<h2> 日' . $min . '篇文章达标域名数量：' . count($yesArr) . ' 个</h2>';
        print_r($yesArr);
        echo '<hr/>';

        echo '<h2> 日' . $littleMin . '~' . $min . '篇文章域名数量：' . count($noArr) . ' 个</h2>';
        print_r($noArr);
        echo '<hr/>';

        echo '<h2> 日低于' . $littleMin . '篇文章域名数量：' . count($little) . ' 个</h2>';
        print_r($little);
        echo '<hr/>';

        echo '</div>';
        exit;
    }


    public function actionCheckComputer()
    {
        $checkComputer = Tools::curlGet(\Yii::$app->params['local_reptile_url'] . '/cms/check-computer');


        echo '<pre>';
        echo '<div style="background: black;color: white;">';
        echo '<h1>时间大于15分钟的标记为红色!</h1>';
        $data = json_decode($checkComputer, true);
        $num = 0;
        $noArr = $yesArr = [];
        foreach ($data as $datum) {
            if ($datum['time'] > (15 * 60)) {
                $num += 1;
                $noArr[] = $datum['电脑号'];
            } else {
                $yesArr[] = $datum['电脑号'];
            }
        }

        echo '<h1>异常机器 <strong style="color: red;font-size: larger">' . $num . '台 ---  ' . implode(',', $noArr) . '</strong></h1>';
        echo '<h1>正常机器<strong style="color: green;font-size: larger">' . (count($data) - $num) . '台 --- ' . implode(',', $yesArr) . '</strong></h1>';
        print_r(json_decode($checkComputer, true));

        echo "<script language=\"JavaScript\"> 
function myrefresh() 
{ 
window.location.reload(); 
} 
setTimeout('myrefresh()',5000); //5秒刷新一次 
</script> ";
        echo '<pre>';
        echo '</div>';
        exit;
    }

    public function actionSetList()
    {
        exit('暂停使用！');
        //查询指定20个站 的规则
        $domainIds = BaiduKeywords::getDomainIds();
        //查询出所有的规则分类
        $articleRules = ArticleRules::find()->select('category_id')->where(['in', 'domain_id', $domainIds])->asArray()->all();
        $itemData = [];

        $step = 50;
        $limit = 50;

        for ($i = 1; $i <= $limit; $i++) {
            foreach ($articleRules as $key => $rules) {
                $keywords = AllBaiduKeywords::find()
                    ->select('id,keywords,type')
                    ->where([
                        'column_id' => 0,
                        'status' => 10,
                        'type_id' => $rules['category_id']
                    ])
                    ->andWhere(['>', 'updated_at', '2020-09-24 08:00:00'])
                    ->andWhere([
                        'catch_status' => 100
                    ])
                    ->andWhere(['back_time' => null])
                    ->orderBy('id desc')
                    ->offset($i * $step)
                    ->limit($step)
                    ->asArray()
                    ->all();

                foreach ($keywords as $keyword) {
                    $data[] = [
                        'keyword' => $keyword['keywords'],
                        'key_id' => $keyword['id'],
                        'id' => 0,
                        'type' => $keyword['type'],
                    ];
                }
            }
            $url = 'http://8.129.37.130/index.php/distribute/set-keyword';
            Tools::curlPost($url, ['res' => json_encode($data)]);
        }
        exit;

        $url = 'http://8.129.37.130/index.php/distribute/set-keyword';
        $res = Tools::curlPost($url, ['res' => json_encode($data)]);

        echo '<pre>';
        print_r($data);
        exit;

//        $data = [];

//        $urlGet = 'http://8.129.37.130/index.php/distribute/set-keyword';
//
//        Tools::curlNewGet()
//        echo '<pre>';
//        print_r($data);exit;

        $url = 'http://8.129.37.130/index.php/distribute/set-keyword';
        $res = Tools::curlPost($url, ['res' => json_encode($data)]);
        print_r($res);
    }


}