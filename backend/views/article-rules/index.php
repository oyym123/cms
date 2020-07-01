<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ArticleRulesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '文章生成规则';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-rules-index">

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
            'name',
            [
                'label' => '类　　　　　　　　型',
                'attribute' => 'type',
                'filter' => \common\models\DomainColumn::getType(),
                'filterInputOptions' => ['prompt' => '所有类型', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    return $model->category->id . '【' . $model->category->name . '】';
                }
            ],
            [
                'label' => '状态',
                'attribute' => 'status',
                'filter' => \common\models\Base::getBaseStatus(),
                'filterInputOptions' => ['prompt' => '所有状态', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    if ($model->status == \common\models\Base::STATUS_BASE_NORMAL) {
                        return '<b style="color: green">已上线</b>';
                    } else {
                        return '<b style="color: red">测试中</b>';
                    }
                }
            ],
            [
                'label' => '域名',
                'attribute' => 'domain_id',

                'content' => function ($model, $key, $index, $column) {
                    return $model->domain->id . '【' . $model->domain->name . '】';
                }
            ],
            [
                'label' => '栏　　　　　　　　　　　　　　　　目',
                'attribute' => 'column_id',

                'content' => function ($model, $key, $index, $column) {
                    return $model->column->id . '【' . $model->column->name . '】' . '【' . $model->column->zh_name . '】';
                }
            ],
            //'method_ids',
            //'one_page_num_min',
            //'one_page_num_max',
            //'one_page_word_min',
            //'one_page_word_max',
            //'one_day_push_num',
            //'push_time_sm',
            //'push_time_bd',
            //'use  r_id',


//            [
//                'label' => '拉取文章',
//                'attribute' => 'status',
//
//                'content' => function ($model, $key, $index, $column) {
//                    return '<a target="_blank" href="/index.php/article-rules/article?column_id=' . $model->column_id . '">点击获取文章</a>';
//                }
//            ],

            //'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
