(function ($) {
    const App = {
        // 页面初始化浅色&深色模式
        pageInit: function () {
            // 检查并获取 sessionStorage 项
            function getSessionStorageItem(key) {
                if (typeof sessionStorage !== 'undefined') {
                    return sessionStorage.getItem(key);
                }
                return null;
            }
            const themeMode = getSessionStorageItem('theme-mode');
            if (themeMode) {
                // console.log('Current theme mode is:', themeMode);
                // 根据 themeMode 的值进行操作
                if (themeMode === 'light') {
                    // 设置为 light 主题
                    $('html').attr('theme-mode', 'light');
                    $('.rb-tabs__block').css('left', '2px');
                    loadCssFile(reborn.themeUrl+'lib/highlight@11.9.0/css/atom-one-light.min.css');
                } else if (themeMode === 'dark') {
                    // 设置为 dark 主题
                    $('html').attr('theme-mode', 'dark');
                    $('.rb-tabs__block').css('left', '30px');
                    loadCssFile(reborn.themeUrl+'lib/highlight@11.9.0/css/atom-one-dark.min.css');
                }
            } else {
                const darkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)');
                if (darkMode && darkMode.matches) {
                    $('.rb-tabs__block').css('left', '30px');
                }
                // console.log('No theme mode found in sessionStorage.');
            }
            //在页面代码里面监听onload事件，使用sw的配置文件注册一个service worker
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function () {
                    navigator.serviceWorker.register(reborn.themeUrl+'assets/js/serviceWorker.js')
                        .then(function (registration) {
                            // 注册成功
                            console.log('ServiceWorker registration successful with scope: ', registration.scope);
                        })
                        .catch(function (err) {
                            // 注册失败
                            console.log('ServiceWorker registration failed: ', err);
                        });
                });
            }
        },
        // 浅色&深色模式切换
        themeModeToggle: function () {
            const $themeButtons = $('.rb-item'); // 获取所有带有 .rb-item 类的元素
            const $realButton = $('.rb-tabs__block'); // 遮罩
            const setTheme = (theme, position) => {
                if (!document.startViewTransition) {
                    $('html').attr('theme-mode', theme);
                    $realButton.css('left', position);
                } else {
                    document.startViewTransition(() => {
                        $('html').attr('theme-mode', theme);
                        $realButton.css('left', position);
                    });
                }
                sessionStorage.setItem("theme-mode", theme);
            };
            // 遍历每个按钮，添加点击事件监听器
            $themeButtons.on('click', function() {
                const theme = $(this).data('theme');
                if (theme === 'light') {
                    setTheme('light', '2px');
                    loadCssFile(reborn.themeUrl+'lib/highlight@11.9.0/css/atom-one-light.min.css');
                } else {
                    setTheme('dark', '30px');
                    loadCssFile(reborn.themeUrl+'lib/highlight@11.9.0/css/atom-one-dark.min.css');
                }
                // 更新激活状态
                $themeButtons.removeClass('active');
                $(this).addClass('active');
            });
        },
        pageLoadMore: function () {
            $('.pagination .next').click(function (e){
                e.preventDefault();
                let $this = $(this);
                $this.addClass('loading').text('努力加载中');
                let href = $this.attr('href');
                if (href !== undefined) {
                    $.ajax({
                        url: href,
                        type: 'get',
                        error: function (request) {
                            alert('加载失败！');
                        },
                        success: function (data) {
                            $this.removeClass('loading').text('点击查看更多');
                            let $res = $(data).find('.post-type');
                            $('.pagination').before($res.fadeIn(500));
                            let newhref = $(data).find('.next').attr('href');
                            if (newhref !== undefined) {
                                $this.attr('href', newhref);
                            } else {
                                $this.remove();
                            }
                        }
                    });
                }
            });
        },
        menuToggle: function () {
            $(document).on('click', '.rb-header-nav-mobile.rb-menu, .rb-header-mobile-mask', function () {
                $('.rb-header-mobile-nav, .rb-header-mobile-mask').toggleClass('show');
            });
        },
        // 点击展开点赞/评论
        postMetaExpand: function () {
            $(document).on('click', '.post-more', function () {
                const $postAction = $(this).siblings('.post-action-container').find('.post-action');
                const cid = $postAction.data('cid');
                const likeArray = JSON.parse(localStorage.getItem('postLike') || '[]');
                //console.log("cid: "+cid);
                //console.log(likeArray);
                if (likeArray.includes(cid)) {
                    $postAction.find('.like').hide();
                    $postAction.find('.cancel').show();
                } else {
                    $postAction.find('.cancel').hide();
                    $postAction.find('.like').show();
                }
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
        postLike: function () {
            // 获取并处理 Cookies
            const cleanedCookiesObject = {};
            $.each(document.cookie.split('; '), function(_, cookie) {
                const [key, value] = cookie.split('=');
                // 仅处理包含特定前缀的 cookie
                if (
                    key.includes('_typecho_remember_remember') ||
                    key.includes('_typecho_remember_author') ||
                    key.includes('_typecho_remember_mail') ||
                    key.includes('_typecho_remember_url')
                ) {
                    // 去掉前缀并解码值
                    const cookieKey = key.includes('__') ? key.split('__')[1] : key;
                    cleanedCookiesObject[cookieKey] = decodeURIComponent(value);
                }
            });
            $(document).on('click', '.post-like', function (e) {
                e.preventDefault();
                const $this = $(this);
                const cid = $this.data('cid');
                const location = $this.data('location');
                let likeArray = JSON.parse(localStorage.getItem('postLike') || '[]');
                let isLiked = likeArray.includes(cid);
                let $postLikeArea = $('#post-like-area-' + cid);
                // 禁用按钮，防止重复点击
                $this.prop('disabled', true);
                // 发送 AJAX 请求
                $.ajax({
                    url: reborn.apiUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        cid: cid,
                        routeType: 'postLike',
                        action: isLiked ? 'dislike' : 'like',
                        location: location,
                        cookieObject: JSON.stringify(cleanedCookiesObject)
                    },
                    success: function(response) {
                        if (response.code === 1) {
                            // 更新本地存储
                            if (isLiked) {
                                // 取消点赞
                                likeArray = likeArray.filter(element => element !== cid);
                            } else {
                                // 添加点赞
                                likeArray.push(cid);
                            }
                            localStorage.setItem('postLike', JSON.stringify(likeArray));
                            // 切换点赞/取消点赞的显示
                            $this.find('.like').toggle();
                            $this.find('.cancel').toggle();
                            // 更新点赞数显示
                            let likesTotalNum = response.likesTotalNum;
                            let likesDisplay = response.likesListHtml;
                            if (likesTotalNum === 0) {
                                $postLikeArea.addClass('hidden');
                            } else {
                                $postLikeArea.removeClass('hidden');
                                $postLikeArea.html(likesDisplay);
                            }
                            $('.post-action').removeClass('show');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ', status, error);
                    },
                    complete: function() {
                        // 恢复按钮状态
                        $this.prop('disabled', false);
                    }
                });
            });
        },
        commentLike: function () {
            let likeArray = JSON.parse(localStorage.getItem('commentLike') || '[]');
            $('.comment-like').each(function () {
                const $this = $(this);
                const coid = $this.data('coid');
                if (likeArray.includes(coid)) {
                    $this.find('.reborn').attr('class', 'reborn rb-like');
                }
            });
            $(document).on('click', '.comment-like', function (e) {
                e.preventDefault();
                const $this = $(this);
                const coid = $this.data('coid');
                let isLiked = likeArray.includes(coid);
                $.ajax({
                    url: reborn.apiUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        coid: coid,
                        routeType: 'commentLike',
                        action: isLiked ? 'dislike' : 'like',
                    },
                    success:function (response) {
                        if (response.code === 1) {
                            let likesTotalNum = response.likesTotalNum;
                            // 更新本地存储
                            if (isLiked) {
                                // 取消点赞
                                likeArray = likeArray.filter(element => element !== coid);
                                $this.html(likesTotalNum+'&nbsp;<i class="reborn rb-like-o"></i>');
                            } else {
                                // 添加点赞
                                likeArray.push(coid);
                                $this.html(likesTotalNum+'&nbsp;<i class="reborn rb-like"></i>');
                            }
                            localStorage.setItem('commentLike', JSON.stringify(likeArray));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ', status, error);
                    }
                })
            });
        },
        postComment: function () {
            // 评论框显示逻辑
            $(document).on('click', '.post-comment', function () {
                $('.post-action').removeClass('show');
                let cid = $(this).data('cid');
                let coid = $(this).data('coid');
                let name = $(this).data('name');
                let location = $(this).data('location');
                // 显示评论区域
                let $commentArea = $('.post-comment-area-' + cid);
                $commentArea.removeClass('hidden');
                // 隐藏所有的评论表单
                $('.comment-form').hide();
                // 获取当前的评论表单
                let $commentForm = $commentArea.find('.comment-form');
                let $commentTextArea = $commentArea.find('.comment-textarea');
                if (coid === undefined) {
                    $commentForm.find('#comment-parent').val(0);
                    $commentTextArea.attr('placeholder', '回复内容');
                    if (location === 'index') {
                        $commentArea.prepend($commentForm);
                    } else if (location === 'shuoshuo' || location === 'post') {
                        $commentArea.find('.respond-form').prepend($commentForm);
                    }
                } else {
                    // 设置数据属性和占位符
                    $commentForm.find('#comment-parent').val(coid);
                    $commentTextArea.attr('placeholder', '回复@' + name);
                    // 将评论表单移动到指定位置
                    $commentForm.insertAfter('#comment-' + coid + '>.comment-item-header');
                }
                $commentForm.show().addClass('focus');
                $commentTextArea.focus();
            });
            $(document).on('click', '.comment-form', function () {
                $(this).addClass('focus');
            });
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.comment-form').length && !$(e.target).hasClass('post-comment')) {
                    $('.comment-form').removeClass('focus');
                }
            });
            $(document).on('click', '.comment-cancel', function () {
                $('.none-comment').show();
                let cid = $(this).data('cid');
                $('.comment-form').removeClass('focus').hide();
                if ($('#post-comment-area-'+cid).find('.comment-item').length === 0) {
                    $('#post-comment-area-'+cid).addClass('hidden');
                }
            });
        },
        emojiEvent: function () {
            const emojiBasePath = reborn.themeUrl + 'assets/emoji/';
            let emojiData = {};
            // 加载表情包数据
            $.getJSON(reborn.themeUrl + 'assets/emoji/emojiData.json', function(data) {
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
                if (!emojiData[category]) {
                    console.error('No emojis found for category:', category);
                    return;
                }
                emojiData[category].forEach(function(emoji) {
                    const img = $('<img>').attr('src', reborn.themeUrl + emoji.icon)
                        .attr({'alt': emoji.data, 'title': emoji.data })
                        .addClass('rb-emoji-item')
                        .click(function() {
                            let cid = $(this).parents('.comment-form').data("cid");
                            let area = '.post-comment-area-'+cid+' .comment-textarea';
                            // 表情点击事件
                            const emojiCode = emoji.data;
                            insertAtCaret(area, emojiCode);
                            $('.emoji-container').toggle();
                        });
                    emojiList.append(img);
                });
            }
            // 插入表情到文本框中
            function insertAtCaret(area, text) {
                const txtArea = $(area).get(0);
                if (!txtArea) {
                    return;
                }
                const scrollPos = txtArea.scrollTop;
                let strPos = txtArea.selectionStart;
                const front = (txtArea.value).substring(0, strPos);
                const back = (txtArea.value).substring(strPos, txtArea.value.length);
                txtArea.value = front + text + back;
                strPos = strPos + text.length;
                txtArea.selectionStart = strPos;
                txtArea.selectionEnd = strPos;
                txtArea.focus();
                txtArea.scrollTop = scrollPos;
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
            let $stickyModule = $('#sticky');
            if ($stickyModule.length) {
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
        }
    };

    $(document).ready(function () {
        App.pageInit();
        App.themeModeToggle();
        App.pageLoadMore();
        App.menuToggle();
        App.postMetaExpand();
        App.postLike();
        App.commentLike();
        App.postComment();
        App.emojiEvent();
        hljs.highlightAll();
        hljs.initLineNumbersOnLoad();
        App.codeCopy();
        App.scrollEvent();
    });
})(jQuery);

// 动态加载 CSS 文件的函数，并移除之前的主题 CSS
function loadCssFile(filename) {
    // 先移除之前的主题 CSS
    const existingLink = document.querySelector('link[rel="stylesheet"][code-theme]');
    if (existingLink) {
        existingLink.parentNode.removeChild(existingLink);
    }
    // 创建新的 link 标签加载新的主题 CSS
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = filename;
    link.setAttribute('code-theme', 'true');  // 加一个标识，方便以后移除
    document.head.appendChild(link);
}