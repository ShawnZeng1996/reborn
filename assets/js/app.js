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
            $(document).on('click', '.comment-cancel', function () {
                let cid = $(this).data('cid');
                $('.comment-form').remove();
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
        emojiEvent: function () {
            const emojiBasePath = themeUrl + '/assets/emoji/';
            const emojiData = {
                alu: [
                    '阿鲁_吐血倒地.png', '阿鲁_深思.png', '阿鲁_抽烟.png', '阿鲁_蜡烛.png', '阿鲁_咽气.png',
                    '阿鲁_得意.png', '阿鲁_装大款.png', '阿鲁_脸红.png', '阿鲁_傻笑.png', '阿鲁_喷血.png',
                    '阿鲁_看不见.png', '阿鲁_尴尬.png', '阿鲁_暗地观察.png', '阿鲁_扇耳光.png', '阿鲁_亲亲.png',
                    '阿鲁_喷水.png', '阿鲁_内伤.png', '阿鲁_害羞.png', '阿鲁_便便.png', '阿鲁_击掌.png',
                    '阿鲁_期待.png', '阿鲁_中刀.png', '阿鲁_不说话.png', '阿鲁_锁眉.png', '阿鲁_呲牙.png',
                    '阿鲁_高兴.png', '阿鲁_无所谓.png', '阿鲁_中枪.png', '阿鲁_阴暗.png', '阿鲁_坐等.png',
                    '阿鲁_喜极而泣.png', '阿鲁_小怒.png', '阿鲁_赞一个.png', '阿鲁_小眼睛.png', '阿鲁_献黄瓜.png',
                    '阿鲁_皱眉.png', '阿鲁_无语.png', '阿鲁_不高兴.png', '阿鲁_哭泣.png', '阿鲁_欢呼.png',
                    '阿鲁_大囧.png', '阿鲁_口水.png', '阿鲁_中指.png', '阿鲁_长草.png', '阿鲁_愤怒.png',
                    '阿鲁_狂汗.png', '阿鲁_投降.png', '阿鲁_鼓掌.png', '阿鲁_肿包.png', '阿鲁_吐舌.png',
                    '阿鲁_汗.png', '阿鲁_看热闹.png', '阿鲁_观察.png', '阿鲁_吐.png', '阿鲁_邪恶.png',
                    '阿鲁_想一想.png', '阿鲁_不出所料.png', '阿鲁_惊喜.png', '阿鲁_抠鼻.png', '阿鲁_黑线.png',
                    '阿鲁_献花.png', '阿鲁_无奈.png'
                ],
                paopao: [
                    '泡泡_笑尿.png', '泡泡_吐舌.png', '泡泡_真棒.png', '泡泡_nico.png', '泡泡_花心.png',
                    '泡泡_钱.png', '泡泡_呼.png', '泡泡_OK.png', '泡泡_笑眼.png', '泡泡_药丸.png',
                    '泡泡_不高兴.png', '泡泡_香蕉.png', '泡泡_黑线.png', '泡泡_疑问.png', '泡泡_太开心.png',
                    '泡泡_呵呵.png', '泡泡_咦.png', '泡泡_酸爽.png', '泡泡_滑稽.png', '泡泡_玫瑰.png',
                    '泡泡_怒.png', '泡泡_礼物.png', '泡泡_冷.png', '泡泡_星星月亮.png', '泡泡_开心.png',
                    '泡泡_乖.png', '泡泡_手纸.png', '泡泡_蛋糕.png', '泡泡_弱.png', '泡泡_心碎.png',
                    '泡泡_三道杠.png', '泡泡_灯泡.png', '泡泡_狂汗.png', '泡泡_犀利.png', '泡泡_便便.png',
                    '泡泡_泪.png', '泡泡_小红脸.png', '泡泡_喷.png', '泡泡_小乖.png', '泡泡_捂嘴笑.png',
                    '泡泡_胜利.png', '泡泡_懒得理.png', '泡泡_鄙视.png', '泡泡_钱币.png', '泡泡_汗.png',
                    '泡泡_太阳.png', '泡泡_啊.png', '泡泡_睡觉.png', '泡泡_哈哈.png', '泡泡_酷.png',
                    '泡泡_爱心.png', '泡泡_大拇指.png', '泡泡_红领巾.png', '泡泡_茶杯.png', '泡泡_吐.png',
                    '泡泡_惊哭.png', '泡泡_沙发.png', '泡泡_生气.png', '泡泡_挖鼻.png', '泡泡_蜡烛.png',
                    '泡泡_彩虹.png', '泡泡_音乐.png', '泡泡_委屈.png', '泡泡_勉强.png', '泡泡_惊讶.png',
                    '泡泡_呀咩爹.png', '泡泡_你懂的.png', '泡泡_阴险.png', '泡泡_what.png'
                ],
                xiaodianshi: [
                    '小电视_打脸.png', '小电视_晕.png', '小电视_生气.png', '小电视_流鼻血.png', '小电视_呕吐.png',
                    '小电视_委屈.png', '小电视_大哭.png', '小电视_点赞.png', '小电视_闭嘴.png', '小电视_发财.png',
                    '小电视_亲亲.png', '小电视_鬼脸.png', '小电视_尴尬.png', '小电视_害羞.png', '小电视_惊吓.png',
                    '小电视_吐血.png', '小电视_偷笑.png', '小电视_黑人问号.png', '小电视_冷漠.png', '小电视_滑稽笑.png',
                    '小电视_馋.png', '小电视_色.png', '小电视_鄙视.png', '小电视_发呆.png', '小电视_皱眉.png',
                    '小电视_抓狂.png', '小电视_发怒.png', '小电视_目瞪口呆.png', '小电视_大佬.png', '小电视_白眼.png',
                    '小电视_腼腆.png', '小电视_调皮.png', '小电视_难过.png', '小电视_鼓掌.png', '小电视_可爱.png',
                    '小电视_思考.png', '小电视_微笑.png', '小电视_睡着.png', '小电视_调侃.png', '小电视_坏笑.png',
                    '小电视_流泪.png', '小电视_再见.png', '小电视_捂脸哭.png', '小电视_狗头.png', '小电视_困困.png',
                    '小电视_无奈.png', '小电视_生病.png', '小电视_疑问.png', '小电视_抠鼻.png', '小电视_流汗.png'
                ],
                koukou: [
                    '扣扣_调皮.png', '扣扣_嘘.png', '扣扣_秃头.png', '扣扣_难过.png', '扣扣_hi.png',
                    '扣扣_敬礼.png', '扣扣_事不关己.png', '扣扣_眼馋.png', '扣扣_卖萌.png', '扣扣_抓狂_2.png',
                    '扣扣_凉凉.png', '扣扣_鬼.png', '扣扣_收红包.png', '扣扣_丑拒.png', '扣扣_开心笑.png',
                    '扣扣_耍酷.png', '扣扣_投降.png', '扣扣_蛋糕.png', '扣扣_猪头.png', '扣扣_可爱.png',
                    '扣扣_背手.png', '扣扣_口罩加油.png', '扣扣_枯萎.png', '扣扣_食指.png', '扣扣_闪电.png',
                    '扣扣_小汗.png', '扣扣_抓狂.png', '扣扣_无语.png', '扣扣_邪笑.png', '扣扣_发怒.png',
                    '扣扣_欢呼.png', '扣扣_刀.png', '扣扣_礼物.png', '扣扣_骷髅.png', '扣扣_愉悦.png',
                    '扣扣_玫瑰.png', '扣扣_麦克风.png', '扣扣_滑稽.png', '扣扣_难受.png', '扣扣_溴.png',
                    '扣扣_黑眼圈.png', '扣扣_工作中.png', '扣扣_欢庆.png', '扣扣_药丸.png', '扣扣_炸弹.png',
                    '扣扣_再见.png', '扣扣_西瓜.png', '扣扣_乒乓球.png', '扣扣_右哼哼.png', '扣扣_转身.png',
                    '扣扣_柠檬精.png', '扣扣_狗头滑稽.png', '扣扣_流汗.png', '扣扣_抠鼻.png', '扣扣_爬虫.png',
                    '扣扣_done.png', '扣扣_咦.png', '扣扣_可怜.png', '扣扣_发.png', '扣扣_无奈.png',
                    '扣扣_鼓掌.png', '扣扣_跳.png', '扣扣_完了.png', '扣扣_流氓.png', '扣扣_牛.png',
                    '扣扣_吐舌.png', '扣扣_背过头.png', '扣扣_赞.png', '扣扣_吃瓜.png', '扣扣_棒棒糖.png',
                    '扣扣_get.png', '扣扣_OK.png', '扣扣_屎.png', '扣扣_捂脸.png', '扣扣_啾咪.png',
                    '扣扣_邮件.png', '扣扣_微笑.png', '扣扣_啊？.png', '扣扣_滚.png', '扣扣_烟花.png',
                    '扣扣_炮竹.png', '扣扣_啊这.png', '扣扣_狗头放大.png', '扣扣_内疚.png', '扣扣_阴险.png',
                    '扣扣_看手机.png', '扣扣_哭.png', '扣扣_委屈哭.png', '扣扣_卖萌_2.png', '扣扣_篮球.png',
                    '扣扣_变形.png', '扣扣_疑惑.png', '扣扣_色.png', '扣扣_心碎.png', '扣扣_嘿嘿.png',
                    '扣扣_瘪嘴.png', '扣扣_捏鼻子.png', '扣扣_戳脸.png', '扣扣_米饭.png', '扣扣_推眼镜.png',
                    '扣扣_苦笑.png', '扣扣_亲亲.png', '扣扣_打头.png', '扣扣_呕吐.png', '扣扣_颤抖.png',
                    '扣扣_耍酷_2.png', '扣扣_rock.png', '扣扣_爱死你了.png', '扣扣_蜡烛.png', '扣扣_捂嘴笑.png',
                    '扣扣_菜狗滑稽.png', '扣扣_努力工作.png', '扣扣_挤眉.png', '扣扣_菜刀.png', '扣扣_左哼哼.png',
                    '扣扣_生气.png', '扣扣_汗_2.png', '扣扣_s192.png', '扣扣_头大.png', '扣扣_？？？.png',
                    '扣扣_摸鱼.png', '扣扣_闭嘴.png', '扣扣_亲亲_3.png', '扣扣_灯笼.png', '扣扣_惊讶.png',
                    '扣扣_踩.png', '扣扣_亲亲_2.png', '扣扣_咖啡.png', '扣扣_汗.png', '扣扣_揶揄.png',
                    '扣扣_大哭.png', '扣扣_跳绳.png', '扣扣_撑大眼.png', '扣扣_囍.png', '扣扣_酸柠檬.png',
                    '扣扣_吃糖.png', '扣扣_庆祝.png', '扣扣_包子.png', '扣扣_爱心.png', '扣扣_口罩.png',
                    '扣扣_打哈欠.png', '扣扣_发呆.png', '扣扣_足球.png', '扣扣_太阳.png', '扣扣_睡觉.png',
                    '扣扣_大叫.png', '扣扣_鄙视.png', '扣扣_疯狂.png', '扣扣_旋转.png', '扣扣_+1.png',
                    '扣扣_月亮.png', '扣扣_抱拳了.png', '扣扣_打call.png', '扣扣_晕.png', '扣扣_好的.png',
                    '扣扣_拳头.png', '扣扣_对.png', '扣扣_耶.png', '扣扣_星星眼.png', '扣扣_喝咖啡.png',
                    '扣扣_捧花.png', '扣扣_握手.png', '扣扣_肌肉.png', '扣扣_错.png', '扣扣_笑哭.png',
                    '扣扣_勾手.png', '扣扣_吐血.png', '扣扣_飞吻.png', '扣扣_花痴.png', '扣扣_偷笑.png',
                    '扣扣_口罩抱抱.png', '扣扣_美味.png', '扣扣_啤酒.png', '扣扣_狂笑.png', '扣扣_害羞.png',
                    '扣扣_抱抱.png', '扣扣_惊讶_2.png', '扣扣_惊吓.png', '扣扣_奋斗.png', '扣扣_红唇.png',
                    '扣扣_比心.png', '扣扣_小拇指.png', '扣扣_不开心.png', '扣扣_抱拳.png', '扣扣_方块.png'
                ],
            };
            //preloadEmojis();
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
                    const altText = emoji.replace('.png', ''); // 去掉.png后缀
                    const img = $('<img>').attr('src', emojiBasePath + category + '/' + emoji)
                        .attr({'alt': altText, 'title': altText })
                        .addClass('rb-emoji-item')
                        .click(function() {
                            // 表情点击事件
                            const emojiCode = '@(' + altText + ')';
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
                loadEmojis('xiaodianshi');
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
            <div class="comment-footer relative flex">
                <span class="reborn rb-smile comment-emoji" id="toggle-emoji-picker"></span>
                <div class="emoji-container absolute">
                    <div class="emoji-list"></div>
                    <div class="emoji-bar flex">
                        <div class="emoji-category active" data-category="xiaodianshi">小电视</div>
                        <div class="emoji-category" data-category="koukou">扣扣</div>
                        <div class="emoji-category" data-category="alu">阿鲁</div>
                        <div class="emoji-category" data-category="paopao">泡泡</div>
                    </div>
                </div>
                <div class="comment-footer-btn">
                    <button class="comment-cancel" data-cid="${cid}">取消</button>
                    <button class="comment-btn underline" data-cid="${cid}" data-coid="${coid}">回复</button>
                </div>
            </div>
        </div>
    `;
}
