<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TemplateTplSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '模组 套装';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="template-tpl-index">

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
            [
                'label' => '公共页',
                'attribute' => 't_common',
                'content' => function ($model, $key, $index, $column) {
                    $tem = \common\models\Template::findOne($model->t_common);
                    $name = $tem ? $tem->name : '';
                    return '<a href="/index.php/template/' . $model->t_common . '" >' . $name . '  </a>';
                }
            ],
            [
                'label' => '首页',
                'attribute' => 't_home',
                'content' => function ($model, $key, $index, $column) {
                    $tem = \common\models\Template::findOne($model->t_home);
                    $name = $tem ? $tem->name : '';
                    return '<a href="/index.php/template/' . $model->t_home . '" >' . $name . '  </a>';
                }
            ],
            [
                'label' => '列表页',
                'attribute' => 't_list',
                'content' => function ($model, $key, $index, $column) {
                    $tem = \common\models\Template::findOne($model->t_list);
                    $name = $tem ? $tem->name : '';
                    return '<a href="/index.php/template/' . $model->t_list . '" >' . $name . '  </a>';
                }
            ],
            [
                'label' => '泛内页',
                'attribute' => 't_inside',
                'content' => function ($model, $key, $index, $column) {
                    $tem = \common\models\Template::findOne($model->t_inside);
                    $name = $tem ? $tem->name : '';
                    return '<a href="/index.php/template/' . $model->t_inside . '" >' . $name . '  </a>';
                }
            ],
            [
                'label' => '详情页',
                'attribute' => 't_detail',
                'content' => function ($model, $key, $index, $column) {
                    $tem = \common\models\Template::findOne($model->t_detail);
                    $name = $tem ? $tem->name : '';
                    return '<a href="/index.php/template/' . $model->t_detail . '" >' . $name . ' </a>';
                }
            ],
            [
                'label' => '标签页',
                'attribute' => 't_tags',
                'content' => function ($model, $key, $index, $column) {
                    $tem = \common\models\Template::findOne($model->t_tags);
                    $name = $tem ? $tem->name : '';
                    return '<a href="/index.php/template/' . $model->t_tags . '" >' . $name . ' </a>';
                }
            ],
            [
                'label' => '自定义页面',
                'attribute' => 't_customize',
                'content' => function ($model, $key, $index, $column) {
                    $tem = \common\models\Template::find()->where(['in', 'id', explode(',', $model->t_customize)])->all();
                    $str = '';
                    foreach ($tem as $item) {
                        $name = $item ? $item->name : '';
                        $str .= '<a href="/index.php/template/' . $item->id . '" >' . $name . ' </a><br>';
                    }

                    return $str;
                }
            ],

            //'t_list',
            //'t_common',
            //'t_home',
            //'type',
            //'t_ids',
            //'status',
            //'user_id',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
