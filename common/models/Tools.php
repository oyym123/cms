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
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
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