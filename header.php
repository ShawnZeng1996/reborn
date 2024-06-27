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
    <link rel="stylesheet" href="<?php $this->options->themeUrl('font/iconfont.css'); ?>?v=<?php echo THEME_VERSION; ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('css/atom-one-dark.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('style.css'); ?>?v=<?php echo THEME_VERSION; ?>">
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
    <div id="site-info" class="container relative">
        <?php echo '<img id="site-logo" class="absolute" src="' . getGravatarUrl($this->options->avatarEmail, 160) . '" alt="头像" />'; ?>
        <h1 id="site-title"><a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title() ?></a></h1>
        <p id="site-description" class="absolute"><?php $this->options->description() ?></p>
    </div>
</header>

