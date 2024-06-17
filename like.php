<?php
// 包含Typecho的必要文件
require_once dirname(__FILE__, 4) . DIRECTORY_SEPARATOR . 'config.inc.php';
if (!defined('__TYPECHO_ROOT_DIR__'))
    exit;
require_once __TYPECHO_ROOT_DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'Typecho' . DIRECTORY_SEPARATOR . 'Cookie.php';
require_once __TYPECHO_ROOT_DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'Typecho' . DIRECTORY_SEPARATOR . 'Db.php';

header('Content-Type: application/json');
// error_log('like.php has been called'); // 添加一个日志记录，用于验证日志输出
$response = array('success' => false, 'likes' => 0, 'message' => '操作失败');

try {
    // 检查请求方法是否为 POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db = Typecho_Db::get();
        $siteUrl = $db->fetchRow($db->select('value')->from('table.options')->where('name = ?', 'siteUrl'));
        $cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        Typecho_Cookie::setPrefix($siteUrl['value']);
        if ($cid > 0 && in_array($action, ['like', 'unlike'])) {
            $prefix = $db->getPrefix();
            $likeRecording = json_decode(Typecho_Cookie::get('__typechoLikeRecording', '[]'), true);
            // 查询出点赞数量
            $likes = $db->fetchRow($db->select('likes')->from('table.contents')->where('cid = ?', $cid));
            if ($action === 'like') {
                if (!in_array($cid, $likeRecording)) {
                    // 执行点赞操作
                    $db->query($db->update($prefix . 'contents')->rows(array('likes' => $likes['likes'] + 1))->where('cid = ?', $cid));
                    $likeRecording[] = $cid;
                    Typecho_Cookie::set('__typechoLikeRecording', json_encode($likeRecording), time() + 365*24*3600);
                    $response['message'] = '点赞成功';
                } else {
                    $response['message'] = '已经点赞过';
                }
            } elseif ($action === 'unlike') {
                if (($key = array_search($cid, $likeRecording)) !== false) {
                    // 执行取消点赞操作
                    unset($likeRecording[$key]);
                    $db->query($db->update($prefix . 'contents')->rows(array('likes' => $likes['likes'] - 1))->where('cid = ?', $cid));
                    Typecho_Cookie::set('__typechoLikeRecording', json_encode(array_values($likeRecording)));
                    $response['message'] = '取消点赞成功';
                } else {
                    $response['message'] = '尚未点赞';
                }
            }
            // 获取最新点赞数
            $likes = $db->fetchRow($db->select('likes')->from('table.contents')->where('cid = ?', $cid));
            $response['success'] = true;
            $response['likes'] = $likes['likes'];
        } else {
            $response['message'] = '无效的文章 ID 或操作';
        }
    } else {
        $response['message'] = '无效的请求方法';
    }
} catch (Exception $e) {
    $response['message'] = '操作失败: ' . $e->getMessage();
}
echo json_encode($response);
exit;