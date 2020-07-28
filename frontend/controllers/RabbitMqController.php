<?php


namespace frontend\controllers;

use common\models\RabbitMq;
use common\models\Tools;
use yii\web\Controller;
use Yii;

class RabbitMqController extends Controller
{
    /** 生产者 */
    public function actionIndex()
    {
        try {
            $params = Yii::$app->params['rabbitmqConfig'];
            $MqConfig = Yii::$app->params['MqConfig'];
            $params['exchange'] = $MqConfig['exchange'];
            $params['queue'] = $MqConfig['queue'];
            $mq = RabbitMq::instance($params);

            for ($i = 0; $i <= 100; $i++) {
                $content = [
                    'order_goods_id' => rand(000, 999),
                    'express' => 'JD',
                    'express_number' => 'VB52806545124535' . rand(000, 999),
                    'status' => 'Cancel',
                ];

                echo $i . ' ';

                $mq->sendMsg($content);
                Tools::writeLog(['pushQueueMsg' => json_encode($content, 256)], 'rabbitMq.txt');
            }
        } catch (\Exception $e) {
            Tools::writeLog([
                'logisticStatusChangePushQueueFailed:' => json_encode($content, 256),
                'error' => $e->getMessage()
            ], 'rabbitMq.txt');
            Yii::error($e->getMessage());
        }
    }

    /** 消费者*/
    public function actionTest()
    {
        $params = Yii::$app->params['rabbitmqConfig'];
        $MqConfig = Yii::$app->params['MqConfig'];
        $params['exchange'] = $MqConfig['exchange'];
        $params['queue'] = $MqConfig['queue'];
        $mq = RabbitMq::instance($params);
        $callback = function ($msg) {
            Tools::writeLog(['dealQueueMsg' => $msg->body], 'rabbitMqRec.txt');
            if (1) {
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            }
        };
        $mq->consumeMsg($callback);
    }
}