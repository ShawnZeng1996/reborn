(function($) {
    const App = {
        // 点击展开点赞/评论
        postMetaExpand: function () {
            $('.post-more').on('click', function () {
                var $postAction = $(this).siblings('.post-action-container').find('.post-action');
                if ($postAction.hasClass('show')) {
                    $postAction.removeClass('show');
                } else {
                    $('.post-action').removeClass('show'); // 关闭所有其他打开的post-action
                    $postAction.addClass('show');
                }
            });
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.post-meta-2').length) {
                    $('.post-action').removeClass('show');
                }
            });
        },
        // 内容点赞
        postLike: function () {
            $('.post-zan').on('click', function (e) {
                e.preventDefault();
                const $this = $(this);
                const cid = $this.data('cid');
                const isLiked = $this.find('.rb-like').length > 0;
                $.ajax({
                    url: themeUrl + '/like.php',
                    type: 'POST',
                    data: {
                        cid: cid,
                        action: isLiked ? 'unlike' : 'like'
                    },
                    success: function (data) {
                        console.log('AJAX success: ', data); // 输出服务器返回的数据
                        if (data.success) {
                            if (isLiked) {//<span class="reborn rb-like-o"></span>&nbsp;<span class="zan-num"><?php echo $likes; ?></span>
                                $this.html('<span class="reborn rb-like-o"></span>&nbsp;<span class="zan-num">'+data.likes+'</span>');
                            } else {
                                $this.html('<span class="reborn rb-like"></span>&nbsp;<span class="zan-num">'+data.likes+'</span>');
                            }
                            $('#post-view-cid-' + cid).text(data.views);
                        } else {
                            alert('操作失败，请稍后再试。');
                            console.error('操作失败: ', data.message); // 输出错误信息
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('操作失败，请稍后再试。');
                        console.error('AJAX error: ' + textStatus, errorThrown); // 输出错误信息
                    }
                });
            });
            $('.post-like').on('click', function (e) {
                e.preventDefault();
                const $this = $(this);
                const cid = $this.data('cid');
                const isLiked = $this.find('.rb-heart').length > 0;
                $('.post-action').removeClass('show');
                $.ajax({
                    url: themeUrl + '/like.php',
                    type: 'POST',
                    data: {
                        cid: cid,
                        action: isLiked ? 'unlike' : 'like'
                    },
                    success: function (data) {
                        console.log('AJAX success: ', data); // 输出服务器返回的数据
                        if (data.success) {
                            if (isLiked) {
                                $this.html('<span class="reborn rb-heart-o"></span>&nbsp;<span class="underline">赞</span>');
                            } else {
                                $this.html('<span class="reborn rb-heart"></span>&nbsp;<span class="underline">取消</span>');
                            }
                            $('#post-like-area-' + cid).toggleClass('hidden', data.likes === 0).html('<span class="reborn rb-heart-o"></span>&nbsp;' + data.likes + '人喜欢');
                            $('#post-view-cid-' + cid).text(data.views);
                        } else {
                            alert('操作失败，请稍后再试。');
                            console.error('操作失败: ', data.message); // 输出错误信息
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('操作失败，请稍后再试。');
                        console.error('AJAX error: ' + textStatus, errorThrown); // 输出错误信息
                    }
                });
            });
        },
        // 评论
        postComment: function () {
            // 评论框显示逻辑
            $(".post-comment").on('click', function (e) {
                $('.post-action').removeClass('show');
                let cid = $(this).data('cid');
                let coid = $(this).data('coid');
                let name = $(this).data('name');
                // 显示评论区域
                $('#post-comment-area-' + cid).removeClass('hidden');
                let $commentForm = $(".comment-form");
                let existsCommentFormCoid = $commentForm.data("coid");
                let existsCommentFormCid = $commentForm.data("cid");
                let hasCommentForm = $commentForm.length > 0;
                $commentForm.remove();
                if (hasCommentForm && existsCommentFormCoid === coid && existsCommentFormCid === cid) {
                    return;
                }
                // 根据是否有 `coid` 决定插入表单的位置
                if (coid === undefined) {
                    $('#comments-cid-' + cid).prepend(getCommentFormHtml(cid));
                } else {
                    $('#comment-coid-' + coid + '>.comment-item-header').after(getCommentFormHtml(cid, coid, name));
                }
            });
            $(".write-comment").on('click',function (e) {
                $('.none-comment').remove();
                $(".comment-form").remove();
                let cid = $(this).data('cid');
                let coid = $(this).data('coid');
                let name = $(this).data('name');
                if (coid === undefined) {
                    $('.form-place').after(getCommentFormHtml(cid));
                } else {
                    $('#comment-coid-' + coid + '>.comment-item-header').after(getCommentFormHtml(cid, coid, name));
                }

            });

            $(document).on('click', '.comment-form', function () {
                $(this).addClass('focus');
            });
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.comment-form').length) {
                    $('.comment-form').removeClass('focus');
                }
            });
            // 评论提交逻辑
            $(document).on('click', '.comment-btn', function (e) {
                e.stopPropagation();
                const $this = $(this);
                let cid = $this.data('cid');
                let coid = $this.data('coid');
                let author = $('.comment-input.comment-input-author').val();
                let mail = $('.comment-input.comment-input-email').val();
                let url = $('.comment-input.comment-input-url').val();
                let text = $('.comment-textarea.comment-input-text').val();
                let param = {
                    cid: cid,
                    parent: coid,
                    author: author,
                    mail: mail,
                    url: url,
                    text: text,
                    uid: userId
                };
                if (param.author === '') {
                    alert('昵称不能为空');
                    return;
                }
                if (commentsRequireMail === 1 && param.mail === '') {
                    alert('邮件不能为空');
                    return;
                }
                if (commentsRequireURL === 1 && param.url === '') {
                    alert('网址不能为空');
                    return;
                }
                if (param.text === '') {
                    alert('评论内容不能为空');
                    return;
                }
                // 记录信息到localStorage
                window.localStorage.setItem('author', author);
                window.localStorage.setItem('mail', mail);
                window.localStorage.setItem('url', url);
                $.ajax({
                    url: themeUrl + '/comment.php',
                    type: 'POST',
                    data: param,
                    success: function (data) {
                        if (data.success) {
                            // 处理成功的响应
                            alert('评论成功');
                            $(".comment-form").remove();
                            $('#post-view-cid-' + cid).text(data.views);
                            location.reload(); // 刷新页面以显示新评论
                        } else {
                            alert('操作失败，请稍后再试。');
                            console.error('操作失败: ', data.message); // 输出错误信息
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('操作失败，请稍后再试。');
                        console.error('AJAX error: ' + textStatus, errorThrown); // 输出错误信息
                    }
                });
            });
        },
    };
    $(document).ready(function() {
        App.postMetaExpand();
        App.postLike();
        App.postComment();
    });
})(jQuery);

