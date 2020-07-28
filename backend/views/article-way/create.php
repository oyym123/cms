<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ArticleWay */

$this->title = 'Create Article Way';
$this->params['breadcrumbs'][] = ['label' => 'Article Ways', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-way-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
