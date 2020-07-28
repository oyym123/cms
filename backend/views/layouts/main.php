<?php

/* @var $this \yii\web\View */

/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    $menuItems = [
        ['label' => '泛目录域名', 'url' => ['/domain/index']],
        ['label' => '泛目录类目', 'url' => ['/domain-column/index']],
        ['label' => '模板', 'url' => ['/template/index']],
        ['label' => '类目模板', 'url' => ['/domain-tpl/index']],
        ['label' => '泛目录类型', 'url' => ['/category/index']],
        ['label' => '泛目录方法', 'url' => ['/article-way/index']],
        ['label' => '泛目录规则', 'url' => ['/article-rules/index']],
        ['label' => '泛目录文章', 'url' => ['/push-article/index']],

        ['label' => '队列', 'url' => ['/rabbitemq/index']],
        ['label' => '黑帽文章', 'url' => ['/black-article/index']],
        ['label' => '白帽文章', 'url' => ['/white-article/index']],
        ['label' => '百度营销词 & tags', 'url' => ['/baidu-keywords/index']],
        ['label' => '下拉框关键词', 'url' => ['/long-keywords/index']],
        ['label' => '爱站关键词', 'url' => ['/keywords/index']],
        ['label' => 'CMS 推送日志', 'url' => ['/mip-flag/index']],
        ['label' => 'CMS 数据库', 'url' => ['/db-name/index']],
        ['label' => 'Home', 'url' => ['/site/index']],
    ];

    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
