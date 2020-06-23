<?php


namespace common\models;


class Tools extends \yii\db\ActiveRecord
{
    /** 写入日志 */
    public static function writeLog($data, $title = 'log.txt')
    {
        $time = ['time_at' => date('Y-m-d H:i:s')];
        if (is_array($data)) {
            $res = array_merge($data + $time);
        } else {
            $res = array_merge(['res' => $data], $time);
        }
        $res = json_encode($res, JSON_UNESCAPED_UNICODE);
        $res = str_replace('\/', '/', $res);
        $path = PHP_OS == "Linux" ? '/www/wwwroot/logs/cms/' : 'E:/phpstudy_pro/WWW/';
        file_put_contents($path . $title, $res . PHP_EOL . PHP_EOL, FILE_APPEND);
    }

    public static function curlPost($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }


    public static function curlGet($url)
    {
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        //打印获得的数据
        return $output;
    }
}