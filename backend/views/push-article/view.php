<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\PushArticle */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Push Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="push-article-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'b_id',
            'column_id',
            'column_name',
            'rules_id',
            'domain_id',
            'domain',
            'from_path',
            'keywords',
            'title_img',
            'status',
            'content:ntext',
            'intro',
            'title',
            'push_time',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
