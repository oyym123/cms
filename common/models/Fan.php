<?php


namespace common\models;

use frontend\controllers\BaseController;

class Fan extends BaseController
{
    /** 制定url规则 创建分类目录时触发 */
    public static function getRules($domainId)
    {
        Tools::DebugToolbarOff();
        //获取main.php 并且替换路由规则
        $main = file_get_contents(__DIR__ . '/../../frontend/config/main.php');
        $domain = Domain::findOne($domainId);

        $topDomain = $domain->name;

        $domainInfo = json_encode([
                'is_jump' => $domain->is_jump,
                'jump_url' => $domain->jump_url
            ]) . '  &end_url';


        $dataStr = "'rules' => [";

        foreach (DomainColumn::getColumn($domainId,'','fan') as $item) {
            if ($item['name'] == 'label') {
                $dataStr .= "
                '" . $item['name'] . "' => '/fan/tags-list',        
                '" . $item['name'] . "/index_<id:\d+>.html' => '/fan/tags-list',
                ";

                $dataStr .= "
                '" . $domain->start_tags . '<id:\d+>' . $domain->end_tags . "/' => '/fan/tags-detail',";

                $dataStr .= "
                'customize_<id:\d+>.html' => '/site/customize',";
            } else {
                $dataStr .= "
                '" . $item['name'] . "' => '/fan',
                '" . $item['name'] . "/<id:\d+>.html' => '/fan/detail',            
                '" . $item['name'] . "/index_<id:\d+>.html' => '/fan',
                ";
            }
        }

        $dataStr .= "
                'index_<id:\d+>.html' => '/site/index',
                'site.xml' => '/site/site-xml',
                'site.txt' => '/site/site-txt',
                'm_site.xml' => '/site/site-mxml',
                'm_site.txt' => '/site/site-mtxt',
                //end 正则注释识别 勿删" . $domainInfo;
        $res = preg_replace("@'rules'(.*)?//end 正则注释识别 勿删(.*)?&end_url@s", $dataStr, $main);
        file_put_contents(__DIR__ . '/../../frontend/config/' . $topDomain . '_main.php', $res);
    }

    public static function renderView($type)
    {
        Tools::DebugToolbarOff();
        $topDomain = Tools::getDoMain($_SERVER['HTTP_HOST']);
        list($layout, $domainPath) = Tools::setLayout($topDomain);
        $customizeIndex = str_replace('.html', '', explode('/', $_SERVER['REQUEST_URI'])[1]);
        if ($type == Template::TYPE_CUSTOMIZE) {
            $mPath = $domainPath . 'm_static/' . $customizeIndex;
            $pcpath = $domainPath . 'static/' . $customizeIndex;
            $render = Tools::jumpDomain($mPath, $pcpath, $_SERVER['HTTP_HOST']);

        } elseif ($type == Template::TYPE_INSIDE) {

            $tmpIndex = Template::getTmpIndex($type);
            preg_match('/(.*)?\d+/s', $customizeIndex, $inside);
            $inside[1] = Tools::cleanNumber($inside[1]);
            $mPath = $topDomain . '/' . $inside[1] . '/m_static/' . $tmpIndex;
            $pcpath = $topDomain . '/' . $inside[1] . '/static/' . $tmpIndex;
            $layout = str_replace($customizeIndex, $inside[1], $layout);
            $render = Tools::jumpDomain($mPath, $pcpath, $_SERVER['HTTP_HOST']);

        } else {
            $tmpIndex = Template::getTmpIndex($type);
            $mPath = $domainPath . 'm_static/' . $tmpIndex;
            $pcpath = $domainPath . 'static/' . $tmpIndex;
            $render = Tools::jumpDomain($mPath, $pcpath, $_SERVER['HTTP_HOST']);
        }
        return [$layout, $render];
    }


    /** 进行一切初始化操作 */
    public function init()
    {
        //规则配置
        self::getRules();

        //模板静态生成


    }

}