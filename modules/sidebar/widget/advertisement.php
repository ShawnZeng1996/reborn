<?php
// 广告
if ($this->options->sidebarAd): ?>
    <section class="sidebar-adv widget">
        <span class="adv"><?php _e('广告'); ?></span>
        <div><?php echo $this->options->sidebarAd; ?></div>
    </section>
<?php endif;