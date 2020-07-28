<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Rabbitemq */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rabbitemq-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'intro')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(\common\models\Rabbitemq::getType()) ?>

    <?= $form->field($model, 'host')->textInput(['maxlength' => true, 'value' => '127.0.0.1']) ?>

    <?= $form->field($model, 'port')->textInput(['maxlength' => true, 'value' => 5672]) ?>

    <?= $form->field($model, 'user')->textInput(['maxlength' => true, 'value' => 'guest']) ?>

    <?= $form->field($model, 'pwd')->textInput(['maxlength' => true, 'value' => 'guest']) ?>

    <?= $form->field($model, 'vhost')->textInput(['maxlength' => true, 'value' => '/']) ?>

    <?= $form->field($model, 'exchange')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'queue')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(\common\models\Rabbitemq::getStatus()) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
