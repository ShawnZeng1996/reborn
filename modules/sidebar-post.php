<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<div class="sidebar" id="secondary">
    <section class="widget author-card">
        <?php if ($this->author->mail): ?>
            <img class="author-avatar"  src="<?php echo getGravatarUrl($this->author->mail, 160); ?>" alt="<?php $this->author(); ?>" />
        <?php endif; ?>
        <div class="post-author"><?php $this->author(); ?></div>
        <div class="author-description"><?php $this->options->description() ?></div>
        <?php $stats = getAuthorPostStats($this->author->uid); ?>
        <div class="author-meta">
            文章&nbsp;<?php echo $stats['numPosts']; ?>
            &nbsp;|&nbsp;
            阅读&nbsp;<?php echo $stats['totalViews']; ?>
            &nbsp;|&nbsp;
            点赞&nbsp;<?php echo $stats['totalLikes']; ?>
        </div>
    </section>
    <?php if (!empty($this->options->sidebarBlock) && in_array('ShowRecentPosts', $this->options->sidebarBlock)): ?>
        <section class="widget" id="sidebar-post-list">
            <h3 class="widget-title"><?php _e('最新文章'); ?></h3>
            <ul class="widget-list">
                <?php
                // 获取最近的文章
                \Widget\Contents\Post\Recent::alloc()->to($posts);
                // 循环遍历所有文章
                while ($posts->next()):
                    // 检查文章类型是否为 'post'
                    if ($posts->fields->postType != 'shuoshuo'): ?>
                        <li>
                            <a href="<?php $posts->permalink(); ?>"><?php $posts->title(); ?></a>
                        </li>
                    <?php endif;
                endwhile; ?>
            </ul>
        </section>
    <?php endif; ?>

    <?php $sidebarAd = $this->options->sidebarAd; ?>
    <?php if ($sidebarAd): ?>
        <section class="sidebar-adv widget">
            <h3 class="widget-title"><?php _e('广告'); ?></h3>
            <div><?php echo $sidebarAd; ?></div>
        </section>
    <?php endif; ?>

</div><!-- end #sidebar -->
