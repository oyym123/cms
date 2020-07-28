<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DomainColumn */

$this->title = 'Create Domain Column';
$this->params['breadcrumbs'][] = ['label' => 'Domain Columns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="domain-column-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
