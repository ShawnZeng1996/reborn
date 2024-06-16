<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
        <footer id="footer" class="clear-both">
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> <?php $this->options->title(); ?>. <?php _e('由 <a href="http://typecho.org">Typecho</a> 强力驱动'); ?>.</p>
            </div>
        </footer>
    <script type="text/javascript" src="<?php $this->options->themeUrl('js/jquery-3.7.1.min.js'); ?>"></script>
    <script type="text/javascript">
        var themeUrl = '<?php $this->options->themeUrl(); ?>'; // 定义主题URL变量
    </script>
    <script type="text/javascript" src="<?php $this->options->themeUrl('js/app.js'); ?>"></script>
    <?php $this->footer(); ?>
    </body>
</html>
