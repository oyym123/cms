<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ArticleWaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '手法';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-way-index">

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
            'sort',
            'function_name',
            'user_id',
            [
                'label' => '状态',
                'attribute' => 'status',
                'filter' => \common\models\Base::getBaseStatus(),
                'filterInputOptions' => ['prompt' => '所有状态', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    return \common\models\Base::getBaseStatus($model->status);
                }
            ],
            //'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
