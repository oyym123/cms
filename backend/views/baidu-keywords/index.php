<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BaiduKeywordsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '百度营销关键词';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="baidu-keywords-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('新增tags', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'keywords',
            'm_pv',
            'from_keywords',
//            'pc_show_rate',
//            'pc_rank',
            //'pc_cpc',
            //'charge',
            'competition',
            //'match_type',
            //'bid',
            //'pc_click',
            'pc_pv',
            //'pc_show',
            //'pc_ctr',
            //'all_show_rate',
            //'all_rank',
            //'all_charge',
            //'all_cpc',
            'all_rec_bid',
            //'all_click',
            'all_pv',
            //'all_show',
            //'all_ctr',
            //'m_show_rate',
            //'m_rank',
            //'m_charge',
            //'m_cpc',
            //'m_rec_bid',
            //'m_click',

            //'m_show',
            //'m_ctr',
            //'show_reasons',
            //'businessPoints',
            //'word_package:ntext',
            //'json_info:ntext',
            //'similar',
            //'status',
            'created_at',
//            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
