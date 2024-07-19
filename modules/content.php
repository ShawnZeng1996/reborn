<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$db  = Typecho_Db::get();
$sql = $db->select()->from('table.comments')
    ->where('cid = ?', $this->cid)
    ->where('mail = ?', $this->remember('mail', true))
    ->limit(1);
//只有通过审核的评论才能看回复可见内容
$result  = $db->fetchAll($sql);
$content = $this->content;
//a链接增加_blank
//$content = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/sm', '<a href="$1" target="_blank">$2</a>', $content);
//todo
// 将 [ ] 替换为未选中的复选框
$content = preg_replace('/\[\s\]/', '<input type="checkbox" class="rb-checkbox" disabled />', $content);
// 将 [x] 替换为已选中的复选框
$content = preg_replace('/\[x\]/', '<input type="checkbox" class="rb-checkbox" checked disabled />', $content);
//回复可见
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

$pattern = '/<img(?![^>]*\bclass\b)([^>]*?)src="([^"]+)"([^>]*?)(alt="([^"]*)")?([^>]*)>/i';
$replacement = '<a href="$2" data-fancybox="gallery-' . $this->cid . '"><img src="$2" alt="$5" title="$5"></a>';
$content = preg_replace($pattern, $replacement, $this->content);


echo $content;


