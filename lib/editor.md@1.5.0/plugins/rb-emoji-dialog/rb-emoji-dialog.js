/*!
 * rb-emoji plugin for Editor.md
 *
 * @file        rb-emoji-dialog.js
 * @author      Shawn
 * @version     1.0.0
 * @updateTime  2024-07-23
 * @license     MIT
 */

(function() {

    var factory = function (exports) {

        var $            = jQuery;           // if using module loader(Require.js/Sea.js).
        var pluginName   = "rb-emoji-dialog";
        var emojiTabIndex = 0;
        var emojiData     = [];
        var selecteds     = [];

        var langs = {
            "zh-cn" : {
                toolbar : {
                    rbemoji : "Emoji 表情"
                },
                dialog : {
                    rbemoji : {
                        title : "Emoji 表情"
                    }
                }
            },
            "zh-tw" : {
                toolbar : {
                    rbemoji : "Emoji 表情"
                },
                dialog : {
                    rbemoji : {
                        title : "Emoji 表情"
                    }
                }
            },
            "en" : {
                toolbar : {
                    rbemoji : "Emoji"
                },
                dialog : {
                    rbemoji : {
                        title : "Emoji"
                    }
                }
            }
        };

        exports.rbEmojiPlugin = function(){
            alert("rbEmojiPlugin");
        };

        exports.fn.rbEmojiDialog = function() {

            var _this       = this;
            var cm          = this.cm;
            var settings    = _this.settings;

            var path        = settings.pluginPath + pluginName + "/";
            path = path.replace(/(reborn).*$/, '$1')+ "/";
            var editor      = this.editor;
            var cursor      = cm.getCursor();
            var selection   = cm.getSelection();
            var classPrefix = this.classPrefix;

            $.extend(true, this.lang, langs[this.lang.name]);
            this.setToolbar();

            var lang        = this.lang;
            var dialogName  = classPrefix + pluginName, dialog;
            var dialogLang  = lang.dialog.rbemoji;

            var dialogContent = [
                "<div class=\"" + classPrefix + "emoji-dialog-box\" style=\"width: 760px;height: 334px;margin-bottom: 8px;overflow: hidden;\">",
                "<div class=\"" + classPrefix + "tab\"></div>",
                "</div>",
            ].join("\n");

            cm.focus();

            if (editor.find("." + dialogName).length > 0)
            {
                dialog = editor.find("." + dialogName);

                selecteds = [];
                dialog.find("a").removeClass("selected");

                this.dialogShowMask(dialog);
                this.dialogLockScreen();
                dialog.show();
            } else {
                dialog = this.createDialog({
                    name       : dialogName,
                    title      : dialogLang.title,
                    width      : 800,
                    height     : 475,
                    mask       : settings.dialogShowMask,
                    drag       : settings.dialogDraggable,
                    content    : dialogContent,
                    lockScreen : settings.dialogLockScreen,
                    maskStyle  : {
                        opacity         : settings.dialogMaskOpacity,
                        backgroundColor : settings.dialogMaskBgColor
                    },
                    buttons    : {
                        enter  : [lang.buttons.enter, function() {
                            cm.replaceSelection(selecteds.join(" "));
                            this.hide().lockScreen(false).hideMask();

                            return false;
                        }],
                        cancel : [lang.buttons.cancel, function() {
                            this.hide().lockScreen(false).hideMask();

                            return false;
                        }]
                    }
                });
            }

            var category = ["微信", "Bilibili"];
            var tab      = dialog.find("." + classPrefix + "tab");
            if (tab.html() === "")
            {
                var head = "<ul class=\"" + classPrefix + "tab-head\">";

                for (var i = 0; i<2; i++) {
                    var active = (i === 0) ? " class=\"active\"" : "";
                    head += "<li" + active + "><a href=\"javascript:;\">" + category[i] + "</a></li>";
                }

                head += "</ul>";

                tab.append(head);

                var container = "<div class=\"" + classPrefix + "tab-container\">";

                for (var x = 0; x < 2; x++)
                {
                    var display = (x === 0) ? "" : "display:none;";
                    container += "<div class=\"" + classPrefix + "tab-box\" style=\"height: 260px;overflow: hidden;overflow-y: auto;" + display + "\"></div>";
                }

                container += "</div>";

                tab.append(container);
            }

            var tabBoxs = tab.find("." + classPrefix + "tab-box");
            var emojiCategories = ["wechat", "xiaodianshi"];

            var drawTable = function() {
                var cname = emojiCategories[emojiTabIndex];
                var $data = emojiData[cname];
                var $tab  = tabBoxs.eq(emojiTabIndex);

                if ($tab.html() !== "") {
                    //console.log("break =>", cname);
                    return ;
                }

                var pagination = function(data, type) {
                    var rowNumber = 20;
                    var pageTotal = Math.ceil(data.length / rowNumber);
                    var table     = "<div class=\"" + classPrefix + "grid-table\">";
                    for (var i = 0; i < pageTotal; i++)
                    {
                        var row = "<div class=\"" + classPrefix + "grid-table-row\">";
                        for (var x = 0; x < rowNumber; x++)
                        {
                            var emoji = data[(i * rowNumber) + x];

                            if (typeof emoji !== "undefined" && emoji !== "")
                            {
                                var img = "";
                                var src = path + emoji["icon"];
                                img     = "<img src=\"" + src + "\" width=\"24\" class=\"emoji\" title=\"" + emoji["data"] + "\" alt=\"" + emoji["data"] + "\" />";
                                var imgX = "<img src=\"" + src.replace(/"/g, '&quot;') + "\" class=\"rb-emoji-item\" title=\"" + emoji["data"].replace(/"/g, '&quot;') + "\" alt=\"" + emoji["data"].replace(/"/g, '&quot;') + "\" />";
                                row += "<a href=\"javascript:;\" value=\"" + imgX.replace(/"/g, '&quot;') + "\" title=\"" + emoji["data"].replace(/"/g, '&quot;') + "\" class=\"" + classPrefix + "emoji-btn\">" + img + "</a>";


                            }
                            else
                            {
                                row += "<a href=\"javascript:;\" value=\"\"></a>";
                            }
                        }
                        row += "</div>";
                        table += row;
                    }
                    table += "</div>";
                    return table;
                };

                $tab.append(pagination($data, cname));

                $tab.find("." + classPrefix + "emoji-btn").bind(exports.mouseOrTouch("click", "touchend"), function() {
                    $(this).toggleClass("selected");

                    if ($(this).hasClass("selected"))
                    {
                        selecteds.push($(this).attr("value"));
                    }
                });
            };

            if (emojiData.length < 1)
            {
                if (typeof dialog.loading === "function") {
                    dialog.loading(true);
                }
                $.getJSON(path + "assets/emoji/" + "emojiData.json?temp=" + Math.random(), function(json) {
                    if (typeof dialog.loading === "function") {
                        dialog.loading(false);
                    }
                    emojiData = json;
                    drawTable();
                });
            }
            else
            {
                drawTable();
            }

            tab.find("li").bind(exports.mouseOrTouch("click", "touchend"), function() {
                var $this     = $(this);
                emojiTabIndex = $this.index();

                $this.addClass("active").siblings().removeClass("active");
                tabBoxs.eq(emojiTabIndex).show().siblings().hide();
                drawTable();
            });

        };

    };

    // CommonJS/Node.js
    if (typeof require === "function" && typeof exports === "object" && typeof module === "object")
    {
        module.exports = factory;
    }
    else if (typeof define === "function")  // AMD/CMD/Sea.js
    {
        if (define.amd) { // for Require.js

            define(["editormd"], function(editormd) {
                factory(editormd);
            });

        } else { // for Sea.js
            define(function(require) {
                var editormd = require("./../../editormd");
                factory(editormd);
            });
        }
    }
    else
    {
        factory(window.editormd);
    }

})();
