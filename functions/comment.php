<?php

/**
 * 根据IP地址获取地区信息。
 *
 * @param string $ip      要查询的IP地址
 *
 * @return string 返回根据IP地址获取的地区信息。如果IP地址对应的国家不是中国，则返回国家名称。
 *                如果IP地址对应的国家是中国，则返回去除“省”或“市”后缀的省份名称（如果有）。
 */
function getRegionByIp(string $ip): string {
    $apiKey = \Utils\Helper::options()->tencentMapApiKey;
    if (!$apiKey) return '未知';
    $url = "https://apis.map.qq.com/ws/location/v1/ip?key={$apiKey}&ip={$ip}";
    $response = file_get_contents($url);
    if ($response === FALSE) {
        return "未知";
    }
    $data = json_decode($response, true);
    if ($data['status'] !== 0) {
        return "未知";
    }
    if ($data['result']['ad_info']['nation_code']!=156) {
        return $data['result']['ad_info']['nation'];
    } else {
        $province = $data['result']['ad_info']['province'];
        if (mb_substr($province, -1) === '省' || mb_substr($province, -1) === '市')
            return mb_substr($province, 0, mb_strlen($province) - 1);
        else
            return $province;
    }
}

/**
 * 根据评论的coid获取对应的地区信息。
 *
 * 如果地区信息为空，则根据IP地址获取地区信息，并更新数据库中的对应记录。
 *
 * @param int $coid 评论的ID
 *
 * @return string 返回对应的地区信息。如果未找到对应的地区信息，并且IP地址也无法获取，则返回空字符串。
 * @throws \Typecho\Db\Exception
 */
function getRegionByCoid(int $coid): string {
    // 获取Typecho数据库实例
    $db = \Typecho\Db::get();
    // 获取数据库表前缀
    $prefix = $db->getPrefix();
    // 构建查询语句，从comments表中获取coid对应的地区信息和IP地址
    $select = $db->select('region', 'ip')
        ->from($prefix . 'comments')
        ->where('coid = ?', $coid);
    // 执行查询并获取结果
    $result = $db->fetchRow($select);
    // 检查结果是否存在，并且地区信息是否为空
    $apiKey = \Utils\Helper::options()->tencentMapApiKey;
    if ($result && empty($result['region'])) {
        if ($apiKey) {
            // 调用getRegionByIp获取地区信息
            $newRegion = getRegionByIp($result['ip']);
            // 更新数据库中的地区信息
            $update = $db->update($prefix . 'comments')
                ->rows(array('region' => $newRegion))
                ->where('coid = ?', $coid);
            $db->query($update);
            // 返回新的地区信息
            return $newRegion;
        } else {
            return '未知';
        }
    }
    // 如果结果存在，且地区信息不为空，返回现有地区信息，否则返回空字符串
    return $result ? $result['region'] : '';
}

/**
 * 获取点赞数量
 *
 * @param int $coid 文章的 cid
 * @return int 点赞数量
 * @throws \Typecho\Db\Exception
 */
function getCommentLikeNum(int $coid): int
{
    $db = \Typecho\Db::get();
    $prefix = $db->getPrefix();
    try {
        // 查询出点赞数量
        $likes = $db->fetchRow($db->select('likes')->from('table.comments')->where('coid = ?', $coid));
        // 返回点赞数量和记录信息
        return $likes['likes'];
    } catch (Exception $e) {
        // 记录错误信息
        error_log('Database Query Error: ' . $e->getMessage());
        return 0;
    }
}

/**
 * 判断文章是否有评论
 *
 * @param int $cid 文章的 cid
 * @return bool 是否有评论
 * @throws \Typecho\Db\Exception
 */
function haveComments(int $cid): int {
    $db = \Typecho\Db::get();
    $prefix = $db->getPrefix();
    // 查询评论数量
    $comments = $db->fetchRow($db->select('COUNT(*) AS count')->from('table.comments')->where('cid = ?', $cid)->where('status = ?', 'approved'));
    return $comments['count'] ;
}

function ensureAbsoluteUrl($url) {
    if (empty($url)) {
        return '#'; // 如果 URL 为空，返回锚点链接
    }
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        return 'http://' . $url;  // 默认为 http
    }
    return $url;
}

/**
 * 获取指定文章的评论，包括子评论
 *
 * @param int $cid 文章的 cid
 * @param int $parent 父评论的 coid，默认 0 表示顶级评论
 * @param null $limit 限制顶级评论数量，默认无限制
 * @return array 评论列表
 * @throws \Typecho\Db\Exception
 */
function getCommentsWithReplies(int $cid, int $parent = 0, $limit = null): array
{
    $db = \Typecho\Db::get();
    $options = \Typecho\Widget::widget('Widget_Options');
    $commentsOrder = $options->commentsOrder; // 获取评论排序方式
    $select = $db->select('coid', 'author', 'authorId', 'ownerId', 'mail', 'text', 'created', 'parent', 'url', 'cid')
        ->from('table.comments')
        ->where('cid = ?', $cid)
        ->where('parent = ?', $parent)
        ->where('type = ?', 'comment')
        ->where('status = ?', 'approved')
        ->order('created', $commentsOrder); // 动态设置排序方式
    if ($limit !== null) {
        $select->limit($limit);
    }
    $comments = $db->fetchAll($select);
    foreach ($comments as &$comment) {
        $comment['replies'] = getCommentsWithReplies($cid, $comment['coid']);
    }
    return $comments;
}

/**
 * 递归渲染评论
 *
 * @param array $comments 评论列表
 * @param string $link 文章的链接
 * @param int $maxTopLevelComments 显示的最大顶级评论数量
 */
