<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<?php $this->need('/modules/sidebar/widget/recent-games.php'); ?>
<?php
// 判断是否需要生成目录
$showToc = $this->is('post');
// 只有当需要生成目录或广告时才生成 <div id="sticky">
if ($showToc): ?>
    <div id="sticky">
        <section class="widget toc">
            <h3 class="widget-title"><?php _e('目录'); ?></h3>
            <div class="widget-toc">
                <?php echo generateToc($this->content); ?>
            </div>
        </section>
    </div>
<?php endif; ?>
<?php $this->need('/modules/sidebar/widget/advertisement.php'); ?>
