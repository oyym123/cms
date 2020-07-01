<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AizhanRules */

$this->title = '创建爱站规则';
$this->params['breadcrumbs'][] = ['label' => 'Aizhan Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="aizhan-rules-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
