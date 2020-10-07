<?php

namespace console\controllers;

use common\models\AllBaiduKeywords;
use common\models\BaiduKeywords;
use common\models\BlackArticle;
use common\models\DbName;
use common\models\DomainColumn;
use common\models\FanUser;
use common\models\LongKeywords;
use common\models\MipFlag;
use common\models\PushArticle;
use common\models\SiteMap;
use common\models\Tools;
use common\models\ArticleRules;
use Yii;
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

    public function actionPushK()
    {
        return BaiduKeywords::pushKeywords();
    }

    public function actionSetRules()
    {
        global $argv;
        $domainId = $argv[2] ?? '';
        LongKeywords::setRules($domainId);
    }


//    public function actionPushPa()
//    {
//        BaiduKeywords::pushPa();
//    }

    /** 设置链接 */
    public function actionSetUrl()
    {
        MipFlag::crontabSet();
    }

    /** 推送Mip */
    public function actionSetMip()
    {
        MipFlag::pushMip();
    }

    /** 推送Mip */
    public function actionSetMipM()
    {
        MipFlag::pushMipM();
    }

    public function actionTransA()
    {
        //翻译文章
        LongKeywords::rulesTrans();
    }

    public function actionSetList()
    {


//        exit;

        //查询指定20个站 的规则
        $domainIds = BaiduKeywords::getDomainIds();
        //查询出所有的规则分类
        $articleRules = ArticleRules::find()->select('category_id')->where(['in', 'domain_id', $domainIds])->asArray()->all();
        $itemData = [];

        $step = 50;
        for ($i = 0; $i <= 100; $i++) {
            foreach ($articleRules as $key => $rules) {
                $keywords = AllBaiduKeywords::find()
                    ->select('id,keywords,type')
                    ->where([
                        'column_id' => 0,
                        'status' => 10,
                        'type_id' => $rules['category_id']
                    ])
                    ->andWhere(['>', 'updated_at', '2020-09-26 10:00:00'])
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
        }

//        echo '<pre>';
//        print_r($data);
//        exit;

        $url = 'http://8.129.37.130/index.php/distribute/set-keyword';
        Tools::curlPost($url, ['res' => json_encode($data)]);
        exit;

//        echo '<pre>';
//        print_r($data);
//        exit;
//
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



    public function actionCountArticle()
    {
        set_time_limit(0);
        ignore_user_abort(0);
        ini_set("memory_limit", "-1");
        $listRes = Tools::curlGet('http://8.129.37.130/distribute/list-length');
        $listArr = json_decode($listRes, true);

        $domainIds = BaiduKeywords::getDomainIds();
        $articleRules = ArticleRules::find()->select('category_id,column_id')->where(['in', 'domain_id', $domainIds])->asArray()->all();

        $itemData = [];
        $timeStart =Date('Y-m-d') . ' 00:00:00';
        $timeEnd = date("Y-m-d", strtotime("+1 day")) . ' 00:00:00';
        $_GET['domain'] = 0;
        $tuiTotal = $total = 0;
        $min = 500;
        $littleMin = 100;
        $little = $yesArr = $noArr = [];

        foreach ($articleRules as $key => $rules) {
            $tui = 0;
            $column = DomainColumn::find()
                ->where(['id' => $rules['column_id']])->one();
            $res = AllBaiduKeywords::find()
                ->where(['type_id' => $rules['category_id']])
                ->andWhere(['>', 'updated_at', $timeStart])
                ->andWhere(['<', 'updated_at', $timeEnd])
                ->andWhere(['>', 'column_id', 0])
                ->count();
//
            $tui = MipFlag::find()
                ->where(['db_id' => $column->domain_id])
                ->andWhere(['>', 'created_at', $timeStart])
                ->andWhere(['<', 'created_at', $timeEnd])
                ->count();
            $tuiTotal += $tui;
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

        $str = '';
        $str .= '<pre>';
        $str .= '<div style="background: black;color: white">';
        $str .= $timeStart . '  至 ' . $timeEnd . '<h1>期间的文章总量：' . $total . ' 篇</h1>';
        $str .= '<h2> Redis中剩余：' . Yii::$app->redis->llen('list_long_keywords') . ' 个</h2>';
        $str .= '<h2> 爬虫分发器中剩余：' . $listArr['data'][0] . ' 条</h2>';
        $str .= '<h2> 爬虫爬取关键词量：' . $catchNum . ' 个</h2>';
        $str .= '<h2> 百度推送总量：' . $tuiTotal . ' 个</h2>';
        $str .= '<h2> <a href="/cms/check-computer" target="_blank"><button>查看爬虫电脑运行状态：</button></a> </h2>';
        $str .= '<hr/>';
        $str .= '<hr/>';
        $str .= '<h2> 日' . $min . '篇文章达标域名数量：' . count($yesArr) . ' 个</h2>';
        $str .= print_r($yesArr);
        $str .= '<hr/>';
        $str .= '<h2> 日' . $littleMin . '~' . $min . '篇文章域名数量：' . count($noArr) . ' 个</h2>';
        $str .= print_r($noArr);
        $str .= '<hr/>';
        $str .= '<h2> 日低于' . $littleMin . '篇文章域名数量：' . count($little) . ' 个</h2>';
        $str .= print_r($little);
        $str .= '<hr/>';
        $str .= '</div>';
        file_put_contents('./test.log');

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


    public function actionStartMap()
    {
        SiteMap::setAllSiteMap();
    }
}