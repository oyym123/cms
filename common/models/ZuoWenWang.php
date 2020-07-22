<?php


namespace common\models;

use Yii;

class ZuoWenWang extends \yii\db\ActiveRecord
{
    /** 爬取作文网数据 */
    public function catchData()
    {
        set_time_limit(0);
        $dirArr = ['xiaoxue', 'chuzhong', 'gaozhong', 'huati', 'danyuan', 'sucai'];
        $url = 'http://www.zuowen.com/xiaoxue/';

//        $key = 'xiaoxue_test';
//
        $homePage = Tools::curlGet($url);

//        $homePage = file_get_contents('zuowenwang.html');
        $homePage = iconv('gbk', 'UTF-8', $homePage);
        preg_match('@<div class="tager">(.*)?</div>
<div class="close">@s', $homePage, $link);

        $dirLinkArr = explode('<a target="_blank" href="', $link[0]);
        $allUrl = $newArr = [];
        $redisKey = 'catch_data_xiaoxue';
        foreach ($dirLinkArr as $key => $item) {
            if (strpos($item, '>全部</a>') === false) {
                if ($key == 1) {
                    $item = preg_replace('@/">(.*)?@s', '', $item);
                    $value = $this->getList($redisKey, $item);
                    $allUrl[] = $value;
                }
            }
        }
    }

    /** 获取所有的列表 */
    public function getList($redisKey, $url)
    {
        $allUrl = [];
        for ($i = 1; $i < 100; $i++) {
            if ($i == 1) {
                $item = $url;
            } else {
                $item = $url . '/index_' . $i . '.shtml';
            }

            sleep(2);

            //获取列表页
            $listInfo = Tools::curlNewGet($item);

            //表示有该页面 就接着爬
            if (strpos($listInfo, '您要浏览的页面暂时无法访问或不存在') === false) {
                $listInfo = iconv('gbk', 'UTF-8', $listInfo);
                preg_match('@<div class="artlist">(.*)?<div class="artpage">@s', $listInfo, $newList);
                $newInfoArr = explode('<div class="artbox_l_t">', $newList[0]);
                foreach ($newInfoArr as $key => $value) {
                    if ($key == 0) {
                        continue;
                    }

                    preg_match('@<a href="(.*)?" title="@s', $value, $newValue);
                    if (!empty($newValue)) {
                        $allUrl[] = $newValue[1];
                        file_put_contents('demo.txt', $newValue[1] . PHP_EOL, FILE_APPEND);
                    }
                }
            } else {
                continue;
            }
        }
        return $allUrl;
    }

    /** 保存文章网数据 */
    public function saveData()
    {
        set_time_limit(0);
        $error = [];
        $data = file_get_contents('demo.txt');
        $data = array_filter(explode(PHP_EOL, $data));

        if ($data) {
            foreach ($data as $k => $url) {
                $oldUrl = BlackArticle::find()->where(['from_path' => $url])->one();
                if (!empty($oldUrl)) {
                    $error[] = '已经爬取过';
                    continue;
                }

                $res = Tools::curlGet($url);
                $res = iconv('gbk', 'UTF-8', $res);
                preg_match('@<h1 class="h_title">(.*)?</h1>@', $res, $infoTitle);
                preg_match('@<div class="con_content">(.*)?<p style="@s', $res, $infoContentMatch);
                $infoContent = preg_replace('@<br/></p><p style="(.*)?@', '', $infoContentMatch[1]);
                $title = preg_replace('@(.*)?：@', '', $infoTitle[1]);
                $title = preg_replace('@_(.*)@', '', $title);

                //判重 不可重复标题
                $oldInfo = BlackArticle::find()->where(['title' => $title])->one();

                if (empty($title)) {
                    $error[] = '标题为空';
                    continue;
                }

                if (!empty($oldInfo)) {
                    $error[] = $title . '   已经重复了';
                    continue;
                }

                $msgImgsArr = [];

                //清理标签 切词
                $infoContent = strip_tags($infoContent);

                $infoContent = str_replace('　　', '&nbsp; &nbsp; &nbsp;', $infoContent);
                $txtArr = array_filter(explode('&nbsp; &nbsp; &nbsp;', $infoContent));
                array_shift($txtArr);
                $str = implode('{*}', $txtArr);
                $img = '';
                if (empty($txtArr)) {
                    $error[] = $title . '没有内容';
                    continue;
                }

                list($titleTag, $tagsName) = $this->setTags($infoTitle[1]);
                $title = $titleTag . '英语作文:' . $title;

                //根据文章含义抓取图片
                list($imgCode, $imgMsg) = Images::catchPixabay($title);

                if ($imgCode > 0) {
                    $img = $imgMsg;
                    $contentStr = '<br><img src="' . $img . '" alt="' . $title . '"><br><br>';
                } else {
                    $contentStr = '';
                }

                $part = json_encode($txtArr, JSON_UNESCAPED_UNICODE);

                //有道翻译
                $ret = (new YouDaoApi())->startRequest($str);
                $ret = json_decode($ret, true);

                $enRes = explode('{*}', $ret['translation'][0]);

                //英文词分词
                $enPart = json_encode($enRes, JSON_UNESCAPED_UNICODE);
                $allPart = [];

                foreach ($enRes as $key => $value) {
                    $allPart[] = $value . '<br>' . $txtArr[$key];
                    $contentStr .= $value . '<br>' . $txtArr[$key] . '<br><br>';
                }

                //中英文分词
                $allPart = json_encode($allPart, JSON_UNESCAPED_UNICODE);

                sleep(3);

                $dataSave = [
                    'title' => $title,
                    'type' => BlackArticle::TYPE_ZUO_WEN_WANG,
                    'key_id' => 0,
                    'intro' => $infoTitle[1],
                    'keywords' => $title,
                    'cut_word' => '',
                    'image_urls' => $msgImgsArr ? json_encode($msgImgsArr) : '',
                    'from_path' => $url,
                    'word_count' => mb_strlen($contentStr),
                    'part_content' => $part,
                    'en_part_content' => $enPart,
                    'all_part_content' => $allPart,
                    'title_img' => $img,
                    'content' => $contentStr,
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                list($codeArticle, $msgArticle) = BlackArticle::createOne($dataSave);
                if ($codeArticle < 0) {
                    $error[] = $msgArticle;
                }
            }


        }
        echo '<pre>';
        print_r($error);
    }

    /** 随机设置标签 */
    public function setTags($intro)
    {
        $keyArr = ['一年级', '二年级', '三年级', '四年级', '五年级', '六年级'];
        $titleTag = '小学';
        $tagsName = '小学';
        foreach ($keyArr as $value) {
            if (strpos($intro, $value) !== false) {
                //获取随机tags
                $tags = BaiduKeywords::find()->where(['like', 'keywords', $value])->orderBy('RAND()')->one();
                $tagsName = $tags ? $tags->keywords : '小学';
                $titleTag = $value;
            }
        }
        return [$titleTag, $tagsName];
    }

}