<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AizhanRulesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '爱站规则配置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="aizhan-rules-index">

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
            'site_url:url',
            [
                'label' => '类型',
                'attribute' => 'category_id',
                'content' => function ($model, $key, $index, $column) {
                    return $model->category_id . '【' . $model->category->name . '】';
                }
            ],
            [
                'label' => '域名',
                'attribute' => 'domain_id',

                'content' => function ($model, $key, $index, $column) {
                    return $model->domain_id . '【' . $model->domain->name . '】';
                }
            ],
            [
                'label' => '栏　　　　　　　　　　　　　　　　目',
                'attribute' => 'column_id',
                'content' => function ($model, $key, $index, $column) {
                    return '<a href="https://www.' . $model->domain->name . '/' . $model->column->name . '" target="_blank">' . $model->column_id . '【' . $model->column->name . '】' . '【' . $model->column->zh_name . '】</a>';
                }
            ],
//            'column_id',

            'sort',
            [
                'label' => '状态',
                'attribute' => 'status',
                'filter' => \common\models\AizhanRules::getStatus(),
                'content' => function ($model, $key, $index, $column) {
                    return \common\models\AizhanRules::getStatus($model->status);
                }
            ],

            'max_search_num',
            'note',
//            'user_id',
            'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
