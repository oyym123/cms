<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LongKeywordsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '百度下拉框关键词';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="long-keywords-index">

    <h1><?= Html::encode($this->title) ?></h1>

<!--    <p>-->
<!--        --><?//= Html::a('Create Long Keywords', ['create'], ['class' => 'btn btn-success']) ?>
<!--    </p>-->

    <?php Pjax::begin(); ?>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
            'key_id',
            'keywords',

            [
                'label' => '关键词类型',
                'attribute' => 'type',
                'filter' => \common\models\LongKeywords::getType(),
                'filterInputOptions' => ['prompt' => '所有类型', 'class' => 'form-control', 'id' => null],
                'content' => function ($model, $key, $index, $column) {
                    return \common\models\LongKeywords::getType($model->type);
                }
            ],

//            [
//                'label' => '移动下拉词 　　　　　　　　　          ',
//                'contentOptions' => [
//                    'width'=>'200'
//                ],
//                'attribute' => 'm_down_name',
//                'content' => function ($model, $key, $index, $column) {
//                    return implode('【】',json_decode($model->m_down_name,true));
//                }
//            ],
            //'m_search_name',
            //'m_related_name',
//            'pc_down_name',
            //'pc_search_name',
            //'pc_related_name',
            'key_search_num',
            //'status',
            //'remark',
            //'from',
            //'url:url',
            'created_at',
            //'updated_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
