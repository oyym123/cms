<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LongKeywords */

$this->title = 'Create Long Keywords';
$this->params['breadcrumbs'][] = ['label' => 'Long Keywords', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="long-keywords-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
