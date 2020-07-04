<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\BlackArticle */

$this->title = 'Create Black Article';
$this->params['breadcrumbs'][] = ['label' => 'Black Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="black-article-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
