<?php


namespace common\models;

class Fan extends \yii\db\ActiveRecord
{
    /** 制定url规则 创建分类目录时触发 */
    public static function getRules()
    {
        //获取main.php 并且替换路由规则
        $main = file_get_contents(__DIR__ . '/../../frontend/config/main.php');

        $dataStr = "'rules' => [";
        foreach (DomainColumn::getColumn() as $item) {
            $dataStr .= "
                '" . $item['name'] . "' => '/fan',
                '" . $item['name'] . "/<id:\d+>.html' => '/fan/detail',
                '" . $item['name'] . "/index_<id:\d+>.html' => '/fan',
                ";
        }

        $dataStr .= "
                'index_<id:\d+>.html' => '/site/index',
                //end 正则注释识别 勿删";
        $res = preg_replace("@'rules'(.*)?//end 正则注释识别 勿删@s", $dataStr, $main);
        file_put_contents(__DIR__ . '/../../frontend/config/main.php', $res);
    }

    /** 进行一切初始化操作 */
    public function init()
    {
        //规则配置
        self::getRules();

        //模板静态生成
        

    }

}