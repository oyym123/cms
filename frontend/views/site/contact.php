<?php

use yii\widgets\LinkPager;
//循环展示数据
foreach ($models as $model) {
    // ......
}
//显示分页页码
echo LinkPager::widget([
    'pagination' => $pages,
]);