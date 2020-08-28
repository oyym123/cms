<?php
/* @var $this \yii\web\View */

/* @var $content string */

use common\models\DomainColumn;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
$domain = \common\models\Domain::getDomainInfo();
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8"/>
        <?php if (isset($this->params['list_tdk'])) {  //栏目列表页TDK
            $tdk = $this->params['list_tdk']; ?>
            <title><?= $tdk['title'] ?></title>
            <meta name="keywords" content="<?= $tdk['keywords'] ?>">
            <meta name="description" content="<?= $tdk['intro'] ?>">
        <?php } elseif (isset($this->params['detail_tdk'])) { //详情页TDK
            $tdk = $this->params['detail_tdk']; ?>
            <title><?= $tdk['title'] ?></title>
            <meta name="keywords" content="<?= $tdk['keywords'] ?>">
            <meta name="description" content="<?= $tdk['description'] ?>">
            <meta property="og:type" content="<?= $tdk['og_type'] ?>"/>
            <meta property="og:title" content="<?= $tdk['og_title'] ?>"/>
            <meta property="og:description" content="<?= $tdk['og_description'] ?>"/>
            <meta property="og:image" content="<?= $tdk['og_image'] ?>"/>
            <meta property="og:release_date" content="<?= $tdk['og_release_date'] ?>"/>
        <?php } elseif (isset($this->params['tags_list_tdk'])) {   //标签列表页TDK
            $tdk = $this->params['tags_list_tdk']; ?>
            <title><?= $tdk['title'] ?></title>
            <meta name="description" content="<?= $tdk['intro'] ?>">
            <meta name="keywords" content="<?= $tdk['keywords'] ?>">
        <?php } elseif (isset($this->params['home_tdk'])) {   //首页TDK
            $tdk = $this->params['home_tdk']; ?>
            <title><?= $tdk['title'] ?></title>
            <meta name="description" content="<?= $tdk['intro'] ?>">
            <meta name="keywords" content="<?= $tdk['keywords'] ?>">
        <?php } elseif (isset($this->params['tags_tdk'])) {   //标签内页TDK
            $tdk = $this->params['tags_tdk']; ?>
            <title><?= $tdk['title'] ?></title>
            <meta name="description" content="<?= $tdk['intro'] ?>">
            <meta name="keywords" content="<?= $tdk['keywords'] ?>">
        <?php } ?>
        <meta name="applicable-device" content="pc">
        <link type="image/x-icon" rel="shortcut icon" href="/favicon.ico">
        <link rel="stylesheet" href="css/aa.css"/>
        <link rel="stylesheet" href="./css/font-awesome-4.7.0/css/font-awesome.min.css">
    </head>
<body>
    <div class="xx-header-ding">
        <div class="xx-header-box">
            <input placeholder="文章/用户">
            <a href="#" title="">搜索答案</a></div>
    </div>
    <div class="xx-header-tab">
        <div class="xx-header-bob">
            <ul>
                <li class="xx-header-li"><a href="/" title="首页" class="tab-a">首页</a></li>
                <?php
                $column = explode('/', $_SERVER['REQUEST_URI'])[1];
                foreach (DomainColumn::getColumn(0, '', 'person') as $key => $item) { ?>
                    <li class="xx-header-li"><a href="<?= $item['name'] ?>" title="<?= $item['zh_name'] ?>"
                                                class="tab-a"><?= $item['zh_name'] ?></a></li>
                <?php } ?>
            </ul>
            <div class="xx-header-user"><i class="fa fa-user-o" aria-hidden="true"><a href="#" title=""
                                                                                      class="js_login1">我的</a></i></div>
        </div>
    </div>

<div class="xx-main-box">
    <div class="xx-le-box">
        <?= $content ?>
    </div>
    <div class="xx-rr-box">
    <section class="xx-rr-list">
    <h3 class="xx-rr-tutl">热门文章<a href="#" title="">更多 ></a></h3>
    <ul><?php foreach (\common\models\PushArticle::hotArticle(10) as $item) { ?>
    <li class="xx-rr-li-box"><a href="<?= $item['url'] ?>" title="" class="xxrr-li-a"><img
                    src="<?= $item['title_img'] ?>" alt="<?= $item['title'] ?>">
            <h2><?= $item['title'] ?></h2>
        </a> <a href="#" title="" class="xxrr-liaa">用户名</a> <br clear="all"/>
    </li>
        <?php } ?>
    </ul>
    </section>
    <section class="xx-rr-list">
        <h3 class="xx-rr-tutl">热门专题<a href="#" title="">更多 ></a></h3>
        <?php foreach (\common\models\BaiduKeywords::hotKeywords(100) as $item) { ?>
            <a href="<?= $item['url'] ?>" title="<?= $item['keywords'] ?>"
               class="xx-rr-tag"><?= $item['keywords'] ?></a><?php } ?>
    </section>
    <section class="xx-rr-list">
        <h3 class="xx-rr-tutl">排行榜<a href="#" title="">更多 ></a></h3>
        <ul>
            <?php foreach (\common\models\PushArticle::newArticle(10) as $item) { ?>
                <li class="xx-rr-php"><a href="<?= $item['url'] ?>"
                                         title="<?= $item['title'] ?>"><span>1</span><?= $item['title'] ?></a>
                </li><?php } ?>
        </ul>
    </section>
    </div>
    </div>
    <br clear="all"/>
    <div class="xx-footer">
        <p>CopyRight &copy; 2020 零王教育网</p>
    </div>
    <!-- 登录弹窗1 -->
    <div class="js_mask_login">
        <div class="loginbx logbx">
            <div class="icolse"><i class="fa fa-close cr close"></i></div>
            <p class="text-show"><b>登录</b></p>
            <div>
                <input type="text" name="username" placeholder="请输入用户名..."/>
            </div>
            <div>
                <input type="password" name="password" placeholder="请输入密码..."/>
            </div>
            <div class="maskbtbx">
                <button class="js_res">注册点击这里</button>
                <button class="maskbt">登录</button>
            </div>
        </div>
        <div class="loginbx resbx">
            <div class="icolse"><i class="fa fa-close cr close"></i></div>
            <p class="text-show"><b>注册</b></p>
            <div>
                <input type="text" name="username" placeholder="请输入用户名..."/>
            </div>
            <div>
                <input type="password" name="password" placeholder="请输入密码..."/>
            </div>
            <div>
                <input type="password" name="password" placeholder="请确认密码..."/>
            </div>
            <div class="maskbtbx">
                <button class="js_login">登录点击这里</button>
                <button class="maskbt">注册</button>
            </div>
        </div>
    </div>
    <script src="./js/jquery.min.js"></script>
    <script src="./js/aa.js"></script>
<?php $this->endBody() ?>
    </body>
    </html>
    <?php $this->endPage() ?>