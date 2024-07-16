<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
    <div class="sidebar" id="secondary">
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
                            <a href="<?php echo Typecho_Router::url('post', array('cid' => $post['cid']), Helper::options()->index); ?>"><?php echo $post["title"]; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <?php if (!empty($this->options->sidebarBlock) && in_array('ShowRecentComments', $this->options->sidebarBlock)): ?>
            <section class="widget" id="sidebar-comment-list">
                <h3 class="widget-title"><?php _e('最新评论'); ?></h3>
                <ul class="widget-list">
                    <?php
                        $comments = getLatestComments($this->options->commentsListSize);
                        foreach ($comments as $comment):
                        // 获取评论所属文章的 URL
                        $postUrl = Typecho_Router::url('post', array('cid' => $comment['cid']), Helper::options()->index);
                    ?>
                            <li>
                                <a class="flex comment-item"  href="<?php echo $postUrl . '#comment-' . $comment['coid']; ?>">
                                    <img class="comment-author-avatar" src="<?php echo getGravatarUrl($comment['mail']); ?>" alt="<?php echo $comment['author']; ?>">
                                    <div class="flex-1">
                                        <div class="comment-author"><?php echo $comment['author']; ?></div>
                                        <div class="comment-content"><?php echo commentEmojiReplace($comment['text']); ?></div>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <?php if (!empty($this->options->sidebarBlock) && in_array('ShowCategory', $this->options->sidebarBlock)): ?>
            <section class="widget">
                <h3 class="widget-title"><?php _e('分类'); ?></h3>
                <?php \Widget\Metas\Category\Rows::alloc()->listCategories('wrapClass=widget-list'); ?>
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
