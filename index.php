<?php
/**
 * Theme reborn for Typecho
 *
 * @package reborn
 * @author Shawn
 * @version 1.0.0
 * @link https://shawnzeng.com
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
            <div class="rb-site-info">
                <div class="rb-site-info-inner">
                    <?php $siteLogo = $this->options->avatarEmail ?: ''; ?>
                    <img class="rb-site-logo" alt="站点头像" src="<?php echo getGravatarUrl($siteLogo, 160); ?>" />
                    <h1 class="rb-site-title"><?php $this->options->title() ?></h1>
                    <p class="rb-site-description"><?php $this->options->description() ?></p>
                </div>
            </div>
        </header>
        <div id="container">
            <div class="sidebar">
                <?php $this->need('/modules/sidebar/sidebar-index.php'); ?>
            </div>
            <div class="main-content">
                <?php echo getRegionByIp('140.207.25.226'); ?>
                <?php while($this->next()): ?>
                    <?php if ($this->fields->postType == 'shuoshuo'): ?>
                        <!-- 说说 -->
                        <?php $this->need('/modules/index/shuoshuo-item.php'); ?>
                    <?php else: ?>
                        <!-- 文章 -->
                        <?php $this->need('/modules/index/post-item.php'); ?>
                    <?php endif; ?>
                <?php endwhile; ?>
                <div class="pagination">
                    <?php $this->pageLink('点击查看更多','next'); ?>
                </div>
            </div>
            <?php $this->need('footer.php'); ?>
        </div>
    </body>
</html>
<?php 