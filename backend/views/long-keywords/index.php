<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LongKeywordsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Long Keywords';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="long-keywords-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Long Keywords', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'm_down_name',
            'm_seach_name',
            'm_related_name',
            'pc_down_name',
            //'pc_search_name',
            //'pc_related_name',
            //'keywords',
            //'key_id',
            //'key_search_num',
            //'status',
            //'remark',
            //'from',
            //'url:url',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
