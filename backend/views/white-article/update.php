<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WhiteArticle */

$this->title = 'Update White Article: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'White Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="white-article-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
