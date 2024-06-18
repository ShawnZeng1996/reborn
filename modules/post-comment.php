<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<div id="comments">
    <?php if (hasComments($this->cid)) {

    } else {  ?>
        <div class="none-comment"><a class="write-comment" data-cid="<?php echo $this->cid; ?>">写评论</a></div>
    <?php } ?>
</div>
