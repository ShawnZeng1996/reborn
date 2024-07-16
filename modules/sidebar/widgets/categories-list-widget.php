<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<section class="widget">
                <h3 class="widget-title"><?php _e('分类'); ?></h3>
<?php \Widget\Metas\Category\Rows::alloc()->listCategories('wrapClass=widget-list'); ?>
</section>
