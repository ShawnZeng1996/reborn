<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
                        <div class="post-meta">
                            <span class="post-location post-meta-1"><?php echo $this->fields->location; ?></span>
                            <div class="post-meta-2 relative">
                                <span>
                                    <time class="post-publish-time" datetime="<?php $this->date('c'); ?>"><?php echo formatRelativeTime($this->created); ?></time>
                                    <span class="post-view">阅读&nbsp;<span id="post-view-cid-<?php echo $this->cid; ?>"><?php getPostView($this) ?></span></span>
                                </span>
                                <span class="reborn rb-twodots post-more"></span>
                                <div class="post-action-container absolute">
                                    <div class="post-action absolute flex" data-cid="<?php echo $this->cid; ?>">
                                        <a class="post-like flex-1 relative unselectable" data-cid="<?php echo $this->cid; ?>" data-location="index">
                                            <div class="cancel"><span class="reborn rb-heart"></span>&nbsp;<span class="underline">取消</span></div>
                                            <div class="like"><span class="reborn rb-heart-o"></span>&nbsp;<span class="underline">赞</span></div>
                                        </a>
                                        <a class="post-comment flex-1 unselectable" data-cid="<?php echo $this->cid; ?>" data-location="index"><span class="reborn rb-comments"></span>&nbsp;<span class="underline">评论</span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="post-meta-3">
                                <?php $likes = getPostLikeNum($this->cid); ?>
                                <div id="post-like-area-<?php echo $this->cid; ?>" class="post-like-area<?php echo $likes ? '' : ' hidden'; ?>">
                                    <?php echo getPostLikeHtml($this->cid); ?>
                                </div>
                                <div id="post-comment-area-<?php echo $this->cid; ?>"  class="post-comment-area-<?php echo $this->cid; ?> post-comment-area<?php if(!haveComments($this->cid)) echo ' hidden'; ?>">
                                    <?php $this->need('/modules/comment/comment-form.php'); ?>
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
                        </div>
