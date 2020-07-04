<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\BaiduKeywords */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Baidu Keywords', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="baidu-keywords-view">

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
            'from_keywords',
            'pc_show_rate',
            'pc_rank',
            'pc_cpc',
            'charge',
            'competition',
            'match_type',
            'bid',
            'pc_click',
            'pc_pv',
            'pc_show',
            'pc_ctr',
            'all_show_rate',
            'all_rank',
            'all_charge',
            'all_cpc',
            'all_rec_bid',
            'all_click',
            'all_pv',
            'all_show',
            'all_ctr',
            'm_show_rate',
            'm_rank',
            'm_charge',
            'm_cpc',
            'm_rec_bid',
            'm_click',
            'm_pv',
            'm_show',
            'm_ctr',
            'show_reasons',
            'businessPoints',
            'word_package:ntext',
            'json_info:ntext',
            'similar',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
