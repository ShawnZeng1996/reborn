<?php if (!empty($this->options->sidebarBlock) && in_array('ShowRecentComments', $this->options->sidebarBlock)): ?>
    <section class="widget" id="sidebar-comment-list">
        <h3 class="widget-title"><?php _e('最新评论'); ?></h3>
        <ul class="widget-list">
            <?php $this->widget('Widget_Comments_Recent', 'ignoreAuthor=true')->to($comments); ?>
             <?php while ($comments->next()): ?>
                <li>
                    <a class="flex comment-item"  href="<?php $comments->permalink(); ?>">
                        <img class="comment-author-avatar" src="<?php $email = $comments->mail ?: ''; ;echo getGravatarUrl($email); ?>" alt="<?php $comments->author(false); ?>">
                        <div class="flex-1">
                            <div class="comment-meta flex">
                                <div class="comment-author"><?php $comments->author(false); ?></div>
                                <time class="comment-time" datetime="<?php echo $comments->created; ?>"><?php echo formatRelativeTime($comments->created); ?></time>
                            </div>
                            <div class="comment-content"><?php echo commentEmojiReplace($comments->content); ?></div>
                        </div>
                    </a>
                </li>
             <?php endwhile; ?>
        </ul>
    </section>
<?php endif; ?>
