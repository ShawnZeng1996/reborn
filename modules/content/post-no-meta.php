<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<article id="post" class="post post-type">
    <div id="post-content" class="post-content">
        <?php $this->need('/modules/content/content.php'); ?>
    </div>
</article>
<?php if($this->options->postAd) {
    echo '<div class="post-adv">';
    $this->options->postAd();
    echo '</div>';
} ?>
<div id="comments" class="post post-comment-area-<?php echo $this->cid; ?>">
    <?php if (haveComments($this->cid)) { ?>
        <div class="has-comment flex">
            <span id="comment-title">评论&nbsp;<span class="comment-num"><?php echo haveComments($this->cid); ?></span></span>
            <a class="post-comment" data-cid="<?php echo $this->cid; ?>" data-location="post">写评论</a>
        </div>
        <?php $this->need('/modules/comment/comment-form.php'); ?>
        <?php $this->comments()->to($comments);?>
        <?php $this->need('/modules/comment/post-comment-item.php');?>
        <?php $comments->listComments();?>
        <?php $comments->pageNav('上一页', '下一页');?>
    <?php } else {  ?>
        <?php $this->need('/modules/comment/comment-form.php'); ?>
        <div class="none-comment"><a class="post-comment" data-cid="<?php echo $this->cid; ?>" data-location="post">写评论</a></div>
    <?php } ?>

</div>

