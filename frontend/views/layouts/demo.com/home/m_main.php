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
?>

<?php $this->beginPage() ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>华中教育网</title>
<meta name="description" content="华中教育网为您提供中国教育考试相关信息资讯大全，高等教育学生信息网，全国教育大会，基础教育资源应用平台等教育相关信息。"/>
<meta name="keywords" content="帝国网站管理系统,EmpireCMS"/>
<meta name="applicable-device" content="pc">
<link type="image/x-icon" rel="shortcut icon" href="/favicon.ico">
<link href="/css/m_index.css" rel="stylesheet" type="text/css"/>
<script src="https://cdn.bootcss.com/jquery/3.4.1/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
<head>
    <meta charset="UTF-8">
    <title>首页</title>
    <meta name="description" content="华中教育网为您提供中国教育考试相关信息资讯大全，高等教育学生信息网，全国教育大会，基础教育资源应用平台等教育相关信息。">
    <meta name="keywords" content="">
    <meta name="viewport"
          content="width=device-width, initial-scale=1,maximum-scale=1,maximum-scale=1, minimum-scale=1, user-scalable=no">
    <meta name="applicable-device" content="mobile">
    <link type="image/x-icon" rel="shortcut icon" href="/favicon.ico">
    <link rel="stylesheet" href="/css/index.css">
    <script src="/js/jquery.min.js"></script>
    <script src="/js/index.js"></script>
</head>

<body>
<nav class="fixed-nav">
    <img src="/images/logo.jpg" alt="">
    <div class="login">
        <a class="color-fff" href="#" rel="nofollow">登录</a>
        <a class="color-fff" href="#" rel="nofollow">注册</a>
    </div>
</nav>
<!-- 顶部tab导航栏模块 -->
<nav class="label-wrap">
    <ul class="text-center">

        <?php
        $column = explode('/', $_SERVER['REQUEST_URI'])[1];
        foreach (DomainColumn::getColumn(0,'','person') as $key => $item) {
            $active = '';
            if ($column == $item['name']) {
                $active = 'active';
            }

            if ($item['name'] != 'home') {
                ?>
                <li  class="<?= $active ?>"><a href="<?= '/' . $item['name'] ?>"
                                                                              title=""><?= $item['zh_name'] ?></a>
                </li>
                <?php
            } else {
                if ($column == '') {
                    $active = 'curr active';
                }
                echo '<li class="' . $active . '" ><a href="/" title="">首页</a></li>';
            }
        }
        ?>


    </ul>
</nav>
</header>
<!-- 下拉刷新 -->
<div class="loading-wrap"></div>
<!-- 主要内容模块 -->
<main class="main">
    <?= $content ?>
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

