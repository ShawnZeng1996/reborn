<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $sidebarAd = $this->options->sidebarAd; ?>
<?php if ($sidebarAd): ?>
    <section class="sidebar-adv widget">
        <h3 class="widget-title"><?php _e('广告'); ?></h3>
        <div><?php echo $sidebarAd; ?></div>
    </section>
<?php endif; ?>