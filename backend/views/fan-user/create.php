<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\FanUser */

$this->title = 'Create Fan User';
$this->params['breadcrumbs'][] = ['label' => 'Fan Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fan-user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
