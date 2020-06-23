<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MipFlag */

$this->title = 'Update Mip Flag: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mip Flags', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mip-flag-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
