<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ArticleRulesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Article Rules';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-rules-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Article Rules', ['create'], ['class' => 'btn btn-success']) ?>
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
            //'user_id',
            //'status',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
