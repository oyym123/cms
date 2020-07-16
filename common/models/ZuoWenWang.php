<?php


namespace common\models;


class ZuoWenWang extends \yii\db\ActiveRecord
{
    /** 爬取作文网数据 */
    public static function catchData()
    {
        $dirArr = ['xiaoxue', 'chuzhong', 'gaozhong', 'huati', 'danyuan', 'sucai'];
        $url = 'http://www.zuowen.com/xiaoxue/';

//      $homePage = Tools::curlGet($url);
        $homePage = file_get_contents('zuowenwang.html');
        $homePage = iconv('gbk', 'UTF-8', $homePage);
        preg_match('@<div class="tager">(.*)?</div>
<div class="close">@s', $homePage, $link);

        $dirLinkArr = explode('<a target="_blank" href="', $link[0]);
        $allUrl = $newArr = [];

        foreach ($dirLinkArr as $key => $item) {
            if ($key == 1) {
                if (strpos($item, '>全部</a>') === false) {
                    $item = preg_replace('@/">(.*)?@s', '', $item);
                    $listInfo = Tools::curlNewGet($item);
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
                        }
                    }

                    print_r($allUrl);
                    exit;
                    $newArr[] = $item;
                }
            }
        }


        print_r($allUrl);
    }
}