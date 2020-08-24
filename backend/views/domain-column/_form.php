<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DomainColumn */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="domain-column-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'domain_id')->dropDownList(\common\models\Domain::getDomianName(), ['prompt' => '--请选择域名--']) ?>


    <?= $form->field($model, 'type')->dropDownList(\common\models\DomainColumn::getType(), ['prompt' => '--请选择类型--']) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'zh_name')->textInput() ?>

    <?= $form->field($model, 'tags')->textInput(['maxlength' => true])->hint('多个用英文逗号隔开') ?>

    <?= $form->field($model, 'pc_show')->radioList(\common\models\Base::getBaseS()) ?>

    <?= $form->field($model, 'mobile_show')->radioList(\common\models\Base::getBaseS()) ?>

    <?= $form->field($model, 'is_change')->radioList(\common\models\Base::getBaseS()) ?>

    <?= $form->field($model, 'sort')->textInput() ?>

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'keywords')->textInput() ?>

    <?= $form->field($model, 'intro')->textarea() ?>

    <!---->
    <!---->

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
