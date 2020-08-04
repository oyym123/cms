
<!-- 文章列表模块-->
<article class="section-list">
    <div class="user-msg"><a href="#" rel="nofollow"><img src="/images/user-img.png"
                                                          alt=""><span>空白123456</span></a></div>
    <section>
        <h1><strong> <?= $models['data']['title'] ?></strong></h1>
        <?= $models['data']['content'] ?>
    </section>

</article>

<nav class="page-box">
    <a class="fs14 pageUp" href="<?= $models['pre'] ?>" title="">上一条</a>
    <a class="fs14 pageDowm" href="<?= $models['next'] ?>" title="">下一条更精彩</a>
    <div class="fs14 coll">收藏</div>
</nav>
<footer class="fixed-footer">
    <p class="footer-title">热门评论</p>
    <div class="comment-null">
        <img src="/images/shafa.png" alt="">
        <p class="fs14 color-999">此文章暂无评论，快去抢沙发吧~</p>
    </div>
    <div class="fixed-input">
        <input type="text" name="user_input" placeholder="评论点什么...">
        <button name="submit">评论</button>
    </div>
</footer>
