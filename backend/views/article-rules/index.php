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
            'category_id',
            'domain_id',
            'column_id',
            //'method_ids',
            //'one_page_num_min',
            //'one_page_num_max',
            //'one_page_word_min',
            //'one_page_word_max',
            //'one_day_push_num',
            //'push_time_sm',
            //'push_time_bd',
            //'use  r_id',
            [
                'label' => '状态',
                'attribute' => 'status',
                'filter' => \common\models\Base::getBaseStatus(),
                'filterInputOptions' => ['prompt' => '所有状态', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    return \common\models\Base::getBaseStatus($model->status);
                }
            ],
            [
                'label' => '拉取文章',
                'attribute' => 'status',

                'content' => function ($model, $key, $index, $column) {
                    return '<a target="_blank" href="/index.php/article-rules/article?column_id=' . $model->column_id . '">点击获取文章</a>';
                }
            ],

            //'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
