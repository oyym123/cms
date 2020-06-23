<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DbLog */

$this->title = 'Create Db Log';
$this->params['breadcrumbs'][] = ['label' => 'Db Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="db-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
