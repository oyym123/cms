<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WhiteArticle */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="white-article-form">

    <?php $form = ActiveForm::begin([ 'options' => ['enctype' => 'multipart/form-data']]); ?>

    <!--    --><? //= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <!--    --><? //= $form->field($model, 'key_id')->textInput() ?>

    <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>

    <!--    --><? //= $form->field($model, 'cut_word')->textInput(['maxlength' => true]) ?>

    <!--    --><? //= $form->field($model, 'image_urls')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'db_id')->dropDownList(\common\models\DbName::getDbName(), ['prompt' => '--请选择数据库--']) ?>

    <?= $form->field($model, 'db_class_id')->dropDownList([], ['prompt' => '--请选择栏目--']) ?>
    <?= $form->field($model, 'from_path')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'title_img')->widget(\kartik\file\FileInput::classname(), [
        'options' => ['multiple' => false],
        'pluginOptions' => [
            // 是否展示预览图
            'initialPreviewAsData' => true,
            // 是否显示移除按钮，指input上面的移除按钮，非具体图片上的移除按钮
            'showRemove' => true,
            // 是否显示上传按钮，指input上面的上传按钮，非具体图片上的上传按钮
            'showUpload' => false,
        ],
    ])->fileInput([]);
    ?>

    <?=

    $form->field($model, 'db_tags_id')->widget(\kartik\select2\Select2::classname(), [
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

    <!--    --><? //= $form->field($model, 'word_count')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList(\common\models\WhiteArticle::getStatus(), ['value' => \common\models\WhiteArticle::STATUS_ENABLE]) ?>

    <?= $form->field($model, 'content')->widget('common\widgets\ueditor\Ueditor', [
//        'options'=>[
//            'initialFrameWidth' => 850,
//        ]
    ]) ?>

    <!--    --><? //= $form->field($model, 'created_at')->textInput() ?>

    <!--    --><? //= $form->field($model, 'type')->dropDownList(\common\models\WhiteArticle::getType(), ['value' => \common\models\WhiteArticle::TYPE_MANUALLY_WRITTEN]) ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
<script>
    $("#whitearticle-db_id").change(function () {
        var html = '';
        $.ajax({
            url: 'index.php?r=white-article/get-class',
            type: 'GET',
            dataType: 'json',
            data: {db_name: $("#whitearticle-db_id").find("option:selected").text()},
            success: function (msg) {
                $.each(msg, function (key, val) {
                    html += '<option value="' + key + '">' + val + '</option>';
                });
                $("#whitearticle-db_class_id").html(html);
            }
        })
    });
</script>
