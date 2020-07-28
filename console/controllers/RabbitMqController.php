<?php


namespace console\controllers;


use common\models\Rabbitemq;
use common\models\RabbitMq;
use common\models\Tools;
use Yii;

class RabbitMqController extends \yii\console\Controller
{
    /**
     *  开启队列
     */
    public function actionStart()
    {
        //获取所有的队列
        $res = Rabbitemq::find()->select('host,port,user,pwd,vhost,exchange,queue,')->where(['status' => Rabbitemq::STATUS_NORMAL])->all();
        foreach ($res as $item) {
            try {

                $mq = RabbitMq::instance($item);
                $mq->sendMsg($content);

                Tools::writeLog(['pushQueueMsg' => json_encode($content, 256)], 'rabbitMq.txt');
            } catch (\Exception $e) {
                Tools::writeLog([
                    'logisticStatusChangePushQueueFailed:' => json_encode($content, 256),
                    'error' => $e->getMessage()
                ], 'rabbitMq.txt');
                Yii::error($e->getMessage());
            }
        }
    }


}