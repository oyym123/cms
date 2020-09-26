<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SiteMap */

$this->title = 'Update Site Map: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Site Maps', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="site-map-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
