<?php

use Widget\Archive;


/**
 * 获取指定文章的类型
 *
 * @param int $cid 文章的唯一标识符
 * @return string 返回文章的类型。如果未指定类型或在查询过程中出现错误，则返回 'post'
 * @throws \Typecho\Db\Exception
 */
function getPostType(int $cid): string
{
    $db = \Typecho\Db::get();
    $prefix = $db->getPrefix();
    $postType = $db->fetchRow(
        $db->select()->from('table.fields')
            ->where('cid = ?', $cid)
            ->where('name = ?', 'postType')
    );
    if (!empty($postType)) {
        return $postType["str_value"];
    } else {
        return 'post';
    }
}

/**
 * 获取指定文章的略缩图
 *
 * @param int $cid 文章的唯一标识符
 * @return string 返回文章的略缩图地址
 * @throws \Typecho\Db\Exception
 */
function getPostThumbnail(int $cid): string{
    $db = \Typecho\Db::get();
    $thumbnail = $db->fetchRow(
        $db->select()->from('table.fields')
            ->where('cid = ?', $cid)
            ->where('name = ?', 'thumbnail')
    );

    if (!empty($thumbnail["str_value"])) {
        return $thumbnail["str_value"];
    } else {
        return \Utils\Helper::options()->themeUrl . '/assets/img/post.webp';
    }
}

function getPostView($archive): void {
    $cid = $archive->cid;
    $db = \Typecho\Db::get();
    $prefix = $db->getPrefix();
    // 获取当前文章的浏览量
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    $views = $row ? (int) $row['views'] : 0;
    // 如果是单篇文章页面，则增加浏览量
    if ($archive->is('single')) {
        $cookieViews = \Typecho\Cookie::get('__post_views');
        $viewedPosts = $cookieViews ? explode(',', $cookieViews) : [];
        if (!in_array($cid, $viewedPosts)) {
            $db->query($db->update('table.contents')->rows(array('views' => $views + 1))->where('cid = ?', $cid));
            $viewedPosts[] = $cid;
            \Typecho\Cookie::set('__post_views', implode(',', $viewedPosts)); // 记录查看cookie
            $views++; // 更新本次显示的浏览量
        }
    }
    // 格式化浏览量
    if ($views >= 10000) {
        $formattedViews = number_format($views / 10000, 1) . 'w';
    } elseif ($views >= 1000){
        $formattedViews = number_format($views / 1000, 1) . 'k';
    } else {
        $formattedViews = $views;
    }
    echo $formattedViews;
}


function getPostViewNum($cid){
    $db = \Typecho\Db::get();
    $prefix = $db->getPrefix();
    // 获取当前文章的浏览量
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    $views = $row ? (int) $row['views'] : 0;
    // 格式化浏览量
    if ($views >= 10000) {
        $formattedViews = number_format($views / 10000, 1) . 'w';
    } elseif ($views >= 1000){
        $formattedViews = number_format($views / 1000, 1) . 'k';
    } else {
        $formattedViews = $views;
    }
    return $formattedViews;
}

/**
 * 获取点赞数量
 *
 * @param int $cid 文章的 cid
 * @return int 点赞数量
 * @throws \Typecho\Db\Exception
 */
function getPostLikeNum(int $cid): int {
    $db = \Typecho\Db::get();
    $prefix = $db->getPrefix();
    try {
        // 查询出点赞数量
        $likes = $db->fetchRow($db->select('likes')->from('table.contents')->where('cid = ?', $cid));
        // 返回点赞数量和记录信息
        return $likes['likes'];
    } catch (Exception $e) {
        // 记录错误信息
        error_log('Database Query Error: ' . $e->getMessage());
        return 0;
    }
}

