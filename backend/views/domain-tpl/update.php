<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DomainTpl */

$this->title = 'Update Domain Tpl: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Domain Tpls', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="domain-tpl-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
