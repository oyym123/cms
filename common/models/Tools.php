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

    /**
     * 获取两个字符串之间的子字符串
     *
     * @param
     * @param 返回两个字符串之间的子字符串，不包括分隔符
     * @param string $ string Haystack
     * @param string $ start起始分隔符
     * @param string | null $ end结束分隔符，如果省略则返回字符串的其余部分
     * @return bool | string $ start和$ end之间的子字符串，如果找不到任何一个字符串，则返回false
     */
    public static function subBetween($string, $start, $end = null)
    {
        if (($start_pos = strpos($string, $start)) !== false) {
            if ($end) {
                if (($end_pos = strpos($string, $end, $start_pos + strlen($start))) !== false) {
                    return substr($string, $start_pos + strlen($start), $end_pos - ($start_pos + strlen($start)));
                }
            } else {
                return substr($string, $start_pos);
            }
        }
        return false;
    }
}