function getPostLink($cid): string
{
    $db = \Typecho\Db::get();
    $article = $db->fetchRow($db->select()->from('table.contents')->where('cid = ?', $cid));
    $articleSlug = $article['slug'];
    $articleTime = $article['created'];
    $articleType = $article['type'];
    $articleYear = date('Y', $articleTime);
    $articleMonth = date('m', $articleTime);
    $articleDay = date('d', $articleTime);
    // 获取文章分类
    $category = $db->fetchRow(
        $db->select()->from('table.metas')
            ->join('table.relationships', 'table.metas.mid = table.relationships.mid', Typecho_Db::LEFT_JOIN)
            ->where('table.relationships.cid = ?', $cid)
            ->where('table.metas.type = ?', 'category')
    );
    return \Typecho\Router::url($articleType, array(
        'cid' => $cid,
        'slug' => $articleSlug,
        'category' => $category,
        'year' => $articleYear,
        'month' => $articleMonth,
        'day' => $articleDay
    ), \Utils\Helper::options()->index);
}

function getPostLikeList($cid, $limit = 10): array {
    $db = \Typecho\Db::get();
    // 确保 limit 是正整数
    $limit = max(1, (int)$limit);
    // 获取点赞总数
    $likesTotalNum = getPostLikeNum($cid);
    try {
        // 查询点赞列表，带上 limit
        $likesList = $db->fetchAll($db->select()
            ->from('table.post_like_list')
            ->where('cid = ?', $cid)
            ->order('id', \Typecho\Db::SORT_ASC)
            ->limit($limit)
        );
        //error_log(print_r($likesList,true));
        // 查询点赞人数
        $likesCountRow = $db->fetchRow($db->select(array('COUNT(id)'=>'count'))
            ->from('table.post_like_list')
            ->where('cid = ?', $cid)
        );
        // 返回格式化的数据
        return array(
            'likesCount' => (int) $likesCountRow['count'], // 使用数组下标访问
            'likesTotalNum' => $likesTotalNum,
            'likesList' => $likesList
        );
    } catch (Exception $e) {
        // 处理异常
        return array(
            'likesCount' => 0,
            'likesTotalNum' => $likesTotalNum,
            'likesList' => array()
        );
    }
}

