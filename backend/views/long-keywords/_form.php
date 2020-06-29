<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LongKeywords */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="long-keywords-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'm_down_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'm_seach_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'm_related_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pc_down_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pc_search_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pc_related_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'key_id')->textInput() ?>

    <?= $form->field($model, 'key_search_num')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'from')->textInput() ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
