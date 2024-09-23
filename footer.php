<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
        <footer id="footer" class="clear-both">
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> <?php $this->options->title(); ?>. Typecho theme <?php echo '<a href="https://shawnzeng.com" target="_blank">' . __THEME_NAME__ . '</a>&nbsp;'  . __THEME_VERSION__; ?> by <a href="https://shawnzeng.com" target="_blank">Shawn</a>. All rights reserved.</p>
            </div>
        </footer>
    <?php $this->footer(); ?>

