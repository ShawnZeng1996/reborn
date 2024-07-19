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
    <?php
    // 判断是否需要生成目录
    $showToc = $this->is('post');
    // 获取广告内容
    $sidebarAd = $this->options->sidebarAd;
    // 判断是否需要生成广告
    $showAd = !empty($sidebarAd);

    // 只有当需要生成目录或广告时才生成 <div id="sticky">
    if ($showToc || $showAd): ?>
        <div id="sticky">
            <?php if ($showToc): ?>
                <section class="widget toc">
                    <h3 class="widget-title"><?php _e('目录'); ?></h3>
                    <div class="widget-toc">
                        <?php echo generateToc($this->content); ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($showAd): ?>
                <?php $this->need('/modules/sidebar/widgets/advertisement-widget.php'); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>



</div><!-- end #sidebar -->
