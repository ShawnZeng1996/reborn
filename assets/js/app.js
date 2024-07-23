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
                let $commentArea = $('#post-comment-area-' + cid);
                $commentArea.removeClass('hidden');
                // 隐藏所有的评论表单
                $('.comment-form').hide();
                // 获取当前的评论表单
                let $commentForm = $commentArea.find('.comment-form');
                if (coid === undefined) {
                    $commentArea.find('.comment-textarea').attr('placeholder', '回复内容');
                } else {
                    // 设置数据属性和占位符
                    $commentForm.attr('data-coid', coid);
                    $commentForm.find('.comment-btn').attr('data-coid', coid);
                    $commentArea.find('.comment-textarea').attr('placeholder', '回复@' + name);
                    // 将评论表单移动到指定位置
                    $commentForm.insertAfter('#comment-' + coid + '>.comment-item-header');
                }
                $commentForm.show();
            });

            $(".write-comment").on('click',function (e) {
                $('.none-comment').hide();
                //$(".comment-form").remove();
                let cid = $(this).data('cid');
                let coid = $(this).data('coid');
                let name = $(this).data('name');
                // 显示评论区域
                let $commentArea = $('#comments');
                let $commentForm = $commentArea.find('.comment-form');
                console.log('cid:'+cid+';coid'+coid);
                if (coid === undefined) {
                    $commentArea.find('.comment-textarea').attr('placeholder', '回复内容');
                } else {
                    // 设置数据属性和占位符
                    $commentForm.attr('data-coid', coid);
                    $commentForm.find('.comment-btn').attr('data-coid', coid);
                    $commentArea.find('.comment-textarea').attr('placeholder', '回复@' + name);
                    // 将评论表单移动到指定位置
                    $commentForm.insertAfter('#comment-' + coid + '>.comment-item-header');
                }
                $commentForm.show();
            });

            $(document).on('click', '.comment-form', function () {
                $(this).addClass('focus');
            });
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.comment-form').length) {
                    $('.comment-form').removeClass('focus');
                }
            });
            $(document).on('click', '.comment-cancel', function () {
                $('.none-comment').show();
                let cid = $(this).data('cid');
                $('.comment-form').hide();
                if ($('#post-comment-area-'+cid).find('.comment-item').length === 0) {
                    $('#post-comment-area-'+cid).addClass('hidden');
                }
            });

            // 评论提交逻辑
            $(document).on('click', '.comment-btn', function (e) {
                e.stopPropagation();
                const $this = $(this);
                let cid = $this.data('cid');
                let coid = $this.data('coid');
                let $commentArea = $('.post-comment-area-' + cid);
                let author = $commentArea.find('.comment-input.comment-input-author').val();
                let mail = $commentArea.find('.comment-input.comment-input-email').val();
                let url = $commentArea.find('.comment-input.comment-input-url').val();
                let text = $commentArea.find('.comment-textarea.comment-input-text').val();
                console.log(text);
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
                            // 记录已评论的文章cid
                            //let commentedCids = JSON.parse(window.localStorage.getItem('commentedCids')) || [];
                            //if (!commentedCids.includes(cid)) {
                            //    commentedCids.push(cid);
                            //}
                            //window.localStorage.setItem('commentedCids', JSON.stringify(commentedCids));

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
        emojiEvent: function () {
            const emojiBasePath = themeUrl + 'assets/emoji/';
            let emojiData = {};
            // 加载表情包数据
            $.getJSON(themeUrl + 'assets/emoji/emojiData.json', function(data) {
                emojiData = data;
                //preloadEmojis();
                loadEmojis('wechat');
            });
            // 预加载所有表情图像
            function preloadEmojis() {
                Object.keys(emojiData).forEach(category => {
                    emojiData[category].forEach(emoji => {
                        const img = new Image();
                        img.src = emojiBasePath + category + '/' + emoji;
                    });
                });
            }
            // 加载表情页
            function loadEmojis(category) {
                const emojiList = $('.emoji-list');
                emojiList.empty(); // 清空现有的表情列表
                emojiData[category].forEach(function(emoji) {
                    const img = $('<img>').attr('src', themeUrl + emoji.icon)
                        .attr({'alt': emoji.data, 'title': emoji.data })
                        .addClass('rb-emoji-item')
                        .click(function() {
                            // 表情点击事件
                            const emojiCode = emoji.data;
                            insertAtCaret('.comment-textarea', emojiCode);
                            $('.emoji-container').toggle();
                        });
                    emojiList.append(img);
                });
            }
            // 插入表情到文本框中
            function insertAtCaret(area, text) {
                const txtarea = $(area).get(0);
                if (!txtarea) {
                    return;
                }
                const scrollPos = txtarea.scrollTop;
                let strPos = txtarea.selectionStart;
                const front = (txtarea.value).substring(0, strPos);
                const back = (txtarea.value).substring(strPos, txtarea.value.length);
                txtarea.value = front + text + back;
                strPos = strPos + text.length;
                txtarea.selectionStart = strPos;
                txtarea.selectionEnd = strPos;
                txtarea.focus();
                txtarea.scrollTop = scrollPos;
            }
            // 切换类别
            $(document).on('click', '.emoji-category', function () {
                $('.emoji-category').removeClass('active');
                $(this).addClass('active');
                const category = $(this).data('category');
                loadEmojis(category);
            });
            // 显示/隐藏表情选择器
            $(document).on('click', '#toggle-emoji-picker', function () {
                $('.emoji-container').toggle();
                loadEmojis('wechat');
            });
            $(document).mouseup(function(e) {
                if (!$(e.target).hasClass('comment-emoji')) {
                    // 如果点击的不是.reborn.rb-emoji及其子元素
                    $("#emoji-container").each(function() {
                        const container = $(this);
                        // 还要确保点击的不是container内部的元素
                        if (!container.is(e.target) && container.has(e.target).length === 0) {
                            container.hide();
                        }
                    });
                }
            });
        },
        codeCopy: function () {
            $('pre').each(function(index) {
                const $pre = $(this);
                const $code = $pre.find('code').first(); // 查找第一个 code 标签
                if ($code.length > 0) {
                    const uniqueId = 'rb-code-' + index;
                    $code.before('<span class="reborn rb-down code-hide"></span><span class="reborn rb-copy code-copy" data-clipboard-action="copy" data-clipboard-target="#' + uniqueId + '"></span>'); // 在 code 标签前添加按钮
                    $code.attr('id', uniqueId);
                    var clipboard = new ClipboardJS('.code-copy');
                    clipboard.on('success', function(e) {
                        //console.log(e);
                    });
                    clipboard.on('error', function(e) {
                        //console.log(e);
                    });
                }
            });
            // 点击 code-hide 标签时隐藏/显示对应的 code 标签，并切换 rb-down 和 rb-up 类
            $(document).on('click', '.code-hide', function() {
                const $this = $(this);
                const $code = $this.nextAll('code').first(); // 获取相邻的 code 标签
                if ($code.css('max-height') === '35px') {
                    $code.css('max-height', 'none');
                    $code.css('padding-bottom', '15px');
                } else {
                    $code.css('max-height', '35px');
                    $code.css('padding-bottom', '5px');
                }
                $this.toggleClass('rb-down rb-up'); // 切换 rb-down 和 rb-up 类
            });
        },
        scrollEvent: function () {
            var $stickyModule = $('#sticky');
            var stickyModuleOffset = $stickyModule.offset().top;
            var isSticky = false;
            function checkSticky() {
                var moduleRect = $stickyModule[0].getBoundingClientRect();
                var moduleWidth = moduleRect.width;
                if ($(window).scrollTop() > stickyModuleOffset) {
                    if (!isSticky) {
                        $stickyModule.css('width', moduleWidth).addClass('sticky');
                        isSticky = true;
                    }
                } else {
                    if (isSticky) {
                        $stickyModule.css('width', '').removeClass('sticky');
                        isSticky = false;
                    }
                }
            }
            $(window).on('scroll', function() {
                requestAnimationFrame(checkSticky);
            });
            // 监听滚动事件，更新目录项样式
            var $tocLinks = $('.toc-link');
            var $headers = $('#post-content h1, #post-content h2, #post-content h3, #post-content h4, #post-content h5');
            function scrollCheck() {
                var scrollTop = $(window).scrollTop();
                var activeIndex = -1;
                $headers.each(function(index) {
                    if ($(this).offset().top - scrollTop < 40) {
                        activeIndex = index;
                    }
                });
                $tocLinks.removeClass('active');
                if (activeIndex >= 0) {
                    $tocLinks.eq(activeIndex).addClass('active');
                } else {
                    // 当没有找到高亮的标题时，默认高亮第一个目录项
                    $tocLinks.eq(0).addClass('active');
                }
            }
            scrollCheck();
            $(window).on('scroll', function() {
                scrollCheck();
            });
        }
    };
    $(document).ready(function() {
        App.postMetaExpand();
        App.postLike();
        App.postComment();
        App.emojiEvent();
        hljs.highlightAll();
        hljs.initLineNumbersOnLoad();
        App.codeCopy();
        App.scrollEvent();
    });
})(jQuery);
