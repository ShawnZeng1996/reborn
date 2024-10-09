<?php

/**
 * 获取 Gravatar 头像 URL
 *
 * @param string $email 用户的邮箱地址
 * @param int $size 头像大小，默认为 80
 * @return string Gravatar 头像 URL
 */
function getGravatarUrl(string $email, int $size = 80): string {
    if(\Utils\Helper::options()->gravatarPrefix) {
        $gravatarUrl = \Utils\Helper::options()->gravatarPrefix;
    } else {
        $gravatarUrl = 'https://cravatar.cn/avatar/';
    }
    $hash = md5(strtolower(trim($email)));
    $gravatarUrl = $gravatarUrl . $hash;
    // 默认头像列表
    $defaultAvatars = [
        '/assets/img/欢乐马.jpg',
        '/assets/img/神经蛙.jpg',
        '/assets/img/阿白.jpg',
        '/assets/img/momo.jpg',
        '/assets/img/哄哄.jpg'
    ];
    // 随机选择一个默认头像
    $defaultAvatarUrl = \Utils\Helper::options()->themeUrl . $defaultAvatars[array_rand($defaultAvatars)];
    $imgUrl = $gravatarUrl . "?s=$size&d=" . urlencode($defaultAvatarUrl) . "&r=G";
    $headers = @get_headers($imgUrl);
    if ($headers && strpos($headers[0], '500') === false) {
        return $imgUrl;
    } else {
        return $defaultAvatarUrl;
    }
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

function updateOldCommentsRegion() {
    // 获取Typecho数据库实例
    $db = \Typecho\Db::get();
    // 获取数据库表前缀
    $prefix = $db->getPrefix();
    // 构建查询语句，查找没有地区信息的历史评论
    $select = $db->select('coid', 'ip')
        ->from($prefix . 'comments')
        ->where('region IS NULL');
    // 每次获取5条记录
    $comments = $db->fetchAll($select->limit(5));

    // 如果没有需要处理的评论，则返回
    if (empty($comments)) {
        return;
    }

    // 遍历这些评论，获取并更新地区信息
    foreach ($comments as $comment) {
        $region = getRegionByIp($comment['ip']); // 调用API获取地区信息
        if ($region !== '未知') {
            // 更新数据库
            $update = $db->update($prefix . 'comments')
                ->rows(array('region' => $region))
                ->where('coid = ?', $comment['coid']);
            $db->query($update);
        }
    }

    // 等待1秒后再处理下一批评论
    sleep(1);
    // 递归调用函数继续处理剩余的评论
    updateOldCommentsRegion();
}

function formatNumber($number) {
    if ($number >= 10000) {
        return number_format($number / 10000, 1) . 'w';
    } elseif ($number >= 1000) {
        return number_format($number / 1000, 1) . 'k';
    } else {
        return $number;
    }
}
