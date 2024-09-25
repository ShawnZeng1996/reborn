<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$db  = \Typecho\Db::get();
$sql = $db->select()->from('table.comments')
    ->where('cid = ?', $this->cid)
    ->where('mail = ?', $this->remember('mail', true))
    ->limit(1);
// 只有通过审核的评论才能看回复可见内容
$result  = $db->fetchAll($sql);
$content = $this->content;

// a链接增加_blank
//$content = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/sm', '<a href="$1" target="_blank">$2</a>', $content);

// 将 [ ] 替换为未选中的复选框
$content = preg_replace('/\[\s\]/', '<input type="checkbox" class="rb-checkbox" disabled />', $content);
// 将 [x] 替换为已选中的复选框
$content = preg_replace('/\[x\]/', '<input type="checkbox" class="rb-checkbox" checked disabled />', $content);

// 回复可见
if ($this->user->hasLogin() || $result) {
    $content = preg_replace("/\[hide\](<br>)?(.*?)\[\/hide\]/sm", '$2', $content);
} else {
    $content = preg_replace("/\[hide\](.*?)\[\/hide\]/sm", '<a href="#comments" class="comment-visible"><span class="underline">回复</span>&nbsp;阅读全文</a>', $content);
}

// 给没有 class 属性的 h1 到 h5 标签添加自增的 id
$idCounter = 1;
$content = preg_replace_callback('/<h([1-5])(?![^>]*class=)([^>]*)>(.*?)<\/h\1>/', function($matches) use (&$idCounter) {
    return '<h' . $matches[1] . ' id="header-' . $idCounter++ . '"' . $matches[2] . '>' . $matches[3] . '</h' . $matches[1] . '>';
}, $content);

// 处理 [gallery][/gallery] 短代码，去掉 <br> 标签
$content = preg_replace_callback('/\[gallery\](.*?)\[\/gallery\]/s', function($matches) {
    $galleryContent = preg_replace('/<br\s*\/?>/i', '', $matches[1]);
    preg_match_all('/<img.*?src="(.*?)".*?>/i', $galleryContent, $imageMatches);
    $images = array_filter($imageMatches[0]); // 获取完整的img标签
    if ( $this->is('post') || $this->is('page') ) {
        return generateGalleryHtml($images, 0, 1);
    } else {
        return generateGalleryHtml($images, $this->cid,0 );
    }
}, $content);

// 处理没有 class 属性的 img 标签
$pattern = '/<img(?![^>]*\bclass\b)([^>]*)>/i';
$content = preg_replace_callback($pattern, function($matches) {
    // 从 src 属性中提取链接
    preg_match('/src="([^"]+)"/i', $matches[0], $srcMatches);
    $src = $srcMatches[1];
    // 包裹 img 标签
    return '<a href="' . $src . '" data-fancybox="gallery-' . $this->cid . '">' . $matches[0] . '</a>';
}, $content);

// 匹配farea的正则表达式
$farea_pattern = '/\[farea\](.*?)\[\/farea\]/s';

// 处理farea短代码
$content = preg_replace_callback($farea_pattern, function($matches) {
    // 获取farea内部内容并移除<p>和<br>标签
    $farea_content = $matches[1];
    $farea_content = str_replace(['<p>', '</p>', '<br>', '<br />'], '', $farea_content);

    // 处理farea内的flink短代码
    $flink_pattern = '/\[flink\s+href="([^"]+)"\s+name="([^"]+)"\s+img="([^"]+)"\s+description="([^"]+)"\s+comment="([^"]*)"\]/';
    $farea_content = preg_replace_callback($flink_pattern, function($flink_matches) {
        $href = $flink_matches[1];
        $name = $flink_matches[2];
        $img = $flink_matches[3];
        $description = $flink_matches[4];
        $comment = $flink_matches[5];

        // 构建HTML输出
        $html = '
        <div class="friend-link">
            <div class="flex">
                <img class="friend-link-img" alt="' . htmlspecialchars($name) . '" title="' . htmlspecialchars($name) . '" src="' . htmlspecialchars($img) . '" />
                <div class="flex-1">
                    <div class="friend-link-name">' . htmlspecialchars($name) . '</div>
                    <div class="friend-link-description">' . htmlspecialchars($description) . '</div>
                </div>
                <div class="friend-link-more"><i class="reborn rb-down"></i></div>
            </div>';

        // 如果 comment 不为空，则显示对应的 div
        if (!empty($comment)) {
            $html .= '
            <div class="friend-link-comment">' . htmlspecialchars($comment) . '</div>';
        }

        $html .= '
        </div>';

        return $html;
    }, $farea_content);

    // 返回处理后的farea内容
    return '<div class="friend-area flex">' . $farea_content . '</div>';
}, $content);

// 输出最终结果
echo $content;






