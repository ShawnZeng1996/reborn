<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
    <div class="sidebar" id="secondary">
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

        <?php if (!empty($this->options->sidebarBlock) && in_array('ShowRecentComments', $this->options->sidebarBlock)): ?>
            <section class="widget" id="sidebar-comment-list">
                <h3 class="widget-title"><?php _e('最新评论'); ?></h3>
                <ul class="widget-list">
                    <?php \Widget\Comments\Recent::alloc()->to($comments); ?>
                    <?php while ($comments->next()): ?>
                        <li>
                            <a class="flex comment-item" href="<?php $comments->permalink(); ?>">
                                <img class="comment-author-avatar" src="<?php echo getGravatarUrl($comments->mail); ?>" alt="<?php $comments->author(false); ?>">
                                <div class="flex-1">
                                    <div class="comment-author"><?php $comments->author(false); ?></div>
                                    <div class="comment-content"><?php echo commentEmojiReplace($comments->content); ?></div>
                                </div>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </section>
        <?php endif; ?>

        <?php if (!empty($this->options->sidebarBlock) && in_array('ShowCategory', $this->options->sidebarBlock)): ?>
            <section class="widget">
                <h3 class="widget-title"><?php _e('分类'); ?></h3>
                <?php \Widget\Metas\Category\Rows::alloc()->listCategories('wrapClass=widget-list'); ?>
            </section>
        <?php endif; ?>

        <?php if (!empty($this->options->sidebarBlock) && in_array('ShowArchive', $this->options->sidebarBlock)): ?>
            <section class="widget">
                <h3 class="widget-title"><?php _e('归档'); ?></h3>
                <ul class="widget-list">
                    <?php \Widget\Contents\Post\Date::alloc('type=month&format=F Y')
                        ->parse('<li><a href="{permalink}">{date}</a></li>'); ?>
                </ul>
            </section>
        <?php endif; ?>

        <?php if (!empty($this->options->sidebarBlock) && in_array('ShowOther', $this->options->sidebarBlock)): ?>
            <section class="widget">
                <h3 class="widget-title"><?php _e('其它'); ?></h3>
                <ul class="widget-list">
                    <?php if ($this->user->hasLogin()): ?>
                        <li class="last"><a href="<?php $this->options->adminUrl(); ?>"><?php _e('进入后台'); ?>
                                (<?php $this->user->screenName(); ?>)</a></li>
                        <li><a href="<?php $this->options->logoutUrl(); ?>"><?php _e('退出'); ?></a></li>
                    <?php else: ?>
                        <li class="last"><a href="<?php $this->options->adminUrl('login.php'); ?>"><?php _e('登录'); ?></a>
                        </li>
                    <?php endif; ?>
                    <li><a href="<?php $this->options->feedUrl(); ?>"><?php _e('文章 RSS'); ?></a></li>
                    <li><a href="<?php $this->options->commentsFeedUrl(); ?>"><?php _e('评论 RSS'); ?></a></li>
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