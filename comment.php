<?php
// 包含Typecho的必要文件
require_once dirname(__FILE__, 4) . DIRECTORY_SEPARATOR . 'config.inc.php';
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
require_once __TYPECHO_ROOT_DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'Typecho' . DIRECTORY_SEPARATOR . 'Common.php';
require_once __TYPECHO_ROOT_DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'Widget' . DIRECTORY_SEPARATOR . 'Base.php';
require_once __TYPECHO_ROOT_DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'Widget' . DIRECTORY_SEPARATOR . 'Options.php';
require_once __TYPECHO_ROOT_DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'Typecho' . DIRECTORY_SEPARATOR . 'Request.php';
require_once __TYPECHO_ROOT_DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'Typecho' . DIRECTORY_SEPARATOR . 'Cookie.php';
require_once __TYPECHO_ROOT_DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'Typecho' . DIRECTORY_SEPARATOR . 'Plugin.php';
require_once __TYPECHO_ROOT_DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'Typecho' . DIRECTORY_SEPARATOR . 'Db.php';

header('Content-Type: application/json');

$response = array('success' => false, 'message' => '操作失败');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
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

        // 插入评论
        $comment = array(
            'cid' => $cid,
            'created' => time(),
            'author' => $author,
            'authorId' => 0,
            'ownerId' => $ownerId['authorId'],
            'mail' => $mail,
            'url' => $url,
            'ip' => $clientIp,
            'agent' => $userAgent,
            'text' => $text,
            'type' => 'comment',
            'status' => $commentsRequireModeration['value'] ? 'approved' : 'waiting',
            'parent' => $parent,
        );

        $insertId = $db->query($db->insert('table.comments')->rows($comment));

        if ($insertId) {
            $response['success'] = true;
            $response['message'] = '评论成功';
            $response['comment'] = array(
                'coid' => $insertId,
                'author' => $author,
                'text' => $text,
                'url' => $url,
                'status' => $commentsRequireModeration['value'] ? 'approved' : 'waiting',
            );
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

