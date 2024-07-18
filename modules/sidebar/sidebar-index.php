<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
    <div class="sidebar" id="secondary">
        <?php if (!empty($this->options->sidebarBlock) && in_array('ShowRecentPosts', $this->options->sidebarBlock)): ?>
            <?php $this->need('/modules/sidebar/widgets/recent-posts-widget.php'); ?>
        <?php endif; ?>

        <?php if (!empty($this->options->sidebarBlock) && in_array('ShowRecentComments', $this->options->sidebarBlock)): ?>
            <?php $this->need('/modules/sidebar/widgets/recent-comments-widget.php'); ?>
        <?php endif; ?>

        <?php if (!empty($this->options->sidebarBlock) && in_array('ShowCategory', $this->options->sidebarBlock)): ?>
            <?php $this->need('/modules/sidebar/widgets/categories-list-widget.php'); ?>
        <?php endif; ?>

        <div id="sticky">
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

        <?php $this->need('/modules/sidebar/widgets/advertisement-widget.php'); ?>
        </div>
    </div><!-- end #sidebar -->
