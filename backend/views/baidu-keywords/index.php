<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BaiduKeywordsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tags词 >> 将会匹配下拉词相关词 做为其子类 爬取的词';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="baidu-keywords-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('新增tags', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'keywords',
            'm_pv',
            'from_keywords',
            [
                'label' => '域名',
                'attribute' => 'domain_id',
                'filter' => \common\models\Domain::getDomianName(),
                'filterInputOptions' => ['prompt' => '所有域名', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    return $model->domain->name;
                }
            ],
            [
                'label' => '分类　　　   　　　   ',
                'attribute' => 'column_id',
                'filter' => \common\models\DomainColumn::getColumnData(1),
                'filterInputOptions' => ['prompt' => '所有栏目', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    return $model->column->name . '　【' . $model->column->zh_name . '】';
                }
            ],
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