function getPostLikeHtml(int $cid, string $location = 'index'):string {
    // 获取点赞数据
    $likes = getPostLikeList($cid, 99999); // 获取所有点赞者
    $likesCount = $likes["likesCount"]; // 实名点赞人数
    $likesTotalNum = $likes["likesTotalNum"]; // 点赞总人数
    $likesList = $likes["likesList"]; // 实名点赞清单
    // 初始化HTML
    $displayHtml = '';
    // 判断不同的显示位置
    if ($location == 'index') {
        // 首页显示：最多显示10个人名
        $displayHtml = '<span class="reborn rb-heart-o"></span>&nbsp;<span class="like-area">';
        if ($likesCount == 0) {
            // 如果没有实名点赞者，直接显示总人数
            $displayHtml .= $likesTotalNum . '人';
        } else {
            // 提取最多10个实名点赞者的名字
            $names = array_slice(array_map(function($likePeople) {
                if ($likePeople['url']) {
                    return '<a class="like-people" target="_blank" rel="nofollow" href="' . ensureAbsoluteUrl(htmlspecialchars($likePeople['url'])) . '">' . htmlspecialchars($likePeople['name']) . '</a>';
                } else {
                    return '<span class="like-people">' .htmlspecialchars($likePeople['name']) . '</span>';
                }
            }, $likesList), 0, 10);
            $displayNames = implode('、', $names);
            $displayHtml .= $displayNames;
            // 如果点赞人数多于10个，显示"等"和总人数
            if ($likesTotalNum > 10 || $likesTotalNum > $likesCount) {
                $displayHtml .= ' 等' . $likesTotalNum . '人';
            }
        }
        $displayHtml .= '</span>';
    } else if ($location == 'shuoshuo') {
        // 说说页面显示：显示所有人的头像
        $displayHtml = '<div class="shuoshuo-like"><span class="reborn rb-heart-o"></span></div><span class="like-area">';
        if ($likesCount == 0) {
            // 没有实名点赞者，直接显示点赞总人数
            $displayHtml .= '<span class="like-num">' . $likesTotalNum . '人</span>';
        } else {
            // 获取所有点赞者的头像
            $avatars = array_map(function($likePeople) {
                $peopleMail = $likePeople['mail']? : '';
                $gravatarUrl = getGravatarUrl($peopleMail);
                if ($likePeople['url']) {
                    return '<a class="like-people" target="_blank" rel="nofollow" title="' . htmlspecialchars($likePeople['name']) . '" href="'.ensureAbsoluteUrl(htmlspecialchars($likePeople['url'])).'">' . '<img src="' . htmlspecialchars($gravatarUrl) . '" alt="' . htmlspecialchars($likePeople['name']) . '" class="like-avatar" />' . '</a>';
                }
                return '<img src="' . htmlspecialchars($gravatarUrl) . '" title="' . htmlspecialchars($likePeople['name']) . '" alt="' . htmlspecialchars($likePeople['name']) . '" class="like-avatar" />';
            }, $likesList);
            // 拼接头像HTML
            $displayHtml .= implode(' ', $avatars);
            if ($likesTotalNum > $likesCount) {
                $displayHtml .= '<span class="like-num">等' . $likesTotalNum . '人</span>';
            }
        }
        $displayHtml .= '</span>';
    } else if ($location == 'post') {
        // 文章页面显示：显示所有人的头像
        $displayHtml = '<div class="post-like-num">' . $likesTotalNum . '人喜欢' . '</div>';
        if ($likesCount !=0 ) {
            $displayHtml .= '<div class="like-people-list">';
            // 获取所有点赞者的头像
            $avatars = array_map(function($likePeople) {
                $peopleMail = $likePeople['mail']? : '';
                $gravatarUrl = getGravatarUrl($peopleMail);
                if ($likePeople['url']) {
                    return '<a class="like-people" target="_blank" rel="nofollow" title="' . htmlspecialchars($likePeople['name']) . '" href="'.ensureAbsoluteUrl(htmlspecialchars($likePeople['url'])).'">' . '<img src="' . htmlspecialchars($gravatarUrl) . '" alt="' . htmlspecialchars($likePeople['name']) . '" class="like-avatar" />' . '</a>';
                }
                return '<img src="' . htmlspecialchars($gravatarUrl) . '" title="' . htmlspecialchars($likePeople['name']) . '" alt="' . htmlspecialchars($likePeople['name']) . '" class="like-avatar" />';
            }, $likesList);
            // 拼接头像HTML
            $displayHtml .= implode(' ', $avatars);
            $displayHtml .= '</div>';
        }
    }
    return $displayHtml;
}

/**
 * 获取最新的 n 篇非 `shuoshuo` 类型的文章
 *
 * @param int $limit 要获取的文章数量
 * @return array 最新的 n 篇非 `shuoshuo` 类型的文章
 * @throws \Typecho\Db\Exception
 */
function getLatestPosts(int $limit = 5): array
{
    $db = \Typecho\Db::get();
    $prefix = $db->getPrefix();
    // 构建查询，获取最新的 n 篇非 `shuoshuo` 类型的已发布文章
    $query = $db->select()
        ->from('table.contents')
        ->join('table.fields', 'table.contents.cid = table.fields.cid', \Typecho\Db::LEFT_JOIN)
        ->where('table.contents.type = ?', 'post')
        ->where('table.contents.status = ?', 'publish')
        ->where('table.fields.name = ?', 'postType')
        ->where('table.fields.str_value != ?', 'shuoshuo')
        ->order('table.contents.created', \Typecho\Db::SORT_DESC)
        ->limit($limit);
    return $db->fetchAll($query);
}

