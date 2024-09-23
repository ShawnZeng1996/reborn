<?php
function threadedComments($comment, $options) {
    //$currentLevel = $comment->levels;
    echo '<li id="comment-' . $comment->coid . '" class="comment-item">';
    echo '<div class="comment-item-header flex">';
    if (!empty($comment->url)) {
        $hasLink = ' href="' . ensureAbsoluteUrl($comment->url) . '" target="_blank" rel="nofollow"';
    } else {
        $hasLink = '';
    }
    echo '<a class="comment-author-avatar"' . $hasLink . '><img src="' . getGravatarUrl($comment->mail, 80) . '" alt="' . $comment->author . '"></a>';
    echo '<div class="flex flex-1 comment-body">';
    echo '<div class="flex-1">';
    echo '<a class="comment-author"' . $hasLink . '>' . $comment->author . '</a>';
    if ($comment->authorId) {
        echo '<span class="admin m-r-5">作者</span>';
    }
    echo '<span class="comment-region">' . getRegionByCoid($comment->coid) . '</span>';
    echo '<time class="comment-time" datetime="' . $comment->created . '">' . formatRelativeTime($comment->created) . '</time>';
    echo '</div>';
    echo '<a class="post-comment" data-cid="' . $comment->cid . '" data-coid="' . $comment->coid . '" data-name="' . $comment->author . '" data-location="post">回复</a>';
    echo '<a class="comment-like" data-coid="' . $comment->coid . '" >' . getCommentLikeNum($comment->coid) . '&nbsp;<i class="reborn rb-like-o"></i></a>';
    echo '<div class="comment-content post-comment" data-cid="' . $comment->cid . '" data-coid="' . $comment->coid . '" data-name="' . $comment->author . '" data-location="post">';
    commentReply($comment->coid);
    echo removeCommentPar(commentEmojiReplace($comment->content)) . '</div>';
    echo '</div>';
    echo '</div>';
    if ($comment->children) {
        $comment->threadedComments($options);
    }
    echo '</li>';
}
