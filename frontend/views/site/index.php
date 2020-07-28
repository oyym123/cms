<?php

use yii\widgets\LinkPager;

foreach ($models as $model) {
    print_r($model->title);
    echo '<br/>';
}

//显示分页页码
echo LinkPager::widget([
    'pagination' => $pages,
    'maxButtonCount' => 5,
//    'options' => ['class' => 'm-pagination'],
    'nextPageLabel' => '下一页',
    'prevPageLabel' => '上一页',
]);