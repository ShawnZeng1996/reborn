<?php
// 包含Typecho的必要文件
require_once dirname(__FILE__, 4) . DIRECTORY_SEPARATOR . 'config.inc.php';
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
require_once __TYPECHO_ROOT_DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'Typecho' . DIRECTORY_SEPARATOR . 'Cookie.php';
require_once __TYPECHO_ROOT_DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'Typecho' . DIRECTORY_SEPARATOR . 'Db.php';
require_once __TYPECHO_ROOT_DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'Utils' . DIRECTORY_SEPARATOR . 'Helper.php';

header('Content-Type: application/json');

$response = array('success' => false, 'message' => '操作失败');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
        $parent = isset($_POST['parent']) ? intval($_POST['parent']) : 0;
        $author = isset($_POST['author']) ? trim($_POST['author']) : '';
        $mail = isset($_POST['mail']) ? trim($_POST['mail']) : '';
        $url = isset($_POST['url']) ? trim($_POST['url']) : '';
        $text = isset($_POST['text']) ? trim($_POST['text']) : '';

        if ($cid <= 0 || empty($author) || empty($mail) || empty($text)) {
            $response['message'] = '缺少必要的参数';
            echo json_encode($response);
            exit;
        }

        // 获取数据库连接
        $db = Typecho_Db::get();
        $ownerId = $db->fetchRow($db->select('authorId')->from('table.contents')->where('cid = ?', $cid));
        $commentsRequireModeration = $db->fetchRow($db->select('value')->from('table.options')->where('name = ?', 'commentsRequireModeration'));
        // 获取客户端 IP 地址
        $clientIp = $_SERVER['REMOTE_ADDR'];
        // 获取客户端 User-Agent
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        if ( $ownerId['authorId'] === $uid ) {
            $status = 'approved';
        } else {
            $status = $commentsRequireModeration['value'] ? 'approved' : 'waiting';
        }

        // 插入评论
        $comment = array(
            'cid' => $cid,
            'created' => time(),
            'author' => $author,
            'authorId' => $uid,
            'ownerId' => $ownerId['authorId'],
            'mail' => $mail,
            'url' => $url,
            'ip' => $clientIp,
            'agent' => $userAgent,
            'text' => $text,
            'type' => 'comment',
            'status' => $status,
            'parent' => $parent,
        );

        $insertId = $db->query($db->insert('table.comments')->rows($comment));
        $articleName = $db->fetchRow($db->select('title')->from('table.contents')->where('cid = ?', $cid));
        if ($insertId) {
            if ($status === 'approved') {
                // 更新评论数
                $db->query($db->update('table.contents')->expression('commentsNum', 'commentsNum + 1')->where('cid = ?', $cid));
            }
            $response['success'] = true;
            $response['message'] = '评论成功';
            $response['views'] = getPostView($cid);
            $response['comment'] = array(
                'coid' => $insertId,
                'author' => $author,
                'text' => $text,
                'url' => $url,
                'status' => $status,
            );
            // Bark评论消息通知
            $barkUrl = \Utils\Helper::options()->barkUrl;
            if ($barkUrl) {
                $message = '您的博文《' . $articleName['title'] . '》有新的评论/' . $author . '：' . $text;
                file_get_contents($barkUrl . $message);
            }
        } else {
            $response['message'] = '评论失败，请稍后再试';
        }
    } else {
        $response['message'] = '无效的请求方法';
    }
} catch (Exception $e) {
    $response['message'] = '操作失败: ' . $e->getMessage();
}

echo json_encode($response);
exit;

function getPostView($cid)
{
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    $siteUrl = $db->fetchRow($db->select('value')->from('table.options')->where('name = ?', 'siteUrl'));
    Typecho_Cookie::setPrefix($siteUrl['value']);
    // 获取当前文章的浏览量
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    $views = $row ? (int) $row['views'] : 0;

    // 增加浏览量
    $cookieViews = Typecho_Cookie::get('__post_views');
    $viewedPosts = $cookieViews ? explode(',', $cookieViews) : [];

    if (!in_array($cid, $viewedPosts)) {
        $db->query($db->update('table.contents')->rows(array('views' => $views + 1))->where('cid = ?', $cid));
        $viewedPosts[] = $cid;
        Typecho_Cookie::set('__post_views', implode(',', $viewedPosts)); // 记录查看cookie
        $views++; // 更新本次显示的浏览量
    }
    // 格式化浏览量
    if ($views >= 10000) {
        $formattedViews = number_format($views / 10000, 1) . '万';
    } else {
        $formattedViews = $views;
    }
    return $formattedViews;
}


