<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\BaiduKeywords */

$this->title = 'Create Baidu Keywords';
$this->params['breadcrumbs'][] = ['label' => 'Baidu Keywords', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="baidu-keywords-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
