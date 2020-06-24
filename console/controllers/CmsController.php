<?php


namespace console\controllers;


use common\models\DbName;
use common\models\Tools;

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
                    $url = 'http://116.193.169.122:89/index.php?r=cms&db_name=' . $re->name;
                    $arr[] = $url;
                    Tools::curlGet($url);
                } else {

                    echo $re->name . '  执行时间：' . $time;
                    echo PHP_EOL;
                }
            }
        }
        print_r($arr);
    }
}