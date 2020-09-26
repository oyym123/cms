<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SiteMap */

$this->title = 'Create Site Map';
$this->params['breadcrumbs'][] = ['label' => 'Site Maps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-map-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
