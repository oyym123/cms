<?php

namespace app\library\pepper;
/**
 * Class RedisLock
 */
class RedisLock
{

    const UPDATE_LOCK = 'lock:update:';

    /**
     * 获取锁
     * @param  String $key 锁标识
     * @param  Int $expire 锁过期时间
     * @return Boolean
     */
    public static function lock($key, $expire = 5)
    {
        $isLock = \Yii::$app->redis->setnx($key, time() + $expire);

        // 不能获取锁
        if (!$isLock) {

            // 判断锁是否过期
            $lock_time = \Yii::$app->redis->get($key);

            // 锁已过期，删除锁，重新获取
            if (time() > $lock_time) {
                RedisLock::unlock($key);
                $isLock = \Yii::$app->redis->setnx($key, time() + $expire);
                \Yii::$app->redis->expire($key, $expire);
            }
        } else {
            \Yii::$app->redis->expire($key, $expire);
        }
        return $isLock ? true : false;
    }

    /**
     * 释放锁
     * @param  String $key 锁标识
     * @return Boolean
     */
    public static function unlock($key)
    {
        return \Yii::$app->redis->del($key);
    }

    public static function isLock($key)
    {
        // 判断锁
        $isLock = \Yii::$app->redis->get($key);
        return $isLock ? true : false;
    }
}