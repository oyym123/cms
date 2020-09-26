<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SiteMapSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '网站地图';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-map-index">

    <h1><?= Html::encode($this->title) ?></h1>

<!--    <p>-->
<!--        --><?//= Html::a('Create Site Map', ['create'], ['class' => 'btn btn-success']) ?>
<!--    </p>-->

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'label' => '域名',
                'attribute' => 'domain_id',
                'filter' => \common\models\Domain::getDomianName(),
                'filterInputOptions' => ['prompt' => '所有域名', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    return $model->domain->name;
                }
            ],
            [
                'label' => '类型',
                'attribute' => 'type',
                'filter' => \common\models\SiteMap::getType(),
                'filterInputOptions' => ['prompt' => '所有类型', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    return $model->domain->name;
                }
            ],

            'file_name',
            'last_id',
            'update_start_id',
            'start_url_id',
            'number',
            'created_at',
            'updated_at',
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
