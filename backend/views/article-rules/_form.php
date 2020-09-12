<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ArticleRules */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="article-rules-form">

    <?php $form = ActiveForm::begin();

    if (strpos(Yii::$app->request->url, 'create') !== false) {
        $model->one_page_num_min = 10;
        $model->one_page_num_max = 20;
        $model->one_page_word_min = 20;
        $model->one_page_word_max = 5000;
        $model->one_day_push_num = 50;
        $model->push_time_sm = '12:00';
        $model->push_time_bd = '12:00';
    }
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?=
    $form->field($model, 'category_id')->widget(\kartik\select2\Select2::classname(), [
        'options' => ['placeholder' => '请输入分类 ...'],
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

    <?= $form->field($model, 'domain_id')->dropDownList(\common\models\Domain::getDomianName(), ['prompt' => '--请选择数据库--']) ?>

    <?= $form->field($model, 'column_id')->dropDownList([\common\models\DomainColumn::getColumnData($model->domain_id)], ['prompt' => '--请选择栏目--']) ?>

    <?= $form->field($model, 'method_ids')->checkboxList(\common\models\ArticleWay::getWayName(), ['class' => 'label-group']) ?>

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

    <?= $form->field($model, 'status')->radioList(\common\models\Base::getBaseStatus(), ['maxlength' => true])->hint('测试成功改为正常') ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript">
    $("#articlerules-domain_id").change(function () {
        var html = '';
        $.ajax({
            url: '/index.php/article-rules/get-class',
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

