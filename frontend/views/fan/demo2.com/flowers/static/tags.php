<?php
use yii\widgets\LinkPager;
    echo '<h1>标签页</h1>';



foreach($models as $item){
  echo '<a href="' . $item['url'].'">'.$item['keywords'].' </a><br/>';
}
//显示分页页码
echo LinkPager::widget([
    'pagination' => $pages,
    'maxButtonCount' => 5,
//    'options' => ['class' => 'm-pagination'],
    'nextPageLabel' => '下一页',
    'prevPageLabel' => '上一页',
]);