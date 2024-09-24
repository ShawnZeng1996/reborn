<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<?php $this->need('/modules/sidebar/widget/recent-games.php'); ?>
<?php
// 判断是否需要生成目录
$showToc = $this->is('post');
// 获取广告内容
$sidebarAd = $this->options->sidebarAd;
// 判断是否需要生成广告
$showAd = !empty($sidebarAd);
// 只有当需要生成目录或广告时才生成 <div id="sticky">
if ($showToc || $showAd): ?>
    <div id="sticky">
        <?php if ($showToc): ?>
            <section class="widget toc">
                <h3 class="widget-title"><?php _e('目录'); ?></h3>
                <div class="widget-toc">
                    <?php echo generateToc($this->content); ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($showAd): ?>
            <?php $this->need('/modules/sidebar/widget/advertisement.php'); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>
