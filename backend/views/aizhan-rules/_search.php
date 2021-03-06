<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\AizhanRulesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="aizhan-rules-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'site_url') ?>

    <?= $form->field($model, 'category_id') ?>

    <?= $form->field($model, 'sort') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'domain_id') ?>

    <?php // echo $form->field($model, 'column_id') ?>

    <?php // echo $form->field($model, 'max_search_num') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
