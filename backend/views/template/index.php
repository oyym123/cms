<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '模板';
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    .container {
        width: 140px;
        height: 80px;
        border: 2px solid lightpink;
        overflow: hidden;
    }

    .container img {
        height: 100%;
        transition: all 1.0s;
        background: yellow;
    }

    .container:hover img {
        transform: scale(1.3);
    }
</style>
<div class="template-index">

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
                'label' => '截图',
                'attribute' => 'img',
                'content' => function ($model, $key, $index, $column) {
                    return '<a target="_blank" href="' . $model->img . '"><div class="container"><img src="' . $model->img . '?imageView2/1/w/240/h/180" ></div></a>';
                }
            ],
//            'content:ntext',
            [
                'label' => '网页类型',
                'attribute' => 'type',
                'filter' => \common\models\Template::getType(),
                'filterInputOptions' => ['prompt' => '所有类型', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    return \common\models\Template::getType($model->type);
                }
            ],
            'en_name',
            'intro:ntext',
            [
                'label' => '类别',
                'attribute' => 'cate',
                'filter' => \common\models\Template::getCate(),
                'filterInputOptions' => ['prompt' => '所有类型', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    return \common\models\Template::getCate($model->cate);
                }
            ],
            [
                'label' => '状态',
                'attribute' => 'status',
                'filter' => \common\models\Base::getBaseStatus(),
                'filterInputOptions' => ['prompt' => '所有状态', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    return \common\models\Base::getBaseStatus($model->status);
                }
            ],
            //'user_id',
            //'created_at',
            'updated_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
