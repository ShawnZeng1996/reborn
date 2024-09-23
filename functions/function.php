<?php

/**
 * 获取 Gravatar 头像 URL
 *
 * @param string $email 用户的邮箱地址
 * @param int $size 头像大小，默认为 80
 * @return string Gravatar 头像 URL
 */
function getGravatarUrl(string $email, int $size = 80): string {
    $gravatarUrl = __GRAVATAR_PREFIX__;
    $hash = md5(strtolower(trim($email)));
    $gravatarUrl = $gravatarUrl . $hash;
    // 自定义默认头像 URL
    $defaultAvatarUrl = \Utils\Helper::options()->themeUrl . '/assets/img/default-avatar.webp';
    return $gravatarUrl . "?s=$size&d=" . urlencode($defaultAvatarUrl) . "&r=g";
}

/**
 * 输出相对时间
 *
 * @param int $time 时间戳
 * @return string 相对时间字符串
 */
function formatRelativeTime(int $time): string {
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
            return $result . $unit . _t('前');
        }
    }
    return _t('刚刚');
}

function getTagCount($mid) {
    $db = Typecho_Db::get();
    // 构建查询，排除 typecho_fields 表中存在 'shuoshuo' 类型的文章
    $query = $db->select(array('COUNT(DISTINCT table.relationships.cid)' => 'num'))
        ->from('table.relationships')
        ->join('table.contents', 'table.relationships.cid = table.contents.cid', Typecho_Db::LEFT_JOIN)
        ->join('table.fields', 'table.contents.cid = table.fields.cid', Typecho_Db::LEFT_JOIN)
        ->where('table.relationships.mid = ?', $mid)
        ->where('table.contents.type = ?', 'post')
        ->where('table.fields.name = ?', 'postType')
        ->where('table.fields.str_value != ?', 'shuoshuo')
        ->group('table.relationships.mid');
    $result = $db->fetchObject($query);
    return $result ? $result->num : 0;
}

