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
        curl_setopt($curl, CURLOPT_HEADER, 0);
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

    public static function curlNewGet($url)
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    //清除html标签
    public static function cleanHtml($str)
    {
        $content = strip_tags($str);
        $content = str_replace('&#xa0;', PHP_EOL, $content);
        $content = str_replace('　　', PHP_EOL, $content);
        return $content;
    }

    /** 清理关键词空格，逗号等等 */
    public static function cleanKeywords($keywords)
    {
        //中文逗号替换成英文 逗号替换为空
        $keywords = str_replace(['，'], [','], $keywords);
        if (strpos($keywords, ',') !== false) {
            $words = explode(',', $keywords);
        } else {
            $words = explode(' ', $keywords);
        }

        $arr = [];

        foreach ($words as $item) {
            $length = mb_strlen($item);
            if ($length >= 2 && $length < 15) { //只获取关键词长度 大于2 并且 小于15的词
                $arr[] = $item;
            }
        }
        return $arr;
    }

    /** 生成唯一名称 */
    public static function uniqueName($ext)
    {
        return md5(uniqid(microtime(true), true)) . '.' . $ext;
    }

    /**
     * redis存储
     * 标记
     */
    public static function redisResSet($key, $v)
    {
        $redis = \Yii::$app->redis;
        $redis->set($key, $v);
    }

    /**
     * php生成某个范围内的随机时间
     * @param $begintime  起始时间 格式为 Y-m-d H:i:s
     * @param $endtime    结束时间 格式为 Y-m-d H:i:s
     * @param $is         是否是时间戳 格式为 Boolean
     */
    public static function randomDate($begintime, $endtime = "", $is = true)
    {
        $begin = strtotime($begintime);
        $end = $endtime == "" ? time() : strtotime($endtime);
        $timestamp = rand($begin, $end);
        return $is ? date("Y-m-d H:i:s", $timestamp) : $timestamp;
    }

    /**
     * 判断是否手机访问,火狐模拟器返回fasle，chrome模拟器返回true
     * 火狐需要在模拟器右边的“自定义 User Agent”包含下面代码判断的关键字才行，比如:Mozilla/5.0 android,或直接android
     * @return boolean
     */
    public static function isFromMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 判断手机发送的客户端标志,兼容性有待提高,把常见的类型放到前面
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array(
                'android',
                'iphone',
                'samsung',
                'ucweb',
                'wap',
                'mobile',
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'ipod',
                'blackberry',
                'meizu',
                'netfront',
                'symbian',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }

        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取顶级域名
     * @param $url
     * @return string
     */
    public static function getDoMain($url)
    {
        if (filter_var($url, FILTER_VALIDATE_IP)) {
            return $url;
        }

        if (empty($url)) {
            return '';
        }
        if (strpos($url, 'http://') !== false) {
            $url = str_replace('http://', '', $url);
        }
        if (strpos($url, 'https://') !== false) {
            $url = str_replace('https://', '', $url);
        }
        $n = 0;
        for ($i = 1; $i <= 3; $i++) {
            $n = strpos($url, '/', $n);
            $i != 3 && $n++;
        }

        $nn = strpos($url, '?');
        $mix_num = min($n, $nn);
        if ($mix_num > 0 || !empty($mix_num)) {
            //防止链接带有点 （.） 导致出错
            $url = mb_substr($url, 0, $mix_num);
        }
        $data = explode('.', $url);

        $co_ta = count($data);
        //判断是否是双后缀
        $no_tow = true;
        $host_cn = 'com.cn,net.cn,org.cn,gov.cn';
        $host_cn = explode(',', $host_cn);
        foreach ($host_cn as $val) {
            if (strpos($url, $val)) {
                $no_tow = false;
            }
        }
        //截取域名后的目录
        $del = strpos($data[$co_ta - 1], '/');
        if ($del > 0 || !empty($del)) {
            $data[$co_ta - 1] = mb_substr($data[$co_ta - 1], 0, $del);
        }
        //如果是返回FALSE ，如果不是返回true
        if ($no_tow == true) {
            $host = $data[$co_ta - 2] . '.' . $data[$co_ta - 1];
        } else {
            $host = $data[$co_ta - 3] . '.' . $data[$co_ta - 2] . '.' . $data[$co_ta - 1];
        }

        return $host;
    }

    /**
     * 跳转到相对应的域名 视图
     * 移动端跳转m.domain
     * PC端跳转 www.domain domain
     */
    public static function jumpDomain($mRender, $pRender, $url)
    {
        if (Tools::isFromMobile() && (strpos($url, 'm.') !== false)) {  //表示来自mobile端的域名 则跳转到移动端视图
            $render = $mRender;
        } elseif (Tools::isFromMobile() && (strpos($url, 'm.') === false)) {  //表示 来PC的域名 得先进行跳转 mobile
            $res = Tools::getDoMain($url);
            //截取域名 跳转到移动端
            Header('Location: http://m.' . $res . \Yii::$app->request->url);
            exit;
        } else {
            $render = $pRender;
        }

        //表示m端的域名
        if (strpos($url, 'm.') !== false) {
            $render = $mRender;
        }
        return $render;
    }

    /** 设置公共视图 */
    public static function setLayout($topDomain)
    {
        $column = explode('/', $_SERVER['REQUEST_URI'])[1];

        if (empty($column) || strpos($column, '.html') !== false) {  //没有二级类目
            $column = 'home';
        }

        //表示是手机端 或者是手机端的域名 请求 则展示移动端的视图
        $path = $topDomain . '/' . $column . '/';

        if (self::isFromMobile() || strpos($_SERVER['HTTP_HOST'], 'm.') !== false) {
            $layout = $path . 'm_main.php';
        } else {
            $layout = $path . 'main.php';
        }

        return [$layout, $path];
    }

    public static function cleanNumber($str)
    {
        return str_replace(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'], '', $str);
    }

    public static function DebugToolbarOff()
    {
        if (class_exists('\yii\debug\Module')) {
            \Yii::$app->view->off(\yii\web\View::EVENT_END_BODY, [\yii\debug\Module::getInstance(), 'renderToolbar']);
        }
    }

    /** 判断是否有爬虫 */
    public static function isSpider()
    {
        $flag = false;
        $tmp = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($tmp, 'Googlebot') !== false) {
            $flag = true;
        } else if (strpos($tmp, 'Baiduspider') > 0) {
            $flag = true;
        } else if (strpos($tmp, 'Yahoo! Slurp') !== false) {
            $flag = true;
        } else if (strpos($tmp, 'msnbot') !== false) {
            $flag = true;
        } else if (strpos($tmp, 'Sosospider') !== false) {
            $flag = true;
        } else if (strpos($tmp, 'YodaoBot') !== false || strpos($tmp, 'OutfoxBot') !== false) {
            $flag = true;
        } else if (strpos($tmp, 'Sogou web spider') !== false || strpos($tmp, 'Sogou Orion spider') !== false) {
            $flag = true;
        } else if (strpos($tmp, 'fast-webcrawler') !== false) {
            $flag = true;
        } else if (strpos($tmp, 'Gaisbot') !== false) {
            $flag = true;
        } else if (strpos($tmp, 'ia_archiver') !== false) {
            $flag = true;
        } else if (strpos($tmp, 'altavista') !== false) {
            $flag = true;
        } else if (strpos($tmp, 'lycos_spider') !== false) {
            $flag = true;
        } else if (strpos($tmp, 'Inktomi slurp') !== false) {
            $flag = true;
        }
        return $flag;
    }

    /**
     * 内容校验
     *  防止有人模板 填写exec命令盗取代码
     */
    public static function contentCheck($content)
    {
        $execArr = ['exec', 'system', 'file_get_content', 'passthru', 'popen'];
        foreach ($execArr as $item) {
            if (strpos($content, $item) !== false) {
                return false;
            }
        }
        return true;
    }

    /** 获取爬虫地址 */
    public static function reptileUrl()
    {
        return PHP_OS == 'WINNT' ? \Yii::$app->params['local_reptile_url'] : \Yii::$app->params['online_reptile_url'];
    }

    /** 格式化时间 */
    public static function formatTime($time)
    {
        if (is_int($time)) {
            $time = intval($time);
        } else {
            return '';
        }

        $ctime = time();
        $t = $ctime - $time; //时间差 （秒）

        if ($t < 0) {
            return date('Y-m-d', $time);
        }

        $y = intval(date('Y', $ctime) - date('Y', $time));//是否跨年

        if ($t == 0) {
            $text = '刚刚';
        } elseif ($t < 60) {//一分钟内
            $text = $t . '秒前';
        } elseif ($t < 3600) {//一小时内
            $text = floor($t / 60) . '分钟前';
        } elseif ($t < 86400) {//一天内
            $text = floor($t / 3600) . '小时前'; // 一天内
        } elseif ($t < 2592000) {//30天内
            if ($time > strtotime(date('Ymd', strtotime("-1 day")))) {
                $text = '昨天';
            } elseif ($time > strtotime(date('Ymd', strtotime("-2 days")))) {
                $text = '前天';
            } else {
                $text = floor($t / 86400) . '天前';
            }
        } elseif ($t < 31536000 && $y == 0) {//一年内 不跨年
            $m = date('m', $ctime) - date('m', $time) - 1;

            if ($m == 0) {
                $text = floor($t / 86400) . '天前';
            } else {
                $text = $m . '个月前';
            }
        } elseif ($t < 31536000 && $y > 0) {//一年内 跨年
            $text = (12 - date('m', $time) + date('m', $ctime)) . '个月前';
        } else {
            $text = (date('Y', $ctime) - date('Y', $time)) . '年前';
        }

        return $text;
    }

}