<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\PushArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Push Articles';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="push-article-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Push Article', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'b_id',
            'column_id',
            'column_name',
            'rules_id',
            //'domain_id',
            //'domain',
            //'from_path',
            //'keywords',
            //'title_img',
            //'status',
            //'content:ntext',
            //'intro',
            //'title',
            //'push_time',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
