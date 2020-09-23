<ul class="content-left">
    <?php
    $item = $models['data'];
    ?>
        <li>
            <div class="user-msg">
                <a class="user-box" rel="nofollow">
                    <img class="js_user_image" src="././imges/accout-qq.png"/><span class="user-name">用户昵称</span>
                </a>
            </div>
            <a href="<?=  $item['url']?>">
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
    <li>
</ul>

