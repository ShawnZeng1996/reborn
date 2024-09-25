<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<?php $this->need('/modules/sidebar/widget/admin-card.php'); ?>
<?php $this->need('/modules/sidebar/widget/recent-posts.php'); ?>
<?php $this->need('/modules/sidebar/widget/recent-comments.php'); ?>
<?php $this->need('/modules/sidebar/widget/category.php'); ?>
<?php
// 获取广告内容
$sidebarAd = $this->options->sidebarAd;
// 判断是否需要生成广告
$showAd = !empty($sidebarAd);
// 只有当需要生成目录或广告时才生成 <div id="sticky">
if ($showAd): ?>
    <div id="sticky">
        <?php $this->need('/modules/sidebar/widget/advertisement.php'); ?>
    </div>
<?php endif; ?>






