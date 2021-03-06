<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\PushArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '文章';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="push-article-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <!--        --><? //= Html::a('Create Push Article', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
//            'b_id',
            'column_id',
//            'column_name',
//            'rules_id',
            [
                'label' => '域名',
                'attribute' => 'domain_id',
                'content' => function ($model, $key, $index, $column) {
                    return \common\models\Domain::findOne($model->domain_id)->name;
                }
            ],
            [
                'label' => '修改',
                'attribute' => 'intro',
                'content' => function ($model, $key, $index, $column) {
                    return '<a href="/index.php/push-article/update?id=' . $model->id . '&domain_id=' . $model->domain_id . '">点击文章修改</a>';
                }
            ],

//            'from_path',
            'keywords',
//            'title_img',
            [
                'label' => '状态',
                'attribute' => 'status',
                'filter' => \common\models\Base::getBaseStatus(),
                'filterInputOptions' => ['prompt' => '所有状态', 'class' => 'form-control', 'id' => null, 'value' => 'all'],
                'content' => function ($model, $key, $index, $column) {
                    return \common\models\Base::getBaseStatus($model->status);
                }
            ],
            //'content:ntext',
//            'intro',
            'title',
            'push_time',
            'created_at',
//            'updated_at',
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
