<ul class="content-left">
    <li style="background: #fff;">
        <div class="user-msg">
            <a class="user-box">
                <img class="js_user_image" src="/imges/accout-qq.png"/><span class="user-name">用户昵称</span>
            </a>
        </div>
        <p class="title-p"><?= $models['data']['title'] ?></p>
        <p class="details-p">
            <?= $models['data']['content'] ?>
        </p>
        <ul class="left-list-bottom">
            <li class="js_zan"><i></i><span>0</span></li>
            <li class="js_cai"><i></i><span>0</span></li>
            <li class="js_star"><i></i><span>0</span></li>
            <li class="js_com"><i></i><span>0</span></li>
            <li class="js_share"><i></i><span>0</span></li>
        </ul>
        <br clear="all"/>
        <div class="page-flip">
            <a class="pageup" href="<?= $models['pre'] ?>">上一条</a>
            <a class="pagedowm" href="<?= $models['next']?>">下一条</a>
        </div>

        <div class="comment-box">
            <img class="js_user_image" src="/imges/accout-qq.png" alt="">
            <div class="comment-textarea">
                        <textarea name="user_comment" id="user-com" cols="30" rows="10"
                                  placeholder="期待你的神评论"></textarea>
            </div>
            <br clear="all"/>
            <div class="comment-btn"><i>还能输入98字</i>
                <button type="button" id="js_comment_btn">评论</button>
            </div>
        </div>

        <div class="comment-ul">
            <div class="comment-title">评论<i>(0)</i></div>
            <div class="comment-li-null">“还没有人发表评论，快去抢占沙发吧”</div>
        </div>
    </li>
</ul>
