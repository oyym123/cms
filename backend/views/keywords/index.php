<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $searchModel common\models\search\KeywordsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '爱站关键词';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="keywords-index">
    <h1><?= Html::encode($this->title) ?></h1>
<!--    <p>-->
<!--        <button id="catch_aizhan" class="btn btn-primary"> 抓取爱站关键词</button>-->
<!--        <b id="topic"></b>-->
<!--        <br/>-->
<!--        <br/>-->
<!--        <input type="text" id="catch_value" class="form-control">-->
<!--    </p>-->
<!---->
<!--    <br/>-->
<!--    <p>-->
<!--        --><?//= Html::a('创建新的关键词', ['create'], ['class' => 'btn btn-success']) ?>
<!--    </p>-->
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',

            [
                'label' => '关  　　　　　  键    　　　　　　　　词',
                'attribute' => 'keywords',
                'class' => 'kartik\grid\EditableColumn',
                'editableOptions' => [
                    'asPopover' => false,
                ],
            ],
            [
                'label' => '状    　　　　　    　　　　　态',
                'attribute' => 'status',
                'class' => 'kartik\grid\EditableColumn',
                'editableOptions' => [
                    'inputType' => \kartik\editable\Editable::INPUT_RADIO_LIST,
                    'asPopover' => false,
                    'data' => [0 => '关闭', 1 => '开启'],
                ],
                'value' => function ($model) {
                    return \common\models\Base::getBaseS($model->status);
                },
                'filter' => [0 => '关闭', 1 => '开启'],
            ],

            'search_num',
            'sort',
            [
                'label' => '关键词来源',
                'attribute' => 'form',
                'filter' => \common\models\Keywords::getFrom(),
                'filterInputOptions' => ['prompt' => '所有来源', 'class' => 'form-control', 'id' => null],
                'content' => function ($model, $key, $index, $column) {
                    return \common\models\Keywords::getFrom($model->form);
                }
            ],
            [
                'label' => '关键词类型',
                'attribute' => 'type',
                'filter' => \common\models\Keywords::getType(),
                'filterInputOptions' => ['prompt' => '所有类型', 'class' => 'form-control', 'id' => null],
                'content' => function ($model, $key, $index, $column) {
                    return \common\models\Keywords::getType($model->type);
                }
            ],
            'rank',

//            'title',
            //'content:ntext',
            //'note',
            'url:url',
            'created_at',
            //'updated_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script>
    $("#catch_aizhan").click(function () {
        if ($("#catch_value").val() == '') {
            $("#topic").append('<b style="color: red">请输入正确网址<b>');
        } else {
            $("#topic").append('<b style="color: red">正在抓取数据，请稍等。。。<b>');
        }

        $.ajax({
            type: "GET",
            url: '/index.php/keywords/catch&url=' + $("#catch_value").val(),
            success: function (html) {
                console.log(html);
                if (html != '0') {
                    $("#topic").append('<b style="color: red">' + html + ' < b > ');
                }
            }
        });
    });
</script>
