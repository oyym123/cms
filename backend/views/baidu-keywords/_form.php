<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BaiduKeywords */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="baidu-keywords-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
