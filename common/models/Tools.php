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
        $headerArray = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    //清除html标签
    public static function cleanHtml($str)
    {
        $content = strip_tags($str);
        $content = str_replace('&#xa0;', PHP_EOL, $content);
        $content = str_replace('　　', PHP_EOL, $content);
        return $content;
    }
}