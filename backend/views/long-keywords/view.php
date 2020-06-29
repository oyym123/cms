<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LongKeywords */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Long Keywords', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="long-keywords-view">

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
            'm_down_name',
            'm_seach_name',
            'm_related_name',
            'pc_down_name',
            'pc_search_name',
            'pc_related_name',
            'keywords',
            'key_id',
            'key_search_num',
            'status',
            'remark',
            'from',
            'url:url',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
