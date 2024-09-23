<?php

/**
 * 增加指定文章的浏览次数。
 *
 * @param int $cid 文章的ID。
 * @return int 更新后的浏览次数。
 * @throws \Typecho\Db\Exception
 */
function addPostViewNum(int $cid): int {
    $db = \Typecho\Db::get();
    $prefix = $db->getPrefix();
    // 在数据库中直接将浏览次数加1
    $db->query($db->update('table.contents')
        ->expression('views', 'views + 1')
        ->where('cid = ?', $cid)
    );
    // 获取更新后的浏览次数
    $row = $db->fetchRow($db->select('views')
        ->from('table.contents')
        ->where('cid = ?', $cid)
    );
    // 返回最新的浏览次数
    return (int) $row['views'];
}

/**
 * @param $self
 * @return void
 * @throws \Typecho\Db\Exception
 */
function addPostView($self): void {
    $self->response->setStatus(200);
    $cid = $self->request->cid;
    /* sql注入校验 */
    if (!preg_match('/^\d+$/',  $cid)) {
        $self->response->throwJson(array(
            "code" => 0,
            "data" => "非法请求！已屏蔽！"
        ));
    }
    $self->response->throwJson(array(
        "code" => 1,
        "data" => array('views' => addPostViewNum($cid))
    ));
}

function postLike($self) {
    $self->response->setStatus(200);
    // 获取请求参数
    $cid = $self->request->cid;
    $action = $self->request->action;
    $cookie = json_decode($self->request->cookieObject, true);
    $location = $self->request->location;
    // 获取数据库实例
    $db = \Typecho\Db::get();
    // 检查内容是否存在
    $row = $db->fetchRow($db->select('likes')->from('table.contents')->where('cid = ?', $cid));
    if (empty($row)) {
        return $self->response->throwJson(array(
            "code" => 0,
            "message" => "Content not found."
        ));
    }
    $likes = (int)$row['likes'];
    // 检查 cookie 中是否包含作者和邮箱信息
    $author = $cookie['typecho_remember_author'] ?? null;
    $mail = $cookie['typecho_remember_mail'] ?? '';
    try {
        if ($action === 'like') {
            // 点赞操作
            $db->query($db->update('table.contents')->rows(array('likes' => $likes + 1))->where('cid = ?', $cid));
            $likes++;
            // 更新点赞列表
            if ($author) {
                $db->query($db->insert('table.post_like_list')
                    ->rows(array(
                        'cid' => $cid,
                        'name' => $author,
                        'mail' => $mail,
                        'url' => $cookie['typecho_remember_url'] ?? '' // URL 可为空
                    ))
                );
            }
        } elseif ($action === 'dislike') {
            // 取消点赞操作
            $db->query($db->update('table.contents')->rows(array('likes' => max(0, $likes - 1)))->where('cid = ?', $cid));
            $likes--;
            // 从点赞列表中移除
            if ($author) {
                $query = $db->delete('table.post_like_list')
                    ->where('cid = ?', $cid)
                    ->where('name = ?', $author);
                // 仅当 mail 不为空时才加入 mail 条件
                if (!empty($mail)) {
                    $query->where('mail = ?', $mail);
                }
                $db->query($query);
            }
        } else {
            // 无效的操作类型
            return $self->response->throwJson(array(
                "code" => 0,
                "message" => "Invalid action."
            ));
        }
    } catch (\Exception $e) {
        // 数据库操作失败
        return $self->response->throwJson(array(
            "code" => 0,
            "message" => "Database operation failed: " . $e->getMessage()
        ));
    }
    // 成功返回结果
    return $self->response->throwJson(array(
        "code" => 1,
        "likesTotalNum" => $likes,
        "likesListHtml" => getPostLikeHtml($cid, $location)
    ));
}

function commentLike($self) {
    $self->response->setStatus(200);
    // 获取请求参数
    $coid = $self->request->coid;
    $action = $self->request->action;
    // 获取数据库实例
    $db = \Typecho\Db::get();
    // 检查内容是否存在
    $row = $db->fetchRow($db->select('likes')->from('table.comments')->where('coid = ?', $coid));
    if (empty($row)) {
        return $self->response->throwJson(array(
            "code" => 0,
            "message" => "Content not found."
        ));
    }
    $likes = (int)$row['likes'];
    try {
        if ($action === 'like') {
            // 点赞操作
            $db->query($db->update('table.comments')->rows(array('likes' => $likes + 1))->where('coid = ?', $coid));
            $likes++;
        } elseif ($action === 'dislike') {
            // 取消点赞操作
            $db->query($db->update('table.comments')->rows(array('likes' => max(0, $likes - 1)))->where('coid = ?', $coid));
            $likes--;
        } else {
            // 无效的操作类型
            return $self->response->throwJson(array(
                "code" => 0,
                "message" => "Invalid action."
            ));
        }
    } catch (\Exception $e) {
        // 数据库操作失败
        return $self->response->throwJson(array(
            "code" => 0,
            "message" => "Database operation failed: " . $e->getMessage()
        ));
    }
    // 成功返回结果
    return $self->response->throwJson(array(
        "code" => 1,
        "likesTotalNum" => $likes
    ));
}

