<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PushArticle */

$this->title = \common\models\Domain::findOne($model->domain_id)->name . ' 更新文章 : ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Push Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="push-article-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
