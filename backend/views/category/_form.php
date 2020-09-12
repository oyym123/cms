<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'en_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'intro')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pid')->label('一级父类')->dropDownList(\common\models\Category::getCate(0), ['prompt' => '--请选择一级父类--']) ?>

    <?= $form->field($model, 'pid')->label('二级')->dropDownList(\common\models\Category::getCate(), ['prompt' => '--请选择二级父类--']) ?>

    <?= $form->field($model, 'pid')->label('三级')->dropDownList(\common\models\Category::getCate(), ['prompt' => '--请选择三级父类--']) ?>

    <?= $form->field($model, 'pid')->label('四级')->dropDownList(\common\models\Category::getCate(), ['prompt' => '--请选择三四级父类--']) ?>

    <?= $form->field($model, 'level')->textInput() ?>

    <!--    --><? //= $form->field($model, 'user_id')->textInput() ?>
    <!---->
    <!--    --><? //= $form->field($model, 'status')->textInput() ?>
    <!---->
    <!--    --><? //= $form->field($model, 'created_at')->textInput() ?>
    <!---->
    <!--    --><? //= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
