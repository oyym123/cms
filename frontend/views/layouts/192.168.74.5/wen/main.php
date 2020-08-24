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
<!DOCTYPE html
        PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <meta name="applicable-device" content="pc">
    <?php
    if (isset($this->params['list_tdk'])) {  //栏目列表页TDK
        $tdk = $this->params['list_tdk'];
        ?>

        <title><?= $tdk['title'] ?></title>
        <meta name="keywords" content="<?= $tdk['keywords'] ?>">

        <?php
    } elseif (isset($this->params['detail_tdk'])) { //详情页TDK
        $tdk = $this->params['detail_tdk'];
        ?>
        <title><?= $tdk['title'] ?></title>
        <meta name="keywords" content="<?= $tdk['keywords'] ?>">
        <meta name="description" content="<?= $tdk['description'] ?>">
        <meta property="og:type" content="<?= $tdk['og_type'] ?>"/>
        <meta property="og:title" content="<?= $tdk['og_title'] ?>"/>
        <meta property="og:description" content="<?= $tdk['og_description'] ?>"/>
        <meta property="og:image" content="<?= $tdk['og_image'] ?>"/>
        <meta property="og:release_date" content="<?= $tdk['og_release_date'] ?>"/>
        <?php
    } elseif (isset($this->params['tags_list_tdk'])) {   //标签列表页TDK
        $tdk = $this->params['tags_list_tdk'];
        ?>

        <title><?= $tdk['title'] ?></title>
        <meta name="description" content="<?= $tdk['intro'] ?>">
        <meta name="keywords" content="<?= $tdk['keywords'] ?>">

        <?php
    } elseif (isset($this->params['home_tdk'])) {   //首页TDK
        $tdk = $this->params['home_tdk'];
        ?>

        <title><?= $tdk['title'] ?></title>
        <meta name="description" content="<?= $tdk['intro'] ?>">
        <meta name="keywords" content="<?= $tdk['keywords'] ?>">

        <?php
    } elseif (isset($this->params['tags_tdk'])) {   //标签内页TDK
        $tdk = $this->params['tags_tdk'];
        ?>

        <title><?= $tdk['title'] ?></title>
        <meta name="description" content="<?= $tdk['intro'] ?>">
        <meta name="keywords" content="<?= $tdk['keywords'] ?>">
        <?php
    }
    ?>

    <link type="image/x-icon" rel="shortcut icon" href="/favicon.ico">
    <link href="/css/index.css" rel="stylesheet" type="text/css"/>
    <script src="https://cdn.bootcss.com/jquery/3.4.1/jquery.js"></script>
    <script type="text/javascript" src="/js/index.js"></script>
</head>

<body class="homepage">
<div class="wrap">
    <!-- 导航tab选项卡 -->
    <div class="nav">
        <nav class="nav_global">
            <ul>
                <li class="nav-frist-div"></li>
                <?php
                $column = explode('/', $_SERVER['REQUEST_URI'])[1];
                foreach (DomainColumn::getColumn(0, '', 'person') as $key => $item) {
                    $active = '';
                    if ($column == $item['name']) {
                        $active = 'curr active';
                        echo "<title>" . $item['zh_name'] . "</title>";
                    }

                    if ($item['name'] != 'home') {
                        ?>
                        <li id="tabnav_btn_<?= $key + 1 ?>" class="<?= $active ?>"><a href="<?= '/' . $item['name'] ?>"
                                                                                      title=""><?= $item['zh_name'] ?></a>
                        </li>
                        <?php
                    } else {
                        if ($column == '') {
                            $active = 'curr active';
                        }
                        echo '<li class="' . $active . '" id="tabnav_btn_0"><a href="/" title="">首页</a></li>';
                    }
                }
                ?>
                <li class="nav-last-div"><span class="js_register">注册</span><span class="js_login">登录</span></li>
            </ul>
        </nav>
    </div>
    <div class="header-null-box"></div>
    <div class="container">
        <main class="content-list">
            <?= $content ?>
            <div class="float-right-box">
                <ul class="content-right">
                    <li class="right-top-pd">
                        <div class="right-top"><span>热门文章</span><a href="/wen" title=""><em>更多>></em></a></div>
                    </li>

                    <li class="img-txt-ul">
                        <ul>
                            <?php
                            foreach (\common\models\PushArticle::hotArticle(10) as $item) {
                                ?>
                                <li>
                                    <a href="<?= $item['url'] ?>" title="">
                                        <img src="<?= $item['title_img'] ?>" alt="">
                                        <div>
                                            <em><?= $item['title'] ?></em>
                                            <p><?= $item['push_time'] ?></p>
                                        </div>
                                    </a>
                                </li>
                                <?php
                            } ?>
                        </ul>
                    </li>
                </ul>

                <ul class="content-right-mt">
                    <li class="right-top-pd">
                        <div class="right-top"><span>标签</span><a href="/label" title=""><em>更多>></em></a></div>
                    </li>
                    <li class="label-box">
                        <div class="label-list">
                            <?php
                            foreach (\common\models\BaiduKeywords::hotKeywords(100) as $item) {
                                ?>
                                <a href="<?= $item['url'] ?>" title="<?= $item['keywords'] ?>">
                                    <?= $item['keywords'] ?>
                                </a>
                            <?php } ?>

                        </div>
                    </li>
                </ul>

                <ul class="content-right-mt">
                    <li class="right-top-pd">
                        <div class="right-top"><span>最新文章</span><a href="/wen" title=""><em>更多>></em></a></div>
                    </li>
                    <li>
                        <ul class="article-list">
                            <?php
                            foreach (\common\models\PushArticle::newArticle(10) as $item) {
                                ?>
                                <li>
                                    <a href="<?= $item['url'] ?>" title="">
                                        <?= $item['title'] ?>
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </main>
    </div>
</div>
<br clear="all"/>
<!-- 页脚 -->
<footer class="footer">
    Copyright 2005-2017
</footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

