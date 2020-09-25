<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\PushArticleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="push-article-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>
    <!---->
    <!--    --><? //= $form->field($model, 'b_id') ?>

    <?= $form->field($model, 'column_id') ?>

    <!--    --><? //= $form->field($model, 'column_name') ?>

    <?= $form->field($model, 'rules_id') ?>

    <?php echo $form->field($model, 'domain_id') ?>

    <!--    --><?php // echo $form->field($model, 'domain') ?>

    <?php // echo $form->field($model, 'from_path') ?>

    <?php echo $form->field($model, 'keywords') ?>

    <!---->
    <!--    --><?php // echo $form->field($model, 'title_img') ?>
    <!---->
    <!--    --><?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'content') ?>

    <!--    --><?php // echo $form->field($model, 'intro') ?>
    <!---->
    <?php echo $form->field($model, 'title') ?>

    <?php echo $form->field($model, 'push_time') ?>

    <?php echo $form->field($model, 'created_at') ?>

    <!--    --><?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
