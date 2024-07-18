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
        <?php $this->need('/modules/sidebar/widgets/recent-posts-widget.php'); ?>
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
