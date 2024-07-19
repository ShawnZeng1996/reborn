<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $sidebarAd = $this->options->sidebarAd; ?>
<?php if ($sidebarAd): ?>
    <section class="sidebar-adv widget">
        <span class="adv"><?php _e('广告'); ?></span>
        <div><?php echo $sidebarAd; ?></div>
    </section>
<?php endif; ?>