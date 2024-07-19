<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; // ?>
<!DOCTYPE HTML>
<html lang="ch">
<head>
    <meta charset="<?php $this->options->charset(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'author'    =>  _t('%s 发布的文章')
        ), '', ' - '); ?><?php $this->options->title(); ?></title>
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/font/iconfont.css'); ?>?v=<?php echo THEME_VERSION; ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('lib/highlight@11.9.0/css/atom-one-light.min.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('lib/fancybox@3.5.7/jquery.fancybox.min.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('style.css'); ?>?v=<?php echo THEME_VERSION; ?>">
    <script type="text/javascript" src="<?php $this->options->themeUrl('lib/jquery@3.7.1/jquery-3.7.1.min.js'); ?>"></script>
    <script type="text/javascript">
        var themeUrl = '<?php $this->options->themeUrl(); ?>'; // 定义主题URL变量
        var isLogin = <?php echo $this->user->hasLogin() ? 'true' : 'false'; ?>;
        <?php if($this->user->hasLogin()) {
            $user = $this->user;
            $uid = json_encode($user->uid); // 用户网址
            $avatar = json_encode(getGravatarUrl($user->mail));
        } else {
            $uid = json_encode(0);
            $avatar = json_encode('');
        } ?>
        var userId = <?php echo $uid; ?>;
        var userAvatar = <?php echo $avatar; ?>;
        var commentsRequireMail = <?php echo $this->options->commentsRequireMail; ?>;
        var commentsRequireURL = <?php echo $this->options->commentsRequireURL; ?>;
    </script>
    <script src="<?php $this->options->themeUrl('lib/highlight@11.9.0/js/highlight.min.js'); ?>"></script>
    <script src="<?php $this->options->themeUrl('lib/fancybox@3.5.7/jquery.fancybox.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php $this->options->themeUrl('assets/js/app.js'); ?>?v=<?php echo THEME_VERSION; ?>"></script>
    <?php $this->header(); ?>
</head>
<body>
<header id="header">
    <nav id="nav">
        <ul class="container">
            <li><a href="<?php $this->options->siteUrl(); ?>"><?php _e('首页'); ?></a></li>
            <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
            <?php while($pages->next()): ?>
                <li><a href="<?php $pages->permalink(); ?>" title="<?php $pages->title(); ?>"><?php $pages->title(); ?></a></li>
            <?php endwhile; ?>
        </ul>
    </nav>


