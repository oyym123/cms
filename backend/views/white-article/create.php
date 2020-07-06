<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WhiteArticle */

$this->title = '创建文章';
$this->params['breadcrumbs'][] = ['label' => 'White Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="white-article-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
