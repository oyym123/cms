<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DbName */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="db-name-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'baidu_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'baidu_password')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'baidu_account')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'domain')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

<!--    --><?//= $form->field($model, 'updated_at')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
