<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DomainColumnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '栏目';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="domain-column-index">

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
            [
                'label' => '域名',
                'attribute' => 'domain_id',
                'filter' => \common\models\Domain::getDomianName(),
                'filterInputOptions' => ['prompt' => '所有域名', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    return $model->domain->name;
                }
            ],
            'zh_name',
            'name',
            'tags',
            //'user_id',
            [
                'label' => '状态',
                'attribute' => 'status',
                'filter' => \common\models\Base::getBaseStatus(),
                'filterInputOptions' => ['prompt' => '所有状态', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    if ($model->status == \common\models\Base::STATUS_BASE_NORMAL) {
                        return '<b style="color: green">' . \common\models\Base::getBaseStatus($model->status) . '</b>';
                    } else {
                        return '<b style="color: red">' . \common\models\Base::getBaseStatus($model->status) . '</b>';
                    }
                }
            ],
            //'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
