<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
        <footer id="footer" class="clear-both">
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> <?php $this->options->title(); ?>. Typecho theme <?php echo THEME_NAME . ' ' . THEME_VERSION; ?> by Shawn. All rights reserved.</p>
            </div>
        </footer>

    <?php $this->footer(); ?>
    </body>
</html>
