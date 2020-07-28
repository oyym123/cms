<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ArticleRules */

$this->title = 'Create Article Rules';
$this->params['breadcrumbs'][] = ['label' => 'Article Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-rules-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
