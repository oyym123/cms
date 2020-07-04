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
            'keywords',
            [
                'label' => '移动端下拉框',
                'format' => 'raw',
                'attribute' => 'm_down_name',
                'value' => function ($model) {
                    return implode(' |  ', json_decode($model->m_down_name, true));
                }
            ],
            [
                'label' => '移动端其他人搜索',
                'format' => 'raw',
                'attribute' => 'm_search_name',
                'value' => function ($model) {
                    return implode(' |  ', json_decode($model->m_search_name, true));
                }
            ],
            [
                'label' => '移动端相关搜索',
                'format' => 'raw',
                'attribute' => 'm_related_name',
                'value' => function ($model) {
                    return implode(' |  ', json_decode($model->m_related_name, true));
                }
            ],
            'pc_down_name',
            'pc_search_name',
            'pc_related_name',
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
