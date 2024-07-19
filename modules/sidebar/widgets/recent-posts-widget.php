<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<section class="widget" id="sidebar-post-list">
    <h3 class="widget-title"><?php _e('最新文章'); ?></h3>
    <ul class="widget-list">
        <?php
        // 获取最近的文章
        $posts = getLatestPosts($this->options->postsListSize);
        // 循环遍历所有文章
        foreach ($posts as $post): ?>
            <li>
                <a class="post-item flex" href="<?php echo getPostLink($post["cid"]) ?>">
                    <div class="post-item-left flex-1">

                        <h3 class="post-title"><?php echo $post["title"]; ?></h3>
                        <span class="post-view">阅读&nbsp;<?php getPostView($this) ?></span>
                        <span class="post-like-num">赞&nbsp;<?php $likeData = getLikeNumByCid($this->cid); echo $likeData["likes"] ?></span>
                    </div>
                    <img src="<?php echo getPostThumbnail($this->cid); ?>" alt="<?php $this->title(); ?>" class="post-thumbnail" />
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
