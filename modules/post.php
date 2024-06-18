<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
$likeData = getLikeNumByCid($this->cid);
$isLiked = isLikeByCid($this->cid);
$likes = $likeData['likes'];
?>
<article class="post post-type">
    <h3 class="post-title"><?php $this->title(); ?></h3>
    <div class="post-meta">
        <span class="post-author"><?php $this->author(); ?></span>
        <time class="post-publish-time" datetime="<?php $this->date('c'); ?>">
            <?php $this->date('Y-m-d H:i'); ?>
        </time>
        <?php if ($this->fields->location != ''): ?>
            <span class="post-location"><?php echo $this->fields->location; ?></span>
        <?php endif; ?>
    </div>
    <div id="post-content" class="post-content"><?php $this->content(); ?></div>
    <div class="post-footer flex">
        <span class="post-view">阅读&nbsp;<span id="post-view-cid-<?php echo $this->cid; ?>"><?php getPostView($this) ?></span></span>
        <a class="post-zan unselectable" data-cid="<?php echo $this->cid; ?>">
            <?php if ($isLiked): ?>
                <span class="reborn rb-like"></span>&nbsp;<span class="zan-num"><?php echo $likes; ?></span>
            <?php else: ?>
                <span class="reborn rb-like-o"></span>&nbsp;<span class="zan-num"><?php echo $likes; ?></span>
            <?php endif; ?>
        </a>
    </div>
</article>
<?php $this->need('/modules/post-comment.php'); ?>

