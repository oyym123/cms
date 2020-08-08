<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\FanUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fan Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fan-user-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Fan User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'username',
            [
                'label' => '头像',
                'attribute' => 'avatar',
                'content' => function ($model, $key, $index, $column) {
                    return '<a target="_blank" href="' . $model->avatar . '"><div class="container"><img src="' . $model->avatar . '?imageView2/1/w/240/h/180" ></div></a>';
                }
            ],
            'nickname',
            'auth_key',
            //'password_hash',
            //'password_reset_token',
            //'email:email',
            //'status',
            //'created_at',
            //'updated_at',
            //'verification_token',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
