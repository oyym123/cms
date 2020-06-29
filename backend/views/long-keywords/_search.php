<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LongKeywordsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="long-keywords-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'm_down_name') ?>

    <?= $form->field($model, 'm_seach_name') ?>

    <?= $form->field($model, 'm_related_name') ?>

    <?= $form->field($model, 'pc_down_name') ?>

    <?php // echo $form->field($model, 'pc_search_name') ?>

    <?php // echo $form->field($model, 'pc_related_name') ?>

    <?php // echo $form->field($model, 'keywords') ?>

    <?php // echo $form->field($model, 'key_id') ?>

    <?php // echo $form->field($model, 'key_search_num') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'from') ?>

    <?php // echo $form->field($model, 'url') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
