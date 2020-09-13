<?php


namespace common\models;


class Base extends \yii\db\ActiveRecord
{

    const S_OFF = 0;
    const S_ON = 1;

    const STATUS_BASE_NORMAL = 10;
    const STATUS_BASE_DISABLE = 20;

    /** 获取所有的状态 */
    public static function getBaseStatus($key = 'all')
    {
        $data = [
            self::STATUS_BASE_NORMAL => '正常',
            self::STATUS_BASE_DISABLE => '禁用',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    public static function getBaseS($key = 'all')
    {
        $data = [
            self::S_OFF => '关闭',
            self::S_ON => '开启',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    public static function dd($data)
    {
        echo '<pre>';
        print_r($data);
        exit;
    }
}