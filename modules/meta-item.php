<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
$likeData = getLikeNumByCid($this->cid);
$isLiked = isLikeByCid($this->cid);
$likes = $likeData['likes'];
?>
<?php if ($this->fields->location != ''): ?>
    <div class="post-meta-1 post-location"><?php echo $this->fields->location; ?></div>
<?php endif; ?>
<div class="post-meta-2 relative">
            <span>
                <time class="post-publish-time" datetime="<?php $this->date('c'); ?>"><?php echo timeAgo($this->created); ?></time>
                <span class="post-view">阅读&nbsp;<span id="post-view-cid-<?php echo $this->cid; ?>"><?php getPostView($this) ?></span></span>
            </span>
    <span class="reborn rb-twodots post-more"></span>
    <div class="post-action-container absolute">
        <div class="post-action absolute flex">
            <a class="post-like flex-1 relative unselectable" data-cid="<?php echo $this->cid; ?>">
                <?php if ($isLiked): ?>
                    <span class="reborn rb-heart"></span>&nbsp;<span class="underline">取消</span>
                <?php else: ?>
                    <span class="reborn rb-heart-o"></span>&nbsp;<span class="underline">赞</span>
                <?php endif; ?>
            </a>
            <a class="post-comment flex-1 unselectable" data-cid="<?php echo $this->cid; ?>"><span class="reborn rb-comments"></span>&nbsp;<span class="underline">评论</span></a>
        </div>
    </div>
</div>
<div class="post-meta-3">
    <div id="post-like-area-<?php echo $this->cid; ?>" class="post-like-area<?php echo $likes ? '' : ' hidden'; ?>"><span class="reborn rb-heart-o"></span>&nbsp;<?php echo $likes; ?>人喜欢</div>
    <div id="post-comment-area-<?php echo $this->cid; ?>"  class="post-comment-area-<?php echo $this->cid; ?> post-comment-area<?php if(!hasComments($this->cid)) echo ' hidden'; ?>">
        <?php $this->need('/modules/comment-form.php'); ?>
        <!-- 评论列表 -->
        <ul id="comments-cid-<?php echo $this->cid; ?>">
            <?php
            if ($this->is('single')) {
                $comments = getCommentsWithReplies($this->cid, 0);
                if ($comments) {
                    renderComments($comments, $this->permalink, 0);
                }
            } else {
                $comments = getCommentsWithReplies($this->cid, 0, 6);
                if ($comments) {
                    renderComments($comments, $this->permalink);
                }
            }
            ?>
        </ul>
    </div>
</div>
