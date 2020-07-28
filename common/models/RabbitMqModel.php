<?php


namespace common\models;

use Yii;

class RabbitMqModel extends \yii\db\ActiveRecord
{
    /** 生产者 */
    public static function producer($content)
    {
        try {
            $params = Yii::$app->params['rabbitmqConfig'];
            $MqConfig = Yii::$app->params['MqConfig'];
            $params['exchange'] = $MqConfig['exchange'];
            $params['queue'] = $MqConfig['queue'];
            $mq = RabbitMq::instance($params);
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

    /** 消费者*/
    public static function consumer()
    {
        $params = Yii::$app->params['rabbitmqConfig'];
        $MqConfig = Yii::$app->params['MqConfig'];
        $params['exchange'] = $MqConfig['exchange'];
        $params['queue'] = $MqConfig['queue'];
        $mq = RabbitMq::instance($params);
        $callback = function ($msg) {
            Tools::writeLog(['dealQueueMsg' => $msg->body], 'rabbitMqRec.txt');
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };
        $mq->consumeMsg($callback);
    }
}