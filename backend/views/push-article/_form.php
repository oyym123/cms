<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PushArticle */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="push-article-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true])->hint('副词跟在逗号后面') ?>

    <?= $form->field($model, 'domain_id')->label('')->hiddenInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'intro')->textarea(['maxlength' => true]) ?>

    <!--    --><? //= $form->field($model, 'from_path')->textInput(['maxlength' => true]) ?>

<!--    --><?//=
//    $form->field($model, 'key_id')->widget(\kartik\select2\Select2::classname(), [
//        'options' => ['placeholder' => '请输入标签 ...'],
//        'pluginOptions' => [
//            'id' => new \yii\web\JsExpression("function(rs) {
//                return rs.taskId;
//            }"),
//            'placeholder' => 'search ...',
//            'multiple' => true,
//            'allowClear' => true,
//            'language' => [
//                'errorLoading' => new \yii\web\JsExpression("function () { return 'Waiting...'; }"),
//            ],
//            'ajax' => [
//                'url' => \yii\helpers\Url::to(['baidu-keywords/get-tags']),
//                'dataType' => 'json',
//                'data' => new \yii\web\JsExpression('function(params) {
//                return {q:params.term}; }')
//            ],
//            'escapeMarkup' => new \yii\web\JsExpression('function (markup) {
//             return markup; }'),
//            'templateResult' => new \yii\web\JsExpression('function(res) {
//             return res.text; }'),
//            'templateSelection' => new \yii\web\JsExpression('function (res) {
//             return res.text; }'),
//        ],
//    ])->hint('词库里没有嘛？');
//    ?>
<!---->
<!--    <p>-->
<!--        --><?//= Html::a('添加新tags', ['baidu-keywords/create?type_id=' . $model->key_id], ['class' => 'btn btn-success', 'target' => '_blank']) ?>
<!--    </p>-->

    <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title_img')->textInput(['maxlength' => true]) ?>

    <!--    --><? //= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'content')->widget('common\widgets\ueditor\Ueditor', [
        'value' => 1
//        'options'=>[
//            'initialFrameWidth' => 850,
//        ]
    ]) ?>

    <?= $form->field($model, 'push_time')->textInput() ?>

    <!--    --><? //= $form->field($model, 'created_at')->textInput() ?>
    <!---->
    <!--    --><? //= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
