<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SpecialKeywordsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '敏感词';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="special-keywords-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('创建', ['create'], ['class' => 'btn btn-success']) ?>
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
                'label' => '状态',
                'attribute' => 'status',
                'filter' => \common\models\Base::getBaseStatus(),
                'filterInputOptions' => ['prompt' => '所有状态', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    if ($model->status == \common\models\Base::STATUS_BASE_NORMAL) {
                        return '<b style="color: green">正常</b>';
                    } else {
                        return '<b style="color: red">禁用</b>';
                    }
                }
            ],
            'updated_at',
            'created_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
