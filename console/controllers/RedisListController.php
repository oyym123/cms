<?php


namespace console\controllers;


use common\models\AllBaiduKeywords;
use common\models\RedisTools;
use common\models\Tools;

class RedisListController extends \yii\console\Controller
{

    //将关键词从队列取出并且保存到数据库中
    public function actionSetKeywords()
    {
        $dataGet = [
            'prefix' => 'list_keywords_',
            'list_name' => 'list_long_keywords',
        ];

        list($code, $msg) = (new RedisTools())->getList($dataGet, 1);
        $keywords = array_column($msg, 'key_id');

        if ($code > 0) {
            AllBaiduKeywords::setKeywords([
                'keywords' => implode(PHP_EOL, $keywords),
                'type_id' => $msg[0]['type_id']
            ]);
            Tools::writeLog(['success'], 'set_keywords.log');
        } else {
            Tools::writeLog([$msg], 'set_keywords.log');
        }
    }

}