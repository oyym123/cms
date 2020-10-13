<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BaiduKeywords */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="baidu-keywords-form">
    <?php $form = ActiveForm::begin(); ?>

    <?=
    $form->field($model, 'type_id')->widget(\kartik\select2\Select2::classname(), [
        'options' => ['placeholder' => '请输入分类 ...'],
        'pluginOptions' => [
            'id' => new \yii\web\JsExpression("function(rs) {
                    return rs.taskId;
                }"),
            'placeholder' => 'search ...',

            'allowClear' => true,
            'language' => [
                'errorLoading' => new \yii\web\JsExpression("function () { return 'Waiting...'; }"),
            ],
            'ajax' => [
                'url' => \yii\helpers\Url::to(['category/get-category']),
                'dataType' => 'json',
                'data' => new \yii\web\JsExpression('function(params) {
                    return {q:params.term}; }')
            ],
            'escapeMarkup' => new \yii\web\JsExpression('function (markup) {
                 return markup; }'),
            'templateResult' => new \yii\web\JsExpression('function(res) {
                 return res.text; }'),
            'templateSelection' => new \yii\web\JsExpression('function (res) {
                 return res.text; }'),
        ],
    ])->hint('输入c 检索自定义页面 添加单个');

    ?>


    <?= $form->field($model, 'keywords')->textarea(['rows' => 10]) ?>

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

