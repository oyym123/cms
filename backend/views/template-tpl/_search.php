<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\TemplateTplSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="template-tpl-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cate') ?>

    <?= $form->field($model, 't_customize') ?>

    <?= $form->field($model, 't_tags') ?>

    <?= $form->field($model, 't_detail') ?>

    <?php // echo $form->field($model, 't_list') ?>

    <?php // echo $form->field($model, 't_common') ?>

    <?php // echo $form->field($model, 't_home') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 't_ids') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
