<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
        <footer id="footer" class="clear-both">
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> <?php $this->options->title(); ?>. <?php _e('由 <a href="http://typecho.org">Typecho</a> 强力驱动'); ?>.</p>
            </div>
        </footer>
    <script type="text/javascript" src="<?php $this->options->themeUrl('js/jquery-3.7.1.min.js'); ?>"></script>
    <script type="text/javascript">
        var themeUrl = '<?php $this->options->themeUrl(); ?>'; // 定义主题URL变量
        alert(themeUrl);
        var isLogin = <?php echo $this->user->hasLogin() ? 'true' : 'false'; ?>;
        <?php if($this->user->hasLogin()) {
            $user = $this->user;
            $name = json_encode($user->screenName); // 用户昵称
            $mail = json_encode($user->mail); // 用户邮箱
            $url = json_encode($user->url); // 用户网址
            $uid = json_encode($user->uid); // 用户网址
            $avatar = json_encode(getGravatarUrl($user->mail));
        } else {
            $name = json_encode('');
            $mail = json_encode('');
            $url = json_encode('');
            $uid = json_encode(0);
            $avatar = json_encode('');
        } ?>
        var userName = <?php echo $name; ?>;
        var userEmail = <?php echo $mail; ?>;
        var userUrl = <?php echo $url; ?>;
        var userId = <?php echo $uid; ?>;
        var userAvatar = <?php echo $avatar; ?>;
        var commentsRequireMail = <?php echo $this->options->commentsRequireMail; ?>;
        var commentsRequireURL = <?php echo $this->options->commentsRequireURL; ?>;
    </script>
    <script type="text/javascript" src="<?php $this->options->themeUrl('js/app.js'); ?>?v=<?php echo THEME_VERSION; ?>"></script>
    <?php $this->footer(); ?>
    </body>
</html>
