<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TemplateTpl */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="template-tpl-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cate')->radioList(\common\models\Template::getCate(), []) ?>

    <?= $form->field($model, 't_home')->dropDownList(\common\models\Template::getTemplate(\common\models\Template::TYPE_HOME), ['prompt' => '--请选择首页--'])->label('首页') ?>

    <?= $form->field($model, 't_list')->dropDownList(\common\models\Template::getTemplate(\common\models\Template::TYPE_LIST), ['prompt' => '--请选择列表页--']) ?>

    <?= $form->field($model, 't_inside')->dropDownList(\common\models\Template::getTemplate(\common\models\Template::TYPE_INSIDE), ['prompt' => '--请选择泛内页--']) ?>

    <?= $form->field($model, 't_detail')->dropDownList(\common\models\Template::getTemplate(\common\models\Template::TYPE_DETAIL), ['prompt' => '--请选择详情页--']) ?>

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
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>