<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DbNameSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Db Names';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="db-name-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Db Name', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'baidu_token',
            'mip_time',
            'baidu_password',
            'baidu_account',
            'domain',
            'name',
            //'status',
            [
                'label' => '更新tags页面',
                'attribute' => 'status',
                'content' => function ($model, $key, $index, $column) {
                    $domain = str_replace('m.', '', $model->domain);
                    return '<a href="' . 'http://116.193.169.122:89/index.php?r=cms/set-tags&db_name=' . $model->name . '&db_domain=' . $domain . '" target="_blank">点击更新</a>';
                }
            ],
            'updated_at',
            //'created_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
