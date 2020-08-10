<?php

use yii\widgets\LinkPager;

?>
    <h1>PC首页333</h1>


<?php

foreach ($models['home_list'] as $model) {
    echo '<a href ="/wen/' . $model['id'] . '.html" >' . $model['title'] . '</a>';
    echo '<br/>';
}

echo '<a href ="/customize_01.html" >自定义页面 岁月神偷</a>';


//显示分页页码
echo LinkPager::widget([
    'pagination' => $pages,
    'maxButtonCount' => 5,
//    'options' => ['class' => 'm-pagination'],
    'nextPageLabel' => '下一页',
    'prevPageLabel' => '上一页',
]);