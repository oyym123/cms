<?php

use yii\widgets\LinkPager;

?>

<!-- 文章列表模块-->

<?php
foreach ($models['home_list'] as $item) {
    ?>
    <article class="section-list">
        <div class="user-msg"><a href="#" rel="nofollow"><img src="./images/user-img.png"
                                                              alt=""><span>空白123456</span></a></div>
        <a href="<?= $item['url'] ?>" title="">
            <section>
                <h1><?= $item['title'] ?></h1>
                <p>
                    <?php mb_substr($item['intro'], 300) ?>
                </p>
                <p class="post-copyright">转载请注明出处<a href="#" title="">华中教育网</a> &raquo; <a href="<?= $item['url'] ?>"
                                                                                           title=""><?=$item['title']?></a>
                </p>
            </section>
        </a>
    </article>

<?php } ?>

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


