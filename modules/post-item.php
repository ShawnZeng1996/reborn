<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<div class="post post-type flex">
    <div class="post-author-avatar">
        <?php if ($this->author->mail): ?>
            <img src="<?php echo getGravatarUrl($this->author->mail, 80); ?>" alt="<?php $this->author(); ?>" class="author-avatar" />
        <?php endif; ?>
    </div>
    <div class="post-content flex-1">
        <div class="post-author"><?php $this->author(); ?></div>
        <div class="post-excerpt"><?php $this->excerpt(500); ?></div>
        <a class="post-item" href="<?php $this->permalink(); ?>">
            <img src="<?php echo getPostThumbnail($this->cid); ?>" alt="<?php $this->title(); ?>" class="post-thumbnail" />
            <h3 class="post-title ellipsis"><?php $this->title(); ?></h3>
        </a>
        <?php $this->need('/modules/meta-item.php'); ?>
    </div>
</div>