function renderComments(array $comments, string $link, int $maxTopLevelComments = 5): void
{
    $commentCount = count($comments);
    $displayCount = 0;
    $showAll = $maxTopLevelComments === 0;error_log("showAll: " . $showAll);
    foreach ($comments as $comment) {
        if ($showAll || $displayCount < $maxTopLevelComments) {
            echo '<li id="comment-' . $comment['coid'] . '" class="comment-item">';
            echo '<div class="comment-item-header">';
            if(!empty($comment['url'])) {
                $hasLink = ' href="' . ensureAbsoluteUrl($comment['url']) . '" target="_blank" rel="nofollow"';
            } else {
                $hasLink = '';
            }
            echo '<a class="comment-author"' . $hasLink . '>' . $comment['author'] . '</a>';
            echo '<span class="separator post-comment flex-1" data-cid="' . $comment['cid'] . '" data-coid="' . $comment['coid'] . '" data-name="' . $comment['author'] . '" data-location="index">' . commentEmojiReplace($comment['text']) .'</span>';
            echo '</div>';
            if (!empty($comment['replies'])) {
                echo '<ul class="comment-replies">';
                renderReplies($comment['replies'], $comment['author']);
                echo '</ul>';
            }

            echo '</li>';
            $displayCount++;
        } else {
            break;
        }
    }
    if (!$showAll && $commentCount > $maxTopLevelComments) {
        echo '<li class="comment-item"><a class="more-comments underline" href="' . $link . '#comments">查看更多</a></li>';
    }
}

/**
 * 递归渲染回复评论
 *
 * @param array $replies 回复列表
 * @param string $parentAuthor 父评论的作者
 */
function renderReplies(array $replies, string $parentAuthor): void
{
    foreach ($replies as $reply) {
        echo '<li id="comment-' . $reply['coid'] . '" class="comment-item">';
        echo '<div class="comment-item-header">';
        if(!empty($reply['url'])) {
            $hasLink = ' href="' . ensureAbsoluteUrl($reply['url']) . '" target="_blank" rel="nofollow"';
        } else {
            $hasLink = '';
        }
        echo '<a class="comment-author"' . $hasLink . '>' . $reply['author'] . '</a> ';
        commentReply($reply['coid']);
        echo '<span class="post-comment flex-1" data-cid="' . $reply['cid'] . '" data-coid="' . $reply['coid'] . '" data-name="' . $reply['author'] . '" data-location="index">' . commentEmojiReplace($reply['text']) .'</span>';
        echo '</div>';
        if (!empty($reply['replies'])) {
            echo '<ul class="comment-replies">';
            renderReplies($reply['replies'], $reply['author']);
            echo '</ul>';
        }
        echo '</li>';
    }
}

/**
 * 获取最近的 n 条评论，同时排除 authorId 不为 0 的评论
 *
 * @param int $limit 要获取的评论数量
 * @return array 最近的 n 条评论
 */
function getLatestComments($limit = 5) {
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    // 构建查询，获取最近的 n 条评论，同时排除 authorId 不为 0 的评论
    $query = $db->select()
        ->from('table.comments')
        ->where('status = ?', 'approved')
        ->where('authorId = ?', 0)
        ->order('created', Typecho_Db::SORT_DESC)
        ->limit($limit);

    $result = $db->fetchAll($query);
    return $result;
}

function commentReply(int $coid)
{
    // 使用 Typecho 数据库实例
    $db = Typecho_Db::get();

    // 获取当前评论信息
    $comment = $db->fetchRow($db->select()->from('table.comments')->where('coid = ?', $coid));

    // 检查评论是否有父评论
    if ($comment && $comment['parent'] != 0) {
        // 获取父评论信息
        $parentComment = $db->fetchRow($db->select()->from('table.comments')->where('coid = ?', $comment['parent']));

        // 如果父评论存在，输出“回复某某某”
        if ($parentComment) {
            if(!empty($parentComment['url'])) {
                $hasLink = ' href="' . ensureAbsoluteUrl($parentComment['url']) . '" target="_blank" rel="nofollow"';
            } else {
                $hasLink = '';
            }
            $parentAuthor = htmlspecialchars($parentComment['author']); // 防止 XSS 注入
            echo '回复 ' . '<a class="comment-author"' . $hasLink . '>' . $parentAuthor . '</a><span class="separator"></span>';
        }
    }
}

function removeCommentPar($content)
{
    $content = preg_replace("/^<p>(.*)<\/p>$/", '$1', $content);
    return $content;
}

function commentEmojiReplace($comment_text): string {
    // 目录路径
    $directory = '/usr/themes/reborn/assets/emoji/';
    // 表情包类别
    $categories = array('wechat', 'xiaodianshi');
    $data_OwO = array();
    $db = \Typecho\Db::get();
    $siteUrlRow = $db->fetchRow($db->select('value')->from('table.options')->where('name = ?', 'siteUrl'));
    $siteUrl = $siteUrlRow['value'];
    foreach ($categories as $category) {
        // 获取表情包路径
        $path = __TYPECHO_ROOT_DIR__ . $directory . $category;
        // 扫描目录获取文件
        $files = scandir($path);
        foreach ($files as $file) {
            // 检查文件是否为 PNG 格式
            if (strpos($file, '.png') !== false) {
                // 获取表情名称
                $emoji_name = mb_substr($file, 0, -4); // 去掉 .png 扩展名
                // 构建替换数组
                $data_OwO['@(' . $emoji_name . ')'] = '<img src="' . $siteUrl . $directory . $category . '/' . $file . '" alt="' . $emoji_name . '" class="rb-emoji-item">';
            }
        }
    }
    return strtr($comment_text, $data_OwO);
}