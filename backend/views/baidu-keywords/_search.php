<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\BaiduKeywordsSearch */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="baidu-keywords-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>


    <?= $form->field($model, 'domain_id')->dropDownList(\common\models\Domain::getDomianName(), ['prompt' => '--请选择数据库--']) ?>

    <?= $form->field($model, 'column_id')->dropDownList([\common\models\DomainColumn::getColumnData($model->domain_id)], ['prompt' => '--请选择栏目--']) ?>


    <?php
    echo '<label class="control-label" for="longkeywordssearch-key_search_num">最小m_pv</label>';
    echo '<input type="text" id="longkeywordssearch-key_search_num" class="form-control" name="m_pv_min" value="">';
    echo '<br/>';
    ?>

    <?php
    echo '<label class="control-label" for="longkeywordssearch-key_search_num">最大m_pv</label>';
    echo '<input type="text" id="longkeywordssearch-key_search_num" class="form-control" name="m_pv_max" value="">';
    echo '<br/>';
    ?>



<!--    --><?//= $form->field($model, 'id') ?>
<!---->
<!--    --><?//= $form->field($model, 'keywords') ?>
<!---->
<!--    --><?//= $form->field($model, 'from_keywords') ?>
<!---->
<!--    --><?//= $form->field($model, 'pc_show_rate') ?>
<!---->
<!--    --><?//= $form->field($model, 'pc_rank') ?>

    <?php // echo $form->field($model, 'pc_cpc') ?>

    <?php // echo $form->field($model, 'charge') ?>

    <?php // echo $form->field($model, 'competition') ?>

    <?php // echo $form->field($model, 'match_type') ?>

    <?php // echo $form->field($model, 'bid') ?>

    <?php // echo $form->field($model, 'pc_click') ?>

    <?php // echo $form->field($model, 'pc_pv') ?>

    <?php // echo $form->field($model, 'pc_show') ?>

    <?php // echo $form->field($model, 'pc_ctr') ?>

    <?php // echo $form->field($model, 'all_show_rate') ?>

    <?php // echo $form->field($model, 'all_rank') ?>

    <?php // echo $form->field($model, 'all_charge') ?>

    <?php // echo $form->field($model, 'all_cpc') ?>

    <?php // echo $form->field($model, 'all_rec_bid') ?>

    <?php // echo $form->field($model, 'all_click') ?>

    <?php // echo $form->field($model, 'all_pv') ?>

    <?php // echo $form->field($model, 'all_show') ?>

    <?php // echo $form->field($model, 'all_ctr') ?>

    <?php // echo $form->field($model, 'm_show_rate') ?>

    <?php // echo $form->field($model, 'm_rank') ?>

    <?php // echo $form->field($model, 'm_charge') ?>

    <?php // echo $form->field($model, 'm_cpc') ?>

    <?php // echo $form->field($model, 'm_rec_bid') ?>

    <?php // echo $form->field($model, 'm_click') ?>

    <?php // echo $form->field($model, 'm_pv') ?>

    <?php // echo $form->field($model, 'm_show') ?>

    <?php // echo $form->field($model, 'm_ctr') ?>

    <?php // echo $form->field($model, 'show_reasons') ?>

    <?php // echo $form->field($model, 'businessPoints') ?>

    <?php // echo $form->field($model, 'word_package') ?>

    <?php // echo $form->field($model, 'json_info') ?>

    <?php // echo $form->field($model, 'similar') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript">
    $("#baidukeywordssearch-domain_id").change(function () {
        var html = '';
        $.ajax({
            url: '/index.php/article-rules/get-class',
            type: 'GET',
            dataType: 'json',
            data: {domain_id: $("#baidukeywordssearch-domain_id").find("option:selected").val()},
            success: function (msg) {
                $.each(msg, function (key, val) {
                    html += '<option value="' + key + '">' + val + '</option>';
                });
                $("#baidukeywordssearch-column_id").html(html);
            }
        })
    });
</script>