function getCommentFormHtml(cid, coid, name) {
    let author = window.localStorage.getItem('author');
    let mail = window.localStorage.getItem('mail');
    let url = window.localStorage.getItem('url');
    if (author == null) author = '';
    if (mail == null) mail = '';
    if (url == null) url = '';
    let loginClass = '';
    let commentMeta = '';
    if (isLogin) {
        author = userName;
        mail = userEmail;
        url = userUrl;
        loginClass = ' hidden';
        commentMeta = ' 已登录';
    }
    let placeHolder = '回复内容';
    if (coid) {
        placeHolder = '回复@' + name;
    } else {
        coid = 0;
    }
    return `
        <div class="comment-form" data-cid="${cid}" data-coid="${coid}">
            <div class="flex comment-meta">
                <div><span class="color-link">${author}</span>${commentMeta}</div>
                <input placeholder="昵称" type="text" class="comment-input comment-input-author${loginClass}" name="comment-author" value="${author}"/>
                <input placeholder="邮箱" type="text" class="comment-input comment-input-email${loginClass}" name="comment-email" value="${mail}"/>
                <input placeholder="网址" type="text" class="comment-input comment-input-url${loginClass}" name="comment-url" value="${url}" />
            </div>
            <div class="comment-area">
                <textarea placeholder="${placeHolder}" class="comment-textarea comment-input-text" name="comment-text"></textarea>
            </div>
            <div class="comment-footer">
                <button class="comment-btn underline" data-cid="${cid}" data-coid="${coid}">回复</button>
            </div>
        </div>
    `;
}