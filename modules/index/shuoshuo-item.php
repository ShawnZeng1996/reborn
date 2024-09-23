<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
                <article class="post-type shuoshuo flex">
                    <div class="post-author-avatar">
                        <?php $authorMail = $this->author->mail ?: ''; ?>
                        <img src="<?php echo getGravatarUrl($this->author->mail, 80); ?>" alt="<?php $this->author(); ?>" />
                    </div>
                    <div class="post-content">
                        <span class="post-author"><?php $this->author(); ?></span>
                        <div class="shuoshuo-item">
                            <?php $this->need('/modules/content/content.php'); ?>
                            <a class="post-link" href="<?php $this->permalink(); ?>">查看全文</a>
                        </div>
                        <?php $this->need('/modules/index/meta-item.php'); ?>
                    </div>
                </article>