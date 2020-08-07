<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DomainTpl */

$this->title = '合成';
$this->params['breadcrumbs'][] = ['label' => 'Domain Tpls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="domain-tpl-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
