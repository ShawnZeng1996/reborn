<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
</header>
<div class="container content">
    <div class="main-content">
        <?php if ($this->fields->postType == 'shuoshuo'): ?>
            <!-- 说说 -->
            <?php $this->need('/modules/shuoshuo.php'); ?>
        <?php else: ?>
            <!-- 文章 -->
            <?php $this->need('/modules/post.php'); ?>
        <?php endif; ?>
    </div>
    <?php $this->need('/modules/sidebar/sidebar-post.php'); ?>
    <?php $this->need('footer.php'); ?>
</div>