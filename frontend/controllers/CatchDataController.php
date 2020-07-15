<?php


namespace frontend\controllers;

use Yii;
use common\models\WebDriver;
use common\models\WhiteArticle;
use yii\web\Controller;

class CatchDataController extends Controller
{
    public function actionIndex()
    {
        echo '<h1>欢迎来到 爬虫世界！</h1>';
    }

    /** webdriver 工具爬取数据 */
    public function actionSgdata()
    {
        WebDriver::getSgwx();
    }

    /** 测试文章保存 */
    public function actionTestSave()
    {
        $data = [
            'url' => 'https://mp.weixin.qq.com/s?src=11&timestamp=1594692911&ver=2459&signature=Hi4fsRM8QoIaBc8A6-*CmpzUuxF4eZqU1E-*vxIsatlyW0AtdxAAjYfK0eCWp7rn3Oc3VySyNAy15rwwPLtX9mj5ToFvRNToGxZ-me4zL3JEOf*mdOTGZ9alXEEjpTOZ&new=1',
            'img' => 'https://mmbiz.qpic.cn/mmbiz_jpg/v15rf2Bd8vY2zZbCuuQ5mExaS5nPygEibasdyMNPcE4M2Het9MyAQafGFXicxmuLZTyb171aL6l7OUZ2PfBn9ChQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1&wx_co=1',
            'text' => '英语48个音标发音(附详细图解+视频),资料宝贵,必须珍藏! 单元音(长元音)单元音(短元音)双元音爆破音(浊辅音)爆破音(清辅音)摩擦音(清辅音)摩擦音(浊辅音)破擦音(清辅音)破擦音(浊辅音)鼻音(浊辅音)边辅音(浊辅音)半元音(浊辅音) 英语口语2020-6-18 )',
        ];
        $url = 'https://mp.weixin.qq.com/s?src=11&timestamp=1594697024&ver=2459&signature=uZXJQpOqhZ5eMCEE73PlBXHMdKjqdpa7GLqRxyO7Usexa5nWSSItRkpaG*MZWtpPNYr5GdlQeJovgfEDRnahe0635oCCJXZ3KviDnmAjCVWE0H5R48D45jamws*JMDJv&new=1';
        WebDriver::saveArticle($url, $data);
    }

    /** 将本地抓取的数据实时传到线上 */
    public function actionUploadArticle()
    {
        WhiteArticle::createOne(Yii::$app->request->post());
    }
}