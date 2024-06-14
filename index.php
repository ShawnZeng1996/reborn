<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<div class="container content">
    <div class="main-content">
        <?php while($this->next()): ?>
            <?php if ($this->fields->postType == 'shuoshuo'): ?>
                <!-- 说说 -->
                <div class="shuoshuo post-type flex">
                    <div class="post-author-avatar"><?php $this->author->gravatar('40') ?></div>
                    <div class="post-content">
                        <div class="post-author"><?php $this->author(); ?></div>
                        <?php $this->content(); ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- 文章 -->
                <div class="post post-type flex">
                    <?php $this->author->gravatar('40') ?>
                    <?php //$this->content(); ?></div>
            <?php endif; ?>
        <?php endwhile; ?>

    </div>
    <?php $this->need('sidebar.php'); ?>
    <?php $this->need('footer.php'); ?>
</div>
