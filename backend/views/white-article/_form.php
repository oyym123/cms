<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WhiteArticle */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="white-article   -form">

    <?php $form = ActiveForm::begin(); ?>

    <!--    --><? //= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'key_id')->textInput() ?>

    <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cut_word')->textInput(['maxlength' => true]) ?>

    <!--    --><? //= $form->field($model, 'image_urls')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'db_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'from_path')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'word_count')->textInput() ?>

    <!--    --><? //= $form->field($model, 'part_content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'content')->widget('common\widgets\ueditor\Ueditor', [
//        'options'=>[
//            'initialFrameWidth' => 850,
//        ]
    ]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
