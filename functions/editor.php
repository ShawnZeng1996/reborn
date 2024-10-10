<?php

Typecho\Plugin::factory('admin/write-post.php')->richEditor  = array('Editor', 'Edit');
Typecho\Plugin::factory('admin/write-page.php')->richEditor  = array('Editor', 'Edit');
class Editor
{
    public static function Edit() {
        ?>
        <link rel="stylesheet" href="<?php Helper::options()->themeUrl('lib/editor.md@1.5.0/fonts/iconfont.css'); ?>">
        <link rel="stylesheet" href="<?php Helper::options()->themeUrl('lib/editor.md@1.5.0/css/editormd.css'); ?>">
        <script>
            var uploadUrl = '<?php Helper::security()->index('/action/upload?cid=CID'); ?>';
            var emojiPath = '<?php Helper::options()->themeUrl(); ?>';
        </script>
        <script type="text/javascript" src="<?php Helper::options()->themeUrl('lib/editor.md@1.5.0/js/editormd.js'); ?>"></script>
        <script>
            $(document).ready(function() {
                $('#text').wrap("<div id='text-editormd'></div>");
                var postEditormd = editormd("text-editormd", {
                    width: "100%",
                    height: 640,
                    path: '<?php Helper::options()->themeUrl(); ?>/lib/editor.md@1.5.0/lib/',
                    toolbarAutoFixed: false,
                    htmlDecode: true,
                    tex: true,
                    toc: false,
                    tocm: false,
                    taskList: true,
                    flowChart: false,
                    sequenceDiagram: true,
                    toolbarIcons: function () {
                        return ["undo", "redo", "|", "bold", "del", "italic", "quote", "h2", "h3", "h4", "h5", "|", "list-ul", "list-ol", "checkbox-checked", "checkbox", "hr", "|", "link", "reference-link", "friend-link", "image", "code", "code-block", "table", "more", "hide", "gallery", "rb-emoji","|", "goto-line", "watch", "preview", "fullscreen", "clear", "|", "help", "info"]
                    },
                    toolbarIconsClass: {
                        more: "fa-depart",
                        "checkbox-checked": "fa-checkbox-checked",
                        "checkbox": "fa-checkbox",
                        "friend-link": "fa-friend-link",
                        "hide": "fa-unlock",
                        "gallery": "fa-gallery",
                        "rb-emoji": "fa-smile"
                    },
                    // 自定义工具栏按钮的事件处理
                    toolbarHandlers: {
                        /**
                         * @param {Object}      cm         CodeMirror对象
                         * @param {Object}      icon       图标按钮jQuery元素对象
                         * @param {Object}      cursor     CodeMirror的光标对象，可获取光标所在行和位置
                         * @param {String}      selection  编辑器选中的文本
                         */
                        more: function (cm, icon, cursor, selection) {
                            cm.replaceSelection("<!--more-->");
                        },
                        "checkbox-checked": function (cm) {
                            cm.replaceSelection("[x] ");
                        },
                        "checkbox": function (cm) {
                            cm.replaceSelection("[ ] ");
                        },
                        "friend-link": function (cm) {
                            // 插入包含换行符的 [hide][/hide] 标签
                            cm.replaceSelection('[farea][flink href="站点地址" name="站点名称" img="图片地址" description="站点描述" comment="我的印象"][/farea]');
                        },
                        "hide": function (cm) {
                            // 插入包含换行符的 [hide][/hide] 标签
                            cm.replaceSelection("[hide]\n\n[/hide]");
                            // 将光标定位到换行符之间，方便用户输入内容
                            let cursor = cm.getCursor();
                            cm.setCursor({line: cursor.line - 1, ch: 0});
                        },
                        "gallery": function (cm) {
                            // 插入包含换行符的 [hide][/hide] 标签
                            cm.replaceSelection("[gallery]\n\n[/gallery]");
                            // 将光标定位到换行符之间，方便用户输入内容
                            let cursor = cm.getCursor();
                            cm.setCursor({line: cursor.line - 1, ch: 0});
                        },
                        "rb-emoji" : function() {
                            this.executePlugin("rbEmojiDialog", "rb-emoji-dialog/rb-emoji-dialog");
                        },
                    },
                    lang: {
                        toolbar: {
                            more: "插入摘要分隔符",
                            "checkbox-checked": "插入待办事项（已办）",
                            "checkbox": "插入待办事项（未办）",
                            "hide": "插入回复可见内容",
                            "gallery": "插入说说九图",
                            "rb-emoji": "插入表情包",
                            "friend-link": "插入友情链接"
                        }
                    },
                });

                Typecho.insertFileToEditor = function (file, url, isImage) {
                    html = isImage ? '![' + file + '](' + url + ')'
                        : '[' + file + '](' + url + ')';
                    postEditormd.insertValue(html);
                };

                // 支持粘贴图片直接上传
                $(document).on('paste', function(event) {
                    event = event.originalEvent;
                    var cbd = event.clipboardData;
                    var ua = window.navigator.userAgent;
                    if (!(event.clipboardData && event.clipboardData.items)) {
                        return;
                    }
                    if (cbd.items && cbd.items.length === 2 && cbd.items[0].kind === "string" && cbd.items[1].kind === "file" &&
                        cbd.types && cbd.types.length === 2 && cbd.types[0] === "text/plain" && cbd.types[1] === "Files" &&
                        ua.match(/Macintosh/i) && Number(ua.match(/Chrome\/(\d{2})/i)[1]) < 49){
                        return;
                    }
                    var itemLength = cbd.items.length;
                    if (itemLength == 0) {
                        return;
                    }
                    if (itemLength == 1 && cbd.items[0].kind == 'string') {
                        return;
                    }
                    if ((itemLength == 1 && cbd.items[0].kind == 'file')
                        || itemLength > 1
                    ) {
                        for (var i = 0; i < cbd.items.length; i++) {
                            var item = cbd.items[i];

                            if(item.kind == "file") {
                                var blob = item.getAsFile();
                                if (blob.size === 0) {
                                    return;
                                }
                                var ext = 'jpg';
                                switch(blob.type) {
                                    case 'image/jpeg':
                                    case 'image/pjpeg':
                                        ext = 'jpg';
                                        break;
                                    case 'image/png':
                                        ext = 'png';
                                        break;
                                    case 'image/gif':
                                        ext = 'gif';
                                        break;
                                }
                                var formData = new FormData();
                                formData.append('blob', blob, Math.floor(new Date().getTime() / 1000) + '.' + ext);
                                var uploadingText = '![图片上传中(' + i + ')...]';
                                var uploadFailText = '![图片上传失败(' + i + ')]'
                                postEditormd.insertValue(uploadingText);
                                $.ajax({
                                    method: 'post',
                                    url: uploadUrl.replace('CID', $('input[name="cid"]').val()),
                                    data: formData,
                                    contentType: false,
                                    processData: false,
                                    success: function(data) {
                                        if (data[0]) {
                                            postEditormd.setValue(postEditormd.getValue().replace(uploadingText, '![](' + data[0] + ')'));
                                        } else {
                                            postEditormd.setValue(postEditormd.getValue().replace(uploadingText, uploadFailText));
                                        }
                                    },
                                    error: function() {
                                        postEditormd.setValue(postEditormd.getValue().replace(uploadingText, uploadFailText));
                                    }
                                });
                            }
                        }
                    }
                });
            });
        </script>
        <?php
    }
}