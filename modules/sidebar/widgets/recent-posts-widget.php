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
                <a href="<?php echo getPostLink($post["cid"]) ?>"><?php echo $post["title"]; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
