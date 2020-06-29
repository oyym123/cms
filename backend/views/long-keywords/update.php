<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LongKeywords */

$this->title = 'Update Long Keywords: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Long Keywords', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="long-keywords-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
