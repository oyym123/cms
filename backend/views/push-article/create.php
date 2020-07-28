<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PushArticle */

$this->title = 'Create Push Article';
$this->params['breadcrumbs'][] = ['label' => 'Push Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="push-article-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
