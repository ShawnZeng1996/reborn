<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<div class="container content">
    <div class="main-content">
        <?php while($this->next()): ?>
            <?php if ($this->fields->postType == 'shuoshuo'): ?>
                <!-- 说说 -->
                <?php $this->need('/modules/shuoshuo-item.php'); ?>
            <?php else: ?>
                <!-- 文章 -->
                <div class="post post-type flex">
                    <div class="post-author-avatar">
                        <?php if ($this->author->mail): ?>
                            <img src="<?php echo getGravatarUrl($this->author->mail, 80); ?>" alt="<?php $this->author(); ?>" class="author-avatar" />
                        <?php endif; ?>
                    </div>
                    <div class="post-content">
                        <div class="post-author"><?php $this->author(); ?></div>
                        <div><?php $this->content(); ?></div>
                        <div class="post-meta">
                            <time class="post-publish-time" datetime="<?php $this->date('c'); ?>"><?php echo timeAgo($this->created); ?></time>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endwhile; ?>

    </div>
    <?php $this->need('sidebar.php'); ?>
    <?php $this->need('footer.php'); ?>
</div>
