<?php


namespace common\models;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverOptions;

class WebDriver extends RemoteWebDriver
{
    public function initFun()
    {

    }

    public static function getSgwx()
    {
        set_time_limit(0);
        $host = 'http://localhost:4444/wd/hub';

        $capabilities = DesiredCapabilities::chrome();

        $driver = RemoteWebDriver::create($host, $capabilities);
        $options = new WebDriverOptions();

        $options->addCookie();

        $driver->manage()->window()->maximize();
        $error = [];

        $driver->get('https://weixin.sogou.com/weixin?query=%E8%8B%B1%E8%AF%AD&_sug_type_=&sut=1906&lkt=0%2C0%2C0&s_from=input&_sug_=y&type=2&sst0=1594356371082&page=3&ie=utf8&w=01019900&dr=1');

        sleep(2);

        $elements = $driver->findElements(WebDriverBy::className('txt-box'));

        //获取标题与简介
        foreach ($elements as $key => $element) {
            $infoUrl = $driver->findElement(WebDriverBy::id('sogou_vr_11002601_img_' . $key));
            $infoIntro = $driver->findElement(WebDriverBy::id('sogou_vr_11002601_summary_' . $key));
            $infoImg = $driver->findElement(WebDriverBy::tagName('img'))->getAttribute('src');

            $data = [
                'url' => $infoUrl->getAttribute('href'),
                'img' => $infoImg,
                'text' => $infoIntro->getText(),
            ];

            Tools::writeLog($data);

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

            Tools::writeLog([$realDetailUrl]);


            //保存文章页
            list($code, $msg) = self::saveArticle($realDetailUrl, $data, $driver->getTitle());

            //切换回列表页
            $driver->switchTo()->window($handles[0]);

            if ($code < 0) {
                $error[] = $msg;
            }
            sleep(10);
        }

        $driver->quit();
        Tools::writeLog($error);
    }

    /** 保存文章 */
    public static function saveArticle($url, $data, $title)
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

        //标题图片存储七牛云
        list($codeImg, $msgImg) = (new Qiniu())->fetchFile($data['img'], \Yii::$app->params['QiNiuBucketImg'], Tools::uniqueName('png'));
        if ($codeImg < 0) {
            $error[] = $msgImg . '  上传七牛云失败';
            $msgImg = $data['img'];
        }

        //内容中图片获取url
        preg_match('@data-src="https://mmbiz\.qpic\.cn(.*)?" data@', $content, $imgData);
        $imgData = array_filter(explode('data-src="https://mmbiz.qpic.cn', $imgData[0]));
        $imgArr = [];

        foreach ($imgData as $imgDatum) {
            $imgArr[] = 'https://mmbiz.qpic.cn' . preg_replace('@" (.*)?@', '', $imgDatum);
        }

        //将图片传至七牛云
        if (1) {     //是否传到七牛云标识 不传则使用原先文章中的图片地址
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
            'key_id' => 0,
            'intro' => $data['text'],
            'keywords' => '',
            'cut_word' => '',
            'image_urls' => $msgImgsArr ? json_encode($msgImgsArr) : '',
            'from_path' => $url,
            'word_count' => mb_strlen($part),
            'part_content' => $part,
            'title_img' => $msgImg,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        //同步到线上
        $onlineUrl = \Yii::$app->params['OnlineDomain'] . '/index.php?r=catch-data/upload-article';
        Tools::curlPost($onlineUrl, $dataSave);

        list($codeArticle, $msgArticle) = WhiteArticle::createOne($dataSave);
        if ($codeArticle < 0) {
            $error[] = $msgArticle;
        }

        if (!empty($error)) {
            return [-1, $error];
        } else {
            return [1, 'success'];
        }
    }
}