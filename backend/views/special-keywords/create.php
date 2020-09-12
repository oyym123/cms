<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SpecialKeywords */

$this->title = '创建敏感词';
$this->params['breadcrumbs'][] = ['label' => 'Special Keywords', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="special-keywords-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
