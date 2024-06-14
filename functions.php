<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// 文章自定义字段
function themeFields($layout) {
    $postType = new Typecho\Widget\Helper\Form\Element\Radio(
        'postType',
        array(
            'post' => _t('文章（默认）'),
            'shuoshuo'  => _t('说说')
        ),
        'post',
        _t('请选择文章类型'),
        _t('发布文章时的文章类型，默认为文章')
    );
    $layout->addItem($postType);
    $location = new Typecho\Widget\Helper\Form\Element\Text(
        'location',
        NULL,
        NULL,
        _t('坐标'),
        _t('发布内容所在坐标')
    );
    $layout->addItem($location);
}

// 主题设置
function themeConfig($form) {
    // 主页头像
    $avatarEmail = new Typecho\Widget\Helper\Form\Element\Text(
        'avatarEmail',
        NULL,
        NULL,
        _t('主页头像邮箱'),
        _t('主页头像邮箱，调用Gravatar头像')
    );
    $form->addInput($avatarEmail);
    $sidebarBlock = new \Typecho\Widget\Helper\Form\Element\Checkbox(
        'sidebarBlock',
        [
            'ShowRecentPosts'    => _t('显示最新文章'),
            'ShowRecentComments' => _t('显示最近回复'),
            'ShowCategory'       => _t('显示分类'),
            'ShowArchive'        => _t('显示归档'),
            'ShowOther'          => _t('显示其它杂项')
        ],
        ['ShowRecentPosts', 'ShowRecentComments', 'ShowCategory', 'ShowArchive', 'ShowOther'],
        _t('侧边栏显示')
    );

    $form->addInput($sidebarBlock->multiMode());
}

function themeInit($archive) {

}


/**
 * 获取 Gravatar 头像 URL
 *
 * @param string $email 用户的邮箱地址
 * @param int $size 头像大小，默认为 80
 * @param string $default 默认头像类型或 URL
 * @param string $rating 头像分级 (g, pg, r, x)
 * @return string Gravatar 头像 URL
 */
function getGravatarUrl($email, $size = 80, $default = 'mm', $rating = 'g'): string {
    $hash = md5(strtolower(trim($email)));
    return "https://cravatar.cn/avatar/$hash?s=$size&d=$default&r=$rating";
}

/**
 * 输出相对时间
 *
 * @param int $time 时间戳
 * @return string 相对时间字符串
 */


if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function timeAgo($time)
{
    $currentTime = time();
    $timeDifference = $currentTime - $time;

    $units = array(
        _t('年') => 29030400, // 60 * 60 * 24 * 336
        _t('月') => 2419200,  // 60 * 60 * 24 * 28
        _t('周') => 604800,   // 60 * 60 * 24 * 7
        _t('天') => 86400,    // 60 * 60 * 24
        _t('小时') => 3600,   // 60 * 60
        _t('分钟') => 60,
        _t('秒') => 1,
    );

    foreach ($units as $unit => $value) {
        if ($timeDifference >= $value) {
            $result = floor($timeDifference / $value);
            return $result . ' ' . $unit . _t('前');
        }
    }

    return _t('刚刚');
}
