<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\BlackArticle */

$this->title = 'Update Black Article: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Black Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="black-article-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
