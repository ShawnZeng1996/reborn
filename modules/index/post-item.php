<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
                <article class="post-type shuoshuo flex">
                    <?php if ($this->fields->postSticky == 'sticky') {
                        echo '<span class="post-sticky">置顶</span>';
                    } ?>
                    <div class="post-author-avatar">
                        <?php $authorMail = $this->author->mail ?: ''; ?>
                        <img src="<?php echo getGravatarUrl($this->author->mail, 80); ?>" alt="<?php $this->author(); ?>" />
                    </div>
                    <div class="post-content">
                        <span class="post-author"><?php $this->author(); ?></span>
                        <div class="post-item">
                            <div class="post-excerpt"><?php $this->excerpt(500); ?></div>
                            <a class="post-unit" href="<?php $this->permalink(); ?>">
                                <img src="<?php echo getPostThumbnail($this->cid); ?>" alt="<?php $this->title(); ?>" class="post-thumbnail">
                                <h3 class="post-title ellipsis"><?php $this->title(); ?></h3>
                            </a>
                        </div>
                        <?php $this->need('/modules/index/meta-item.php'); ?>
                    </div>
                </article>