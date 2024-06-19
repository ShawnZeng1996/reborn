<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<div class="container content">
    <div class="main-content">
        <?php $this->need('/modules/post.php'); ?>
    </div>
    <?php $this->need('sidebar.php'); ?>
    <?php $this->need('footer.php'); ?>
</div>
