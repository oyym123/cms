<?php


namespace common\models;


class Base extends \yii\db\ActiveRecord
{
    const STATUS_BASE_NORMAL = 10;
    const STATUS_BASE_DISABLE = 20;

    /** 获取所有的类型 */
    public static function getBaseStatus($key = 'all')
    {
        $data = [
            self::STATUS_BASE_NORMAL => '正常',
            self::STATUS_BASE_DISABLE => '禁用',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

}