<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AizhanRules */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="aizhan-rules-form">

    <?php $form = ActiveForm::begin(); ?>




    <?=
    $form->field($model, 'category_id')->widget(\kartik\select2\Select2::classname(), [
        'options' => ['placeholder' => '请输入分类 ...'],
        'pluginOptions' => [
            'id' => new \yii\web\JsExpression("function(rs) {
                    return rs.taskId;
                }"),
            'placeholder' => 'search ...',
            'multiple' => false,
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
    <?= $form->field($model, 'site_url')->textInput() ?>

<!--    --><?//= $form->field($model, 'sort')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList(\common\models\AizhanRules::getStatus()) ?>
    <!---->
    <!--    --><? //= $form->field($model, 'domain_id')->textInput() ?>
    <!---->
    <!--    --><? //= $form->field($model, 'column_id')->textInput() ?>

<!--    --><?//= $form->field($model, 'max_search_num')->textInput(['value' => 10]) ?>
<!---->
<!--    --><?//= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

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


