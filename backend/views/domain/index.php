<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DomainSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '域名';

$this->params['breadcrumbs'][] = $this->title;
?>
<a target="_blank" href="/index.php/domain/refresh">刷新所有跳转规则</a>
<div class="domain-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('新增', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'name:url',
            'zh_name',
//            'ip',
            'start_tags',
            'end_tags',
            [
                'label' => '百度推送PC端',
                'attribute' => 'created_at',
                'filter' => \common\models\Base::getBaseStatus(),
                'filterInputOptions' => ['prompt' => '所有状态', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    return '<a href="/index.php/domain/push-url?id=' . $model->id . '&test=1&type=1">  推送的PC端URL   </a>';
                }
            ],
            [
                'label' => '百度推送M端',
                'attribute' => 'created_at',
                'filter' => \common\models\Base::getBaseStatus(),
                'filterInputOptions' => ['prompt' => '所有状态', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    return '<a href="/index.php/domain/push-url?id=' . $model->id . '&test=1&type=2">  推送的M端URL   </a>';
                }
            ],
            [
                'label' => '点击推送百度M',
                'attribute' => 'user_id',
                'filter' => \common\models\Base::getBaseStatus(),
                'content' => function ($model, $key, $index, $column) {
                    return '<a href="/index.php/domain/push-url?id=' . $model->id . '&type=2"> 点击推送 </a>';
                }
            ],
            [
                'label' => '点击推送百度PC',
                'attribute' => 'user_id',
                'filter' => \common\models\Base::getBaseStatus(),
                'content' => function ($model, $key, $index, $column) {
                    return '<a href="/index.php/domain/push-url?id=' . $model->id . '&type=1"> 点击推送 </a>';
                }
            ],
//            'user_id',
//            'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
