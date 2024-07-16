<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
    <div id="site-info" class="container relative">
        <?php echo '<img id="site-logo" class="absolute" src="' . getGravatarUrl($this->options->avatarEmail, 160) . '" alt="头像" />'; ?>
        <h1 id="site-title"><a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title() ?></a></h1>
        <p id="site-description" class="absolute"><?php $this->options->description() ?></p>
    </div>
</header>
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

    <?php $this->need('/modules/sidebar-index.php'); ?>
    <?php $this->need('footer.php'); ?>
</div>
