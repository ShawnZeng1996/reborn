<?php if (!empty($this->options->sidebarBlock) && in_array('ShowRecentPosts', $this->options->sidebarBlock)): ?>
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
                            <span class="post-view">阅读&nbsp;<?php echo getPostViewNum($post["cid"]); ?></span>
                            <span class="post-like-num">赞&nbsp;<?php echo getPostLikeNum($post["cid"]) ?></span>
                        </div>
                        <img src="<?php echo getPostThumbnail($post["cid"]); ?>" alt="<?php echo $post["title"]; ?>" class="post-thumbnail" />
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>
