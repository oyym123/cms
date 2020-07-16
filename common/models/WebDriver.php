<?php


namespace common\models;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverOptions;

class WebDriver extends RemoteWebDriver
{
    public function initFun()
    {

    }

    public static function getSgwx($keywords, $page, $keywordsId)
    {
        set_time_limit(0);
        $host = 'http://localhost:4444/wd/hub';
        $options = new ChromeOptions();

        //启用无头模式
//        $options->addArguments(["--test-type", "--start-maximized"]);
//        $options->addArguments(["--test-type", "--ignore-certificate-errors"]);
//        $options->addArguments(["headless"]);
//        $options->addArguments([
//            '--window-size=1920,1080',
//        ]);


        $capabilities = DesiredCapabilities::chrome();
//        $capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $options);

        $driver = RemoteWebDriver::create($host, $capabilities, 50000);
        $driver->manage()->window()->minimize();

        $error = [];
        $keywordsUrl = urlencode($keywords);

        //爬取前10页的数据 【没有登入的状态最多访问前10页】
        $driver->get('https://weixin.sogou.com/weixin?query=' . $keywordsUrl . '&_sug_type_=&sut=1906&lkt=0%2C0%2C0&s_from=input&_sug_=y&type=2&sst0=1594356371082&page=' . $page . '&ie=utf8&w=01019900&dr=1');

        //获取标题与简介
        $elements = $driver->findElements(WebDriverBy::className('txt-box'));
        foreach ($elements as $key => $element) {
            sleep(3);
            try {
                $infoUrl = $driver->findElement(WebDriverBy::id('sogou_vr_11002601_img_' . $key));
                $infoIntro = $driver->findElement(WebDriverBy::id('sogou_vr_11002601_summary_' . $key));
                $infoImg = $driver->findElement(WebDriverBy::tagName('img'))->getAttribute('src');
            } catch (\Exception $e) {
                return [-1, '找不到列表元素'];
            }

            $data = [
                'url' => $infoUrl->getAttribute('href'),
                'img' => $infoImg,
                'text' => $infoIntro->getText(),
            ];

            sleep(1);

            //点击进入详情页
            $infoUrl->click();

            //获取所有窗口的标识
            $handles = $driver->getWindowHandles();

            sleep(2);

            //获取点击后的窗口
            $driver->switchTo()->window($handles[$key + 1]);

            //获取当前页面的真实链接网址
            $realDetailUrl = $driver->getCurrentURL();

            sleep(2);

            //保存文章页
            list($code, $msg) = self::saveArticle($realDetailUrl, $data, $driver->getTitle(), $keywords, $page, $key, $keywordsId);

            //切换回列表页
            $driver->switchTo()->window($handles[0]);

            if ($code < 0) {
                $error[] = $msg;
            }
            sleep(10);
        }

        $driver->quit();
        Tools::writeLog(['errors' => $error]);
        if (!empty($error)) {
            return [-1, $error];
        } else {
            return [1, $keywords . '  抓取成功!'];
        }
    }

    /** 保存文章 */
    public static function saveArticle($url, $data, $title, $keywords, $page, $rank, $keywordsId)
    {
        //判重 不可重复标题
        $oldInfo = WhiteArticle::find()->where(['title' => $title])->one();

        if (!empty($oldInfo)) {
            return [-1, $data['title'] . '   已经重复了'];
        }

        //抓取文章内容页数据
        $content = Tools::curlGet($url);

        if (strpos('该内容已被发布者删除', $content) !== false) {
            return [-1, '该内容已被发布者删除' . $url];
        }

        $contentDeal = str_replace("<br", '$*$', $content);
        preg_match('@<div id="js_article" class="rich_media">(.*)?   <div class="function_mod function_mod_index"@s', $contentDeal, $contentInfo);

        //清理标签 切词
        $contentTxt = strip_tags($contentInfo[0]);
        $arrTxt = array_filter(explode('$*$  />', $contentTxt));

        //去掉首尾
        array_pop($arrTxt);
        array_pop($arrTxt);
        array_shift($arrTxt);
        $part = json_encode($arrTxt, JSON_UNESCAPED_UNICODE);

        try {
            //标题图片存储七牛云
            list($codeImg, $msgImg) = (new Qiniu())->fetchFile($data['img'], \Yii::$app->params['QiNiuBucketImg'], Tools::uniqueName('png'));
            if ($codeImg < 0) {
                $error[] = $msgImg;
                $msgImg = $data['img'];
            }
        } catch (\Exception $e) {
            return [-1, '上传七牛云图片失败！'];
        }


        //内容中图片获取url
        preg_match('@data-src="https://mmbiz\.qpic\.cn(.*)?" data@', $content, $imgData);
        $imgData = array_filter(explode('data-src="https://mmbiz.qpic.cn', $imgData[0]));
        $imgArr = [];

        foreach ($imgData as $imgDatum) {
            $imgArr[] = 'https://mmbiz.qpic.cn' . preg_replace('@" (.*)?@', '', $imgDatum);
        }

        //将图片传至七牛云
        if (0) {     //是否传到七牛云标识 不传则使用原先文章中的图片地址
            $msgImgsArr = [];
            foreach ($imgArr as $item) {
                list($codeImgs, $msgImgs) = (new Qiniu())->fetchFile($item, \Yii::$app->params['QiNiuBucketImg'], Tools::uniqueName('png'));
                if ($codeImgs < 0) {
                    $error[] = $msgImgs . '  上传七牛云失败';
                } else {
                    $msgImgsArr[] = $msgImgs;
                    //文章中图片地址替换
                    $content = str_replace($item, $msgImgs, $content);
                }
            }
        } else {
            $msgImgsArr = $imgArr;
        }

        $dataSave = [
            'title' => $title,
            'type' => WhiteArticle::TYPE_SOUGOU_WEIXIN,
            'key_id' => $keywordsId,
            'intro' => $data['text'],
            'keywords' => $keywords,
            'cut_word' => '',
            'image_urls' => $msgImgsArr ? json_encode($msgImgsArr) : '',
            'from_path' => $url,
            'word_count' => mb_strlen($part),
            'part_content' => $part,
            'title_img' => $msgImg,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s'),
        ];


        list($codeArticle, $msgArticle) = WhiteArticle::createOne($dataSave);

        if ($codeArticle < 0) {
            $error[] = $msgArticle;
        } else {
            //同步到线上
            $onlineUrl = \Yii::$app->params['OnlineDomain'] . '/index.php?r=catch-data/upload-article';
            $onlineLog = Tools::curlPost($onlineUrl, $dataSave);
            Tools::writeLog(['线上保存日志关键词：' . $keywords . '   第' . $page . '页 第' . $rank . '行' => $onlineLog]);
        }

        if (!empty($error)) {
            return [-1, $error];
        } else {
            return [1, 'success'];
        }
    }
}