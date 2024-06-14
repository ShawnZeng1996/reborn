<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<footer id="footer" class="clear-both">
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> <?php $this->options->title(); ?>. <?php _e('由 <a href="http://typecho.org">Typecho</a> 强力驱动'); ?>.</p>
    </div>
</footer>
<?php $this->footer(); ?>
</body>
</html>
