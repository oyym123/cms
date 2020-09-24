<?php


namespace frontend\controllers;


use common\models\Tools;
use yii\web\Controller;

class BaseController extends Controller
{
    public $offset = 0;
    public $limit = 20;
    public $pages = 0;
    public $userId = 0;
    public $userIdent = 0;
    public $token;

    /** 判断操作系统是不是windows，方便测试和开发 */
    public static function isWindows()
    {
        if (PHP_OS == 'WINNT') {
            return true;
        };
    }

    /**
     * 解析并送出JSON
     * 200101
     * @param array $res 资源数组，如果是一个字符串则当成错误信息输出
     * @param int $state 状态值，默认为0
     * @param int $msg 是否直接输出,1为返回值
     * @return array
     **/
    public static function showMsg($res, $code = 0, $msg = '成功')
    {
        //header("Content-type: application/json; charset=utf-8");

        if (empty($res)) {
            if ($res == []) {
                $res = [];
            } else {
                $res = '';
            }
        }
        // 构造数据
        $item = array('code' => $code, 'message' => $msg, 'data' => null);

        if (is_array($res) && !empty($res)) {
            $item['data'] = self::int2String($res); // 强制转换为string类型下放
        } elseif (is_string($res)) {
            $item['message'] = $res;
        }

        // 是否需要送出get
        if (isset($_GET['isget']) && $_GET['isget'] == 1) {
            $item['pars'] = !empty($_GET) ? $_GET : null;
        }

        //   if ((isset($_GET['debug']) && $_GET['debug'] == '1') || strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') == true) {
        if ((isset($_GET['debug']) && $_GET['debug'] == '1')) {
            //   if ((isset($_GET['debug']) && $_GET['debug'] == '1') || self::isWindows()) {
            echo "<pre>";
            print_r($_REQUEST);
            print_r($item);
            //编码
            echo json_encode($item);
        } else {
            //编码
            $item = json_encode($item,JSON_UNESCAPED_UNICODE);
            // 送出信息
            echo "{$item}";
        }
        exit;
    }

    public static function int2String($arr)
    {
        foreach ($arr as $k => $v) {
            if (is_int($v)) {
                $arr[$k] = (string)$v;
            } else if (is_array($v)) { //若为数组，则再转义.
                $arr[$k] = self::int2String($v);
            }
        }
        return $arr;
    }

    /** 根据user-agent取手机类型 */
    public static function getAppTypeByUa()
    {
        $tmp = 0;
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
            $tmp = 1;
        } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Android')) {
            $tmp = 2;
        }
        return $tmp;
    }

}