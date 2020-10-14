<?php

namespace common\component;

use common\models\Tools;
use yii;
use yii\base\ErrorHandler as BaseErrorHandler;


class ErrorHandler extends BaseErrorHandler
{

    public $errorView = '@app/views/site/error.php';

    public function renderException($exception)
    {
//        echo '<pre>';
//        print_r($exception->getMessage());
//        exit;

        $keyRefresh = $_SERVER['HTTP_HOST'] . 'refresh';
        //当出现这行报错信息的时候，表示跟阿里云RDS断连了，立马重新连接一次 不成功则重试5次
        if (strpos($exception->getMessage(), 'php_network_getaddresses') !== false) {
            $refresh = Yii::$app->session->get($keyRefresh);
            if (empty($refresh)) {
                Yii::$app->session->set($keyRefresh, 5);
            } else {
                Yii::$app->session->set($keyRefresh, $refresh - 1);
            }

            if ($refresh > 0) {
                echo '与数据服务器断开连接，正在重新连接。。。。 []~(￣▽￣)~*';
//                Tools::writeLog([$refresh], 'refresh.log');
                $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                echo '<script language="javascript" type="text/javascript">
                    window.location.href="' . $url . '";
                  </script>';
                exit;
            }
        }

        if (Yii::$app->request->getIsAjax()) {
            exit(json_encode(array('code' => $exception->getCode(), 'msg' => $exception->getMessage())));
        } else {
            if (PHP_OS == "Linux") {
                echo '<img src="/images/404/new404.png" style="width: 100%;height:100%">';
                exit;
            }
            
            if (strpos($exception->getMessage(), 'Page not found.') !== false) {
                echo '<img src="/images/404/new404.png" style="width: 100%;height:100%">';
                exit;
            } else {
                echo '<pre>';
                print_r($exception->getMessage());
                exit;
            }
//            //将500的代码，发送监控预警
//            if (!empty($exception->getCode()) && $exception->getCode() == 8) {
//                $params = [];
//                $params['projectName'] = "oct-youban";
//                $params['level'] = 5;
//                $params['title'] = "500：" . $exception->getMessage();
//                $params['value'] = $exception->getCode();
//                $params['message'] = $exception->getFile() . "：" . $exception->getLine();
//                $params['bizcode'] = 8;
//                $params['subcode'] = 8001;
//                echo '<pre>';
//                print_r($params);
//                exit;
//            }

            echo Yii::$app->getView()->renderFile($this->errorView, ['exception' => $exception,], $this);
        }
    }
}