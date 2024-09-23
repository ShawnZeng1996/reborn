<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

const __THEME_NAME__ = 'reborn';
const __THEME_VERSION__ = '1.2.2';
const __GRAVATAR_PREFIX__ = 'https://cravatar.cn/avatar/';

// 自定义字段、配置文件
require_once("functions/config.php");
// 通用功能
require_once("functions/function.php");
// 文章相关功能
require_once("functions/post.php");
// 评论相关功能
require_once("functions/comment.php");
// 路由相关功能
require_once("functions/route.php");
// 编辑器相关功能
require_once("functions/editor.php");

