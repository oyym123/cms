<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DomainColumn */

$this->title = 'Update Domain Column: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Domain Columns', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="domain-column-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
