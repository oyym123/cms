<?php

use yii\widgets\LinkPager;

?>
    <h1>PC首页</h1>


<?php

foreach ($column as $item) {
    echo '<a href="/' . $item['name'] . '"><h3>' . $item['zh_name'] . '</h3></a>';
}

foreach ($models as $model) {
    echo '<a href ="/wen/' . $model['id'] . '.html" >' . $model['title'] . '</a>';
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