<?php

use yii\widgets\LinkPager;

?>

<ul class="content-left-label">
    <li>
        <div class="label-list-wrap">
            <?php
            foreach ($models['home_list'] as $item) {
                ?>
                <a href="<?= $item['url'] ?>" title="<?= $item['keywords'] ?>">
                    <?= $item['keywords'] ?>
                </a>

                <?php
            } ?>

        </div>
    </li>
    <li>
        <br clear="all"/>
        <div class="page-list">
            <a href="#" title="">1</a>
            <a href="#" title="">2</a>
            <a href="#" title="">3</a>
            <a href="#" title="">4</a>
            <a href="#" title="">5</a>
            <a href="#" title="">...</a>
            <a href="#" title="">下一页</a>
        </div>
        <?php


        //显示分页页码
        echo LinkPager::widget([
            'pagination' => $pages,
            'maxButtonCount' => 5,
//    'options' => ['class' => 'm-pagination'],
            'nextPageLabel' => '下一页',
            'prevPageLabel' => '上一页',
        ]);
        ?>
    </li>
</ul>
