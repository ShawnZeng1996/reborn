<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
                <div class="shuoshuo post-type flex">
                    <div class="post-author-avatar">
                        <?php if ($this->author->mail): ?>
                            <img src="<?php echo getGravatarUrl($this->author->mail, 80); ?>" alt="<?php $this->author(); ?>" class="author-avatar" />
                        <?php endif; ?>
                    </div>
                    <div class="post-content flex-1">
                        <div class="post-author"><?php $this->author(); ?></div>
                        <a class="shuoshuo-item" href="<?php $this->permalink(); ?>">
                            <?php $this->content(); ?>
                        </a>
                        <?php $this->need('/modules/meta-item.php'); ?>
                    </div>
                </div>