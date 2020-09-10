<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DomainTpl */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="domain-tpl-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'domain_id')->dropDownList(\common\models\Domain::getDomianName(), ['prompt' => '--请选择数据库--']) ?>

    <?= $form->field($model, 'column_id')->dropDownList([\common\models\DomainColumn::getColumnData($model->domain_id)], ['prompt' => '--请选择栏目--']) ?>

    <?= $form->field($model, 'tpl_id')->dropDownList(\common\models\TemplateTpl::getTpl(), ['prompt' => '--请选择套装--']) ?>

    <?= $form->field($model, 'cate')->radioList(\common\models\Template::getCate(), []) ?>

    <?= $form->field($model, 't_home')->dropDownList(\common\models\Template::getTemplate(\common\models\Template::TYPE_HOME), ['prompt' => '--请选择首页--'])->label('首页') ?>

    <?= $form->field($model, 't_list')->dropDownList(\common\models\Template::getTemplate(\common\models\Template::TYPE_LIST), ['prompt' => '--请选择列表页--']) ?>

    <?= $form->field($model, 't_detail')->dropDownList(\common\models\Template::getTemplate(\common\models\Template::TYPE_DETAIL), ['prompt' => '--请选择详情页--']) ?>

    <?= $form->field($model, 't_inside')->dropDownList(\common\models\Template::getTemplate(\common\models\Template::TYPE_INSIDE), ['prompt' => '--请选择泛内页--']) ?>

    <?= $form->field($model, 't_common')->dropDownList(\common\models\Template::getTemplate(\common\models\Template::TYPE_COMMON), ['prompt' => '--请选择详情页--']) ?>

    <?= $form->field($model, 't_tags')->dropDownList(\common\models\Template::getTemplate(\common\models\Template::TYPE_TAGS), ['prompt' => '--请选择标签页--']) ?>

    <?=
    $form->field($model, 't_customize')->widget(\kartik\select2\Select2::classname(), [
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
                'url' => \yii\helpers\Url::to(['template/get-template']),
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
    ])->hint('输入c 检索自定义页面 可添加多个');

    ?>

    <!--    --><? //= $form->field($model, 'type')->textInput() ?>

    <!--    --><? //= $form->field($model, 'status')->textInput() ?>
    <!---->
    <!--    --><? //= $form->field($model, 'user_id')->textInput() ?>
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
<script type="text/javascript">
    $("#domaintpl-domain_id").change(function () {
        var html = '';
        $.ajax({
            url: '/index.php/article-rules/get-class',
            type: 'GET',
            dataType: 'json',
            data: {domain_id: $("#domaintpl-domain_id").find("option:selected").val()},
            success: function (msg) {
                $.each(msg, function (key, val) {
                    html += '<option value="' + key + '">' + val + '</option>';
                });
                $("#domaintpl-column_id").html(html);
            }
        })
    });

    $("#domaintpl-tpl_id").change(function () {
        var html = '';
        $("#domaintpl-t_inside").hide();
        $("#domaintpl-t_home").hide();
        $("#domaintpl-t_detail").hide();
        $("#domaintpl-t_common").hide();
        $("#domaintpl-t_tags").hide();
        $("#domaintpl-t_list").hide();
        $("#domaintpl-t_customize").hide();
    });
</script>

