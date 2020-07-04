<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BaiduKeywords */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="baidu-keywords-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'from_keywords')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pc_show_rate')->textInput() ?>

    <?= $form->field($model, 'pc_rank')->textInput() ?>

    <?= $form->field($model, 'pc_cpc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'charge')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'competition')->textInput() ?>

    <?= $form->field($model, 'match_type')->textInput() ?>

    <?= $form->field($model, 'bid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pc_click')->textInput() ?>

    <?= $form->field($model, 'pc_pv')->textInput() ?>

    <?= $form->field($model, 'pc_show')->textInput() ?>

    <?= $form->field($model, 'pc_ctr')->textInput() ?>

    <?= $form->field($model, 'all_show_rate')->textInput() ?>

    <?= $form->field($model, 'all_rank')->textInput() ?>

    <?= $form->field($model, 'all_charge')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'all_cpc')->textInput() ?>

    <?= $form->field($model, 'all_rec_bid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'all_click')->textInput() ?>

    <?= $form->field($model, 'all_pv')->textInput() ?>

    <?= $form->field($model, 'all_show')->textInput() ?>

    <?= $form->field($model, 'all_ctr')->textInput() ?>

    <?= $form->field($model, 'm_show_rate')->textInput() ?>

    <?= $form->field($model, 'm_rank')->textInput() ?>

    <?= $form->field($model, 'm_charge')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'm_cpc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'm_rec_bid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'm_click')->textInput() ?>

    <?= $form->field($model, 'm_pv')->textInput() ?>

    <?= $form->field($model, 'm_show')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'm_ctr')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'show_reasons')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'businessPoints')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'word_package')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'json_info')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'similar')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
