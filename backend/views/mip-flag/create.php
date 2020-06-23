<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MipFlag */

$this->title = 'Create Mip Flag';
$this->params['breadcrumbs'][] = ['label' => 'Mip Flags', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mip-flag-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
