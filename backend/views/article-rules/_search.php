<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\ArticleRulesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="article-rules-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'category_id') ?>

    <?= $form->field($model, 'domain_id') ?>

    <?= $form->field($model, 'column_id') ?>

    <?php // echo $form->field($model, 'method_ids') ?>

    <?php // echo $form->field($model, 'one_page_num_min') ?>

    <?php // echo $form->field($model, 'one_page_num_max') ?>

    <?php // echo $form->field($model, 'one_page_word_min') ?>

    <?php // echo $form->field($model, 'one_page_word_max') ?>

    <?php // echo $form->field($model, 'one_day_push_num') ?>

    <?php // echo $form->field($model, 'push_time_sm') ?>

    <?php // echo $form->field($model, 'push_time_bd') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
