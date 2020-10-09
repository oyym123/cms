<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */

/* @var $exception Exception */

use yii\helpers\Html;
$this->title = $name;
?>

<?php
$this->context->layout = false; //不使用布局,或者改为自己所需要使用的布局
?>
<div class="site-error">
    <!--    <h1>页面消失了</h1>-->
    <img src="/images/404/new404.png" style="width: 100%;height:100%">
</div>
