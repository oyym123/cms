<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BaiduKeywords */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="baidu-keywords-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->dropDownList([\common\models\DomainColumn::getType()], ['prompt' => '--请选择类型--']) ?>

    <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>


<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript">
    $("#baidukeywords-domain_id").change(function () {
        var html = '';
        $.ajax({
            url: '/index.php/article-rules/get-class',
            type: 'GET',
            dataType: 'json',
            data: {domain_id: $("#baidukeywords-domain_id").find("option:selected").val()},
            success: function (msg) {
                $.each(msg, function (key, val) {
                    html += '<option value="' + key + '">' + val + '</option>';
                });
                $("#baidukeywords-column_id").html(html);
            }
        })
    });
</script>

