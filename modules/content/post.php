<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<article id="post" class="post post-type">
    <h1 class="post-title"><?php $this->title(); ?></h1>
    <div class="post-meta">
        <span class="post-author"><?php $this->author(); ?></span>
        <?php if ($this->categories): ?><span class="post-category"><?php $this->category(' / '); ?></span><?php endif; ?>
        <time class="post-publish-time" datetime="<?php $this->date('c'); ?>">
            <?php $this->date('Y-m-d H:i'); ?>
        </time>
        <?php if ($this->fields->location != ''): ?>
            <span class="post-location"><?php echo $this->fields->location; ?></span>
        <?php endif; ?>
    </div>
    <div id="post-content" class="post-content">
        <?php $this->need('/modules/content/content.php'); ?>
    </div>
    <div class="post-zan">
        <?php if ($this->author->mail): ?>
            <img class="author-avatar"  src="<?php echo getGravatarUrl($this->author->mail, 160); ?>" alt="<?php $this->author(); ?>" />
        <?php endif; ?>
        <div class="post-author"><?php $this->author(); ?></div>
        <div class="author-description">“&nbsp;<?php $this->options->description() ?>&nbsp;”</div>
        <span class="post-like" data-cid="<?php echo $this->cid; ?>" data-location="post"><i class="reborn rb-like-o"></i>&nbsp;喜欢文章</span>
        <?php $likes = getPostLikeNum($this->cid); ?>
        <div id="post-like-area-<?php echo $this->cid; ?>" class="post post-like-area<?php echo $likes ? '' : ' hidden'; ?>">
            <?php echo getPostLikeHtml($this->cid, 'post'); ?>
        </div>
    </div>
    <?php if ($this->tags): ?>
        <div class="post-tags">
            <?php foreach ($this->tags as $tag): ?>
                <div class="article-tags">
                    <?php $count = getTagCount($tag['mid']); ?>
                    <a href="<?php echo $tag['permalink']; ?>"># <?php echo $tag['name']; ?></a> <span class="article-tags-num"><?php echo $count; ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="post-footer">
        <span class="post-view">阅读&nbsp;<span id="post-view-cid-<?php echo $this->cid; ?>"><?php getPostView($this) ?></span></span>
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
