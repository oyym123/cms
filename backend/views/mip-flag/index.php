<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MipFlagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mip Flags';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mip-flag-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Mip Flag', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'label' => '数据库',
                'attribute' => 'db_id',
                'filter' => \common\models\DbName::getDbName(),
                'filterInputOptions' => ['prompt' => '所有数据库', 'class' => 'form-control', 'id' => null],
                'content' => function ($model, $key, $index, $column) {
                    return $model->db_name;
                }
            ],
            [
                'label' => '类型',
                'attribute' => 'type',
                'filter' => \common\models\MipFlag::getType(),
                'filterInputOptions' => ['prompt' => '所有类型', 'class' => 'form-control', 'id' => null],
                'content' => function ($model, $key, $index, $column) {
                    return \common\models\MipFlag::getType($model->type);
                }
            ],
            'type_id',
            //'status',
            'created_at',
            //'updated_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
