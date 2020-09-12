<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pid')->label('一级父类')->dropDownList(\common\models\Category::getCatePid(0)) ?>

    <?= $form->field($model, 'pid2')->label('二级')->dropDownList(\common\models\Category::getCate($model->pid3, 0)) ?>

    <?= $form->field($model, 'pid3')->label('三级')->dropDownList(\common\models\Category::getCate($model->pid4, 0)) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'intro')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->radioList(\common\models\Base::getBaseStatus()) ?>

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
<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script>
    $("#category-pid").change(function () {
        var html = '';
        $.ajax({
            url: '/index.php/category/get-cate',
            type: 'GET',
            dataType: 'json',
            data: {id: $("#category-pid").find("option:selected").val()},
            success: function (msg) {
                $.each(msg, function (key, val) {
                    html += '<option value="' + key + '">' + val + '</option>';
                });
                $("#category-pid2").html(html);
            }
        })
    });

    $("#category-pid2").change(function () {
        var html = '';
        $.ajax({
            url: '/index.php/category/get-cate',
            type: 'GET',
            dataType: 'json',
            data: {id: $("#category-pid2").find("option:selected").val()},
            success: function (msg) {
                $.each(msg, function (key, val) {
                    html += '<option value="' + key + '">' + val + '</option>';
                });
                $("#category-pid3").html(html);
            }
        })
    });

    $("#category-pid3").change(function () {
        var html = '';
        $.ajax({
            url: '/index.php/category/get-cate',
            type: 'GET',
            dataType: 'json',
            data: {id: $("#category-pid3").find("option:selected").val()},
            success: function (msg) {
                $.each(msg, function (key, val) {
                    html += '<option value="' + key + '">' + val + '</option>';
                });
                $("#category-pid4").html(html);
            }
        })
    });

</script>
