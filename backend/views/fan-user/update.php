<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\FanUser */

$this->title = 'Update Fan User: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Fan Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fan-user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
