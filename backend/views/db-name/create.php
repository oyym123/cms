<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DbName */

$this->title = 'Create Db Name';
$this->params['breadcrumbs'][] = ['label' => 'Db Names', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="db-name-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
