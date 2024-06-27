<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

define('THEME_NAME', 'reborn');

define('THEME_VERSION', '1.0.0');

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
    $sidebarBlock = new Typecho\Widget\Helper\Form\Element\Checkbox(
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
    $sidebarAd = new Typecho\Widget\Helper\Form\Element\Textarea(
        'sidebarAd',
        NULL,
        NULL,
        _t('侧边栏广告'),
        _t('侧边栏广告')
    );
    $form->addInput($sidebarAd);
}

function themeInit($self) {
    
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

/**
 * 获取点赞数量和记录信息
 *
 * @param int $cid 文章的 cid
 * @return array 点赞数量和记录信息
 */
function getLikeNumByCid($cid)
{
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    try {
        // 判断点赞数量字段是否存在
        if (!array_key_exists('likes', $db->fetchRow($db->select()->from('table.contents')))) {
            // 在文章表中创建一个字段用来存储点赞数量
            $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `likes` INT(10) NOT NULL DEFAULT 0;');
        }

        // 查询出点赞数量
        $likes = $db->fetchRow($db->select('likes')->from('table.contents')->where('cid = ?', $cid));

        // 获取记录点赞的 Cookie
        $likeRecording = Typecho_Cookie::get('typechoLikeRecording', '[]');
        $likeRecordingDecode = json_decode(urldecode($likeRecording), true);

        // 返回点赞数量和记录信息
        return [
            'likes' => $likes['likes'],
            'recording' => in_array($cid, $likeRecordingDecode)
        ];
    } catch (Exception $e) {
        // 记录错误信息
        error_log('Database Query Error: ' . $e->getMessage());
        return [
            'likes' => 0,
            'recording' => false
        ];
    }
}

/**
 * 是否点过赞
 *
 * @param int $cid 文章的 cid
 * @return bool 是否点过赞
 */
function isLikeByCid($cid)
{
    try {
        // 获取记录点赞的 Cookie
        $likeRecording = Typecho_Cookie::get('__typecho_post_like');

        if (empty($likeRecording)) {
            // 如果不存在就创建一个新的 Cookie
            $likeRecording = '[]';

            Typecho\Cookie::set('__typecho_post_like', $likeRecording, time() + 365 * 24 * 3600); // 设置过期时间为一年
            error_log('Cookie "__typecho_post_like" created with value: ' . $likeRecording); // 调试输出
        }
        // URL 解码并将 JSON 字符串转换为 PHP 数组
        $likeRecordingDecode = json_decode(urldecode($likeRecording), true);
        // 检查是否解码失败或结果不是数组
        if (!is_array($likeRecordingDecode)) {
            $likeRecordingDecode = [];
        }
        // 判断文章是否点赞过
        $isLiked = in_array($cid, $likeRecordingDecode);

        return $isLiked;
    } catch (Exception $e) {
        // 记录错误信息
        error_log('Cookie Parsing Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 获取指定文章的评论，包括子评论
 *
 * @param int $cid 文章的 cid
 * @param int $parent 父评论的 coid，默认 0 表示顶级评论
 * @param int|null $limit 限制顶级评论数量，默认无限制
 * @return array 评论列表
 */
function getCommentsWithReplies($cid, $parent = 0, $limit = null)
{
    $db = Typecho_Db::get();
    $options = Typecho_Widget::widget('Widget_Options');
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
 * 判断文章是否有评论
 *
 * @param int $cid 文章的 cid
 * @return bool 是否有评论
 */
function hasComments($cid)
{
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    // 查询评论数量
    $comments = $db->fetchRow($db->select('COUNT(*) AS count')->from('table.comments')->where('cid = ?', $cid));
    return $comments['count'] ;
}

/**
 * 递归渲染评论
 *
 * @param array $comments 评论列表
 * @param string $link 文章的链接
 * @param int $maxTopLevelComments 显示的最大顶级评论数量
 */
function renderComments($comments, $link, $maxTopLevelComments = 5)
{
    $commentCount = count($comments);
    $displayCount = 0;
    $showAll = $maxTopLevelComments === 0;
    foreach ($comments as $comment) {
        if ($showAll || $displayCount < $maxTopLevelComments) {
            echo '<li id="comment-coid-' . $comment['coid'] . '" class="comment-item">';
            echo '<div class="comment-item-header">';
            echo '<a href="' . htmlspecialchars($comment['url']) . '" target="_blank" class="comment-author" rel="nofollow">' . htmlspecialchars($comment['author']) . '</a>';
            echo '<span class="separator post-comment flex-1" data-cid="' . $comment['cid'] . '" data-coid="' . $comment['coid'] . '" data-name="' . $comment['author'] . '">' . commentEmojiReplace(htmlspecialchars($comment['text'])) .'</span>';
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
        echo '<li class="comment-item"><a class="more-comments" href="' . $link . '#comments">查看更多</a></li>';
    }
}

function renderPostComments($comments, $parentAuthor = '') {
    foreach ($comments as $comment) {
        echo '<li id="comment-coid-' . $comment['coid'] . '" class="comment-item">';
        echo '<div class="comment-item-header flex">';
        echo '<a class="comment-author-avatar" rel="nofollow" target="_blank" href="' . $comment['url'] . '" rel="nofollow"><img src="' . getGravatarUrl($comment['mail'], 40) . '" alt="' . htmlspecialchars($comment['author']) . '"></a>';
        echo '<div class="flex flex-1 comment-body">';
        echo '<div class="flex-1">';
        echo '<a class="comment-author" rel="nofollow" target="_blank" href="' . $comment['url'] . '">' . htmlspecialchars($comment['author']) . '</a>';
        if (!empty($parentAuthor)) {
            echo '回复<span class="comment-author m-l-10">' . htmlspecialchars($parentAuthor) . '</span>';
        }
        echo '<time class="comment-time" datetime="' . $comment['created'] . '">' . timeAgo($comment['created']) . '</time>';
        echo '</div>';
        echo '<a class="write-comment" data-cid="' . $comment['cid'] . '" data-coid="' . $comment['coid'] . '" data-name="' . $comment['author'] . '">回复</a>';
        echo '<div class="comment-content write-comment" data-cid="' . $comment['cid'] . '" data-coid="' . $comment['coid'] . '" data-name="' . $comment['author'] . '">' . commentEmojiReplace(htmlspecialchars($comment['text'])) . '</div>';
        echo '</div>';
        echo '</div>';
        if (!empty($comment['replies'])) {
            echo '<ul class="comment-reply">';
            renderPostComments($comment['replies'], $comment['author']);
            echo '</ul>';
        }
        echo '</li>';
    }
}

/**
 * 递归渲染回复评论
 *
 * @param array $replies 回复列表
 * @param string $parentAuthor 父评论的作者
 */
function renderReplies($replies, $parentAuthor)
{
    foreach ($replies as $reply) {
        echo '<li id="comment-coid-' . $reply['coid'] . '" class="comment-item">';
        echo '<div class="comment-item-header">';
        echo '<a href="' . htmlspecialchars($reply['url']) . '" target="_blank" class="comment-author" rel="nofollow">' . htmlspecialchars($reply['author']) . '</a> 回复 <span class="comment-author">' . htmlspecialchars($parentAuthor) . '</span>';
        echo '<span class="separator post-comment flex-1" data-cid="' . $reply['cid'] . '" data-coid="' . $reply['coid'] . '" data-name="' . $reply['author'] . '">' . commentEmojiReplace(htmlspecialchars($reply['text'])) .'</span>';
        echo '</div>';
        if (!empty($reply['replies'])) {
            echo '<ul class="comment-replies">';
            renderReplies($reply['replies'], $reply['author']);
            echo '</ul>';
        }
        echo '</li>';
    }
}

function getPostView($archive)
{
    $cid = $archive->cid;
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    // 检查是否存在 views 字段
    if (!array_key_exists('views', $db->fetchRow($db->select()->from('table.contents')->page(1, 1)))) {
        $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `views` INT(10) DEFAULT 0;');
        echo 0;
        return;
    }
    // 获取当前文章的浏览量
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    $views = $row ? (int) $row['views'] : 0;

    // 如果是单篇文章页面，则增加浏览量
    if ($archive->is('single')) {
        $cookieViews = Typecho_Cookie::get('__post_views');
        $viewedPosts = $cookieViews ? explode(',', $cookieViews) : [];

        if (!in_array($cid, $viewedPosts)) {
            $db->query($db->update('table.contents')->rows(array('views' => $views + 1))->where('cid = ?', $cid));
            $viewedPosts[] = $cid;
            Typecho_Cookie::set('__post_views', implode(',', $viewedPosts)); // 记录查看cookie
            $views++; // 更新本次显示的浏览量
        }
    }
    // 格式化浏览量
    if ($views >= 10000) {
        $formattedViews = number_format($views / 10000, 1) . '万';
    } else {
        $formattedViews = $views;
    }
    echo $formattedViews;
}

function commentEmojiReplace($comment_text): string {
    // 目录路径
    $directory = '/usr/themes/reborn/emoji/';
    // 表情包类别
    $categories = array('alu', 'paopao', 'xiaodianshi', 'koukou');
    $data_OwO = array();
    $db = Typecho_Db::get();
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





