<?php


namespace common\models;

use Yii;

class RedisTools
{

    public $list_name = 'keyword_list';

    public $size = 2;

    public $prefix = 'long_keyword_';

    /**
     * 插入关键字
     */
    public function setList($post)
    {
//        $data = [
//            'prefix' => $post['prefix'],
//            'list_name' => $post['list_name'],
//            'key_id' => $post['key_id'],
//            'type_id' => $post['type_id'],
//        ];

        $noArr = ['prefix', 'list_name', 'key_id'];
        $newArr = [];

        foreach ($post as $key => $item) {
            if (!in_array($key, $noArr)) {
                $newArr[$key] = $item;
            }
        }

        $redis = Yii::$app->redis;
        $error = [];
        $yesNum = 0;
        foreach ($post['key_id'] as $key => $val) {
            $newArr['key_id'] = $val;
            //设置过滤验证
            if (!$redis->get($post['prefix'] . $val)) {
                $yesNum += 1;
                $redis->set($post['prefix'] . $val, $val);
                $redis->lpush($post['list_name'], json_encode($newArr, JSON_UNESCAPED_UNICODE));
            } else {
                $error[] = $val . ' 重复！';
            }
        }
        return [$yesNum, $error];
    }

    /**
     * 分发关键字
     */
    public function getList($data, $size = 200)
    {
        $redis = Yii::$app->redis;
        //获取当前队列长度
        $length = $redis->llen($data['list_name']);
        if ($length == 0) {
            return [-1, '没有关键词了!'];
        }
        $arr = [];
        for ($i = 0; $i < $size; $i++) {
            try {
                $record = $redis->rpop($data['list_name']);
                if (!empty($record)) {
                    $res = json_decode($record, true);
//                    $redis->setex($data['prefix'] . $res['key_id'], 60 * 60 * 24, $record);
                    //处理业务逻辑
                    $arr[] = $res;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }
        return [1, $arr];
    }
}