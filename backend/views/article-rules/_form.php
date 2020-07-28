<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ArticleRules */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="article-rules-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'category_id')->textInput() ?>

    <?= $form->field($model, 'domain_id')->dropDownList(\common\models\Domain::getDomianName(), ['prompt' => '--请选择数据库--']) ?>

    <?= $form->field($model, 'column_id')->dropDownList([], ['prompt' => '--请选择栏目--']) ?>

    <?=
    $form->field($model, 'method_ids')->widget(\kartik\select2\Select2::classname(), [
        'options' => ['placeholder' => '请输入标签 ...'],
        'pluginOptions' => [
            'id' => new \yii\web\JsExpression("function(rs) {
                return rs.taskId;
            }"),
            'placeholder' => 'search ...',
            'multiple' => true,
            'allowClear' => true,
            'language' => [
                'errorLoading' => new \yii\web\JsExpression("function () { return 'Waiting...'; }"),
            ],
            'ajax' => [
                'url' => \yii\helpers\Url::to(['baidu-keywords/get-tags']),
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
    ]);
    ?>

    <?= $form->field($model, 'one_page_num_min')->textInput() ?>

    <?= $form->field($model, 'one_page_num_max')->textInput() ?>

    <?= $form->field($model, 'one_page_word_min')->textInput() ?>

    <?= $form->field($model, 'one_page_word_max')->textInput() ?>

    <?= $form->field($model, 'one_day_push_num')->textInput() ?>

    <?= $form->field($model, 'push_time_sm')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'push_time_bd')->textInput(['maxlength' => true]) ?>

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

<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript">
    $("#articlerules-domain_id").change(function () {
        var html = '';
        $.ajax({
            url: 'index.php?r=article-rules/get-class',
            type: 'GET',
            dataType: 'json',
            data: {domain_id: $("#articlerules-domain_id").find("option:selected").val()},
            success: function (msg) {
                $.each(msg, function (key, val) {
                    html += '<option value="' + key + '">' + val + '</option>';
                });
                $("#articlerules-column_id").html(html);
            }
        })
    });
</script>

