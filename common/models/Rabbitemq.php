<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "rabbitemq".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $intro 介绍
 * @property int|null $type 10=爬虫 20=推送
 * @property string|null $host
 * @property string|null $port
 * @property string|null $user
 * @property string|null $pwd
 * @property string|null $vhost
 * @property string|null $exchange
 * @property string|null $queue
 * @property int|null $status 10=正常 20=禁用 30=正在执行 40=执行完毕
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Rabbitemq extends \yii\db\ActiveRecord
{
    const TYPE_REPTILE = 10;      //爬虫
    const TYPE_PUSH = 20;         //推送

    const STATUS_NORMAL = 10;      //正常
    const STATUS_DISABLE = 20;     //禁用
    const STATUS_DOING = 30;       //正在执行 开始执行
    const STATUS_DONE = 40;        //执行完毕 结束执行

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rabbitemq';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'exchange', 'queue'], 'required'],
            [['type', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'intro', 'host', 'port', 'user', 'pwd', 'vhost', 'exchange', 'queue'], 'string', 'max' => 255],
        ];
    }

    /** 获取所有的类型 */
    public static function getType($key = 'all')
    {
        $data = [
            self::TYPE_REPTILE => '爬虫',
            self::TYPE_PUSH => '推送',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    /** 获取所有的类型 */
    public static function getStatus($key = 'all')
    {
        $data = [
            self::STATUS_NORMAL => '正常',
            self::STATUS_DISABLE => '禁用',
            self::STATUS_DOING => '正在执行',
            self::STATUS_DONE => '执行完毕',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'intro' => '简介',
            'type' => '类型',
            'host' => 'Host',
            'port' => 'Port',
            'user' => 'User',
            'pwd' => 'Pwd',
            'vhost' => 'Vhost',
            'exchange' => 'Exchange',
            'queue' => 'Queue',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /** 获取日志 */
    public function getConfig()
    {
        $old = self::find()->where([])->one();


    }
}
