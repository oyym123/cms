    <ul class="content-left">
        <?php
        foreach ($models['home_list'] as $item) {
            ?>
            <li>
                <div class="user-msg">
                    <a class="user-box" rel="nofollow">
                        <img class="js_user_image" src="<?= $item['avatar'] ?>"/><span class="user-name"><?= $item['nickname'] ?></span>
                    </a>
                </div>
                <a href="wen/<?= $item['id'] ?>.html">
                    <p class="title-p"><?= $item['title'] ?></p>
                    <p class="detail-p">
                        <?= $item['intro'] ?>
                    </p>
                </a>
                <ul class="left-list-bottom">
                    <li class="js_zan"><i></i><span>0</span></li>
                    <li class="js_cai"><i></i><span>0</span></li>
                    <li class="js_star"><i></i><span>0</span></li>
                    <li class="js_com"><i></i><span>0</span></li>
                    <li class="js_share"><i></i><span>0</span></li>
                </ul>
            </li>
            <?php
        }
        ?>
        <li>

        <li>
<!--            <div class="page-list">-->
<!--                <a href="#" title="">1</a>-->
<!--                <a href="#" title="">2</a>-->
<!--                <a href="#" title="">3</a>-->
<!--                <a href="#" title="">4</a>-->
<!--                <a href="#" title="">5</a>-->
<!--                <a href="#" title="">...</a>-->
<!--                <a href="#" title="">下一页</a>-->
<!--            </div>-->

            <?php

            use yii\widgets\LinkPager;

            echo LinkPager::widget([
                'pagination' => $pages,
                'maxButtonCount' => 5,
                //'options' => ['class' => 'page-list"'],
                'nextPageLabel' => '下一页',
                'prevPageLabel' => '上一页',
            ]);
            ?>

        </li>
    </ul>

