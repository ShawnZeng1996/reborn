<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<div id="comments">
    <?php if (hasComments($this->cid)) { ?>
        <div class="has-comment flex">
            <span id="comment-title"><span class="comment-num"><?php echo hasComments($this->cid); ?></span>条评论</span>
            <a class="write-comment" data-cid="<?php echo $this->cid; ?>">写评论</a>
        </div>
        <div class="form-place"></div>
        <ul id="comments-cid-<?php echo $this->cid; ?>">
            <?php
                $comments = getCommentsWithReplies($this->cid);
                renderPostComments($comments);
            ?>
        </ul>
    <?php } else {  ?>
        <div class="form-place"></div>
        <div class="none-comment"><a class="write-comment" data-cid="<?php echo $this->cid; ?>">写评论</a></div>
    <?php } ?>
</div>

