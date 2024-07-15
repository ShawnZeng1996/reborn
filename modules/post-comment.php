<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<div id="comments" class="post-comment-area-<?php echo $this->cid; ?>">
    <?php if (hasComments($this->cid)) { ?>
        <div class="has-comment flex">
            <span id="comment-title">评论&nbsp;<span class="comment-num"><?php echo hasComments($this->cid); ?></span></span>
            <a class="write-comment" data-cid="<?php echo $this->cid; ?>">写评论</a>
        </div>
        <div class="form-place"></div>
        <?php $this->need('/modules/comment-form.php'); ?>
        <ul id="comments-cid-<?php echo $this->cid; ?>">
            <?php
                $comments = getCommentsWithReplies($this->cid);
                renderPostComments($comments);
            ?>
        </ul>
    <?php } else {  ?>
        <div class="form-place"></div>
        <?php $this->need('/modules/comment-form.php'); ?>
        <div class="none-comment"><a class="write-comment" data-cid="<?php echo $this->cid; ?>">写评论</a></div>
    <?php } ?>
</div>