// 生成图片 HTML
function generateGalleryHtml($images, $cid = 0, $showAll = 0): string {
    $imageCount = count($images);
    error_log($imageCount);
    $imageHtml = '<div class="gallery-images">';
    $imagesProcessed = 0;
    if ($imageCount == 4) {
        $rows = array_chunk($images, 2);
        foreach ($rows as $row) {
            $imageHtml .= '<div class="gallery-row-2">';
            foreach ($row as $index => $image) {
                $imageHtml .= generateGalleryItem($image);
            }
            $imageHtml .= '</div>';
        }
    } else {
        $rows = array_chunk($images, 3); // 将图片分成每行3张
        foreach ($rows as $row) {
            $imageHtml .= '<div class="gallery-row">';
            foreach ($row as $index => $image) {
                if ($showAll == 0 && $imagesProcessed == 8 && $imageCount >= 9) {
                    $remainingCount = $imageCount - 9;
                    $imageHtml .= generateGalleryItem($image, $cid , $remainingCount);
                    break 2;
                }
                $imageHtml .= generateGalleryItem($image);
                $imagesProcessed++;
            }
            $imageHtml .= '</div>';
            if ($imageCount <= 9) {
                $imageCount -= 3;
            }
        }
    }
    $imageHtml .= '</div>';
    return $imageHtml;
}

// 生成单个图片项
function generateGalleryItem($image, $cid = 0, $remainingCount = 0): string {
    $html = '<div class="gallery-image-item">';
    if ($remainingCount > 0) {
        $html .= $image . '<a class="overlay" href="'.getPostLink($cid).'">+' . $remainingCount . '</a>';
    } else {
        $html .= $image;
    }
    $html .= '</div>';

    return $html;
}

function getAuthorPostStats($authorId) {
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    // 构建查询，统计特定作者的文章数量、点赞数和浏览量
    $query = $db->select(array('COUNT(*)' => 'numPosts', 'SUM(table.contents.likes)' => 'totalLikes', 'SUM(table.contents.views)' => 'totalViews'))
        ->from('table.contents')
        ->join('table.fields', 'table.contents.cid = table.fields.cid', Typecho_Db::LEFT_JOIN)
        ->where('table.contents.authorId = ?', $authorId)
        ->where('table.contents.type = ?', 'post')
        ->where('table.contents.status = ?', 'publish')
        ->where('table.fields.name = ?', 'postType')
        ->where('table.fields.str_value != ?', 'shuoshuo');

    $result = $db->fetchObject($query);
    return $result ? array('numPosts' => $result->numPosts, 'totalLikes' => $result->totalLikes, 'totalViews' => $result->totalViews) : array('numPosts' => 0, 'totalLikes' => 0, 'totalViews' => 0);
}

// 生成文章目录树
function generateToc($content): string {
    $idCounter = 1;
    $matches = array();
    preg_match_all('/<h([1-5])(?![^>]*class=)([^>]*)>(.*?)<\/h\1>/', $content, $matches, PREG_SET_ORDER);
    if (!$matches) {
        return '暂无目录';
    }
    $toc = '<ul class="ul-toc">';
    $currentLevel = 0;
    foreach ($matches as $match) {
        $level = (int)$match[1];
        $attributes = $match[2];
        $title = strip_tags($match[3]);
        $anchor = 'header-' . $idCounter++;
        // 生成新的标题标签并添加 id
        $content = str_replace($match[0], '<h' . $level . ' id="' . $anchor . '"' . $attributes . '>' . $match[3] . '</h' . $level . '>', $content);
        // 调整目录层级
        if ($currentLevel == 0) {
            $currentLevel = $level;
        }
        while ($currentLevel < $level) {
            $toc .= '<ul>';
            $currentLevel++;
        }
        while ($currentLevel > $level) {
            $toc .= '</ul></li>';
            $currentLevel--;
        }
        $toc .= '<li><a href="#' . $anchor . '" class="toc-link">' . $title . '</a>';
        // 添加闭合标签
        $toc .= '</li>';
    }
    // 关闭所有未闭合的 ul 标签
    while ($currentLevel > 0) {
        $toc .= '</ul>';
        $currentLevel--;
    }
    $toc .= '</ul>';
    return $toc;
}