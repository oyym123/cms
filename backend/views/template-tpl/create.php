<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TemplateTpl */

$this->title = '创建模组';
$this->params['breadcrumbs'][] = ['label' => 'Template Tpls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="template-tpl-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
