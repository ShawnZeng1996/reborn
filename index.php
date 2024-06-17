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
                <?php $this->need('/modules/post-item.php'); ?>
            <?php endif; ?>
        <?php endwhile; ?>

    </div>
    <?php $this->need('sidebar.php'); ?>
    <?php $this->need('footer.php'); ?>
</div>
