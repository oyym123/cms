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
            'updated_at',
            //'created_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
