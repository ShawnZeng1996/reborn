<?php


// 主题设置
function themeConfig($form): void {
    $db = \Typecho\Db::get();
    $prefix = $db->getPrefix();
    try {
        if (!array_key_exists('postType', $db->fetchRow($db->select()->from('table.contents')->page(1, 1)))) {
            $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `postType` varchar(16) NOT NULL DEFAULT "post";');
        }
        if (!array_key_exists('likes', $db->fetchRow($db->select()->from('table.contents')->page(1, 1)))) {
            $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `likes` INT(10) NOT NULL DEFAULT 0;');
        }
        if (!array_key_exists('likes', $db->fetchRow($db->select()->from('table.comments')->page(1, 1)))) {
            $db->query('ALTER TABLE `' . $prefix . 'comments` ADD `likes` INT(10) NOT NULL DEFAULT 0;');
        }
        if (!array_key_exists('views', $db->fetchRow($db->select()->from('table.contents')->page(1, 1)))) {
            $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `views` INT(10) NOT NULL DEFAULT 0;');
        }
        if (!array_key_exists('region', $db->fetchRow($db->select()->from('table.comments')->page(1, 1)))) {
            $db->query('ALTER TABLE `' . $prefix . 'comments` ADD `region` varchar(50) NULL');
        }
        // 检查是否已有文章点赞列表
        $sql = "SHOW TABLES LIKE '{$prefix}post_like_list'";
        $result = $db->fetchRow($sql);
        if (!$result) {
            // 创建文章点赞列表
            $sql = "CREATE TABLE IF NOT EXISTS `{$prefix}post_like_list`(
                `id` INT NOT NULL AUTO_INCREMENT,
                `cid` INT(10) UNSIGNED NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `mail` VARCHAR(255) NOT NULL,
                `url` VARCHAR(255),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            $db->query($sql);
        }
    } catch (Exception $e) {
    }
    ?>
    <style>
        .col-mb-12.col-tb-8.col-tb-offset-2 {
            margin-left: 0;
            width: 100%;
        }
        .reborn-config {
            display: flex;
        }
        .reborn-config * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            outline: none;
            -webkit-tap-highlight-color: transparent;
        }
        .reborn-config ul {
            list-style: none;
        }
        .reborn-config-menu,
        .reborn-config-index,
        .reborn-config > form {
            background-color: ##F8F8F8;
            border: 1px solid rgba(0, 0, 0, .1);
            padding: 10px;
        }
        .reborn-config-menu {
            width: 220px;
        }
        .reborn-config-menu .logo {
            color: #444444;
            font-weight: 500;
            font-size: 24px;
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px solid rgba(0, 0, 0, .1);
            padding-bottom: 10px;
        }
        .reborn-config-menu .tabs .tab-item {
            border-radius: 20px;
            text-align: center;
            height: 40px;
            line-height: 40px;
            color: #444444;
            cursor: pointer;
            transition: background 0.35s;
            user-select: none;
        }
        .reborn-config-index,
        .reborn-config > form {
            flex: 1;
            margin-left: 10px;
        }
        .reborn-config-index .title {
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, .1);
            font-weight: 500;
            font-size: 22px;
            margin-bottom: 15px;
            color: #444444;
        }
        .reborn-config > form {
            display: none;
            position: relative;
        }
        .reborn-config > form .typecho-label {
            display: block;
            margin-top: .77em;
            margin-bottom: .3em;
            padding-left: 15px;
            padding-right: 15px;
            color: #999;
            font-size: 14px;
        }
        .reborn-config > form .typecho-option {
            position: sticky;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 15px;
            background: ##F8F8F8;
            border-top: 1px solid #ebebeb;
            border-radius: 0 0 8px 8px;
        }
        .typecho-option .btn {
            display: block;
            margin-left: auto;
            margin-right: auto;
            padding-left: 14px;
            padding-right: 14px;
            box-sizing: border-box;
            text-align: center;
            color: #fff;
            border-radius: 5px;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
            overflow: hidden;
            background-color: #1aad19;
        }
        @media (max-width: 768px) {
            .reborn-config {
                display: block;
            }
            .reborn-config-menu {
                width: 100%;
                margin-bottom: 15px;
            }
            .reborn-config-index,
            .reborn-config > form {
                margin-left: 0;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.tab-item');
            const rebornInfoContent = document.querySelector('.reborn-info'); // 第一个tab对应的内容
            const rebornContents = document.querySelectorAll('.reborn-content'); // 其他tab对应的内容
            const rebornForm = document.querySelector('.reborn-config form'); // 对应配置表单
            function hideAllTabs() {
                // 隐藏所有内容
                rebornInfoContent.style.display = 'none';
                rebornContents.forEach(function (content) {
                    content.style.display = 'none';
                });
            }
            function showTab(tab) {
                const current = tab.getAttribute('data-current');
                if (current === 'reborn-info') {
                    rebornInfoContent.style.display = 'block';
                    rebornForm.style.display = 'none';
                } else {
                    rebornForm.style.display = 'block';
                    rebornInfoContent.style.display = 'none';
                    let formItems = document.querySelectorAll('.' + current);
                    formItems.forEach(function (formItem){
                        formItem.style.display = 'block';
                    });
                }
            }
            // 默认显示第一个tab内容
            hideAllTabs();
            showTab(tabs[0]);
            // 绑定点击事件，切换显示内容
            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    hideAllTabs();
                    showTab(tab);
                });
            });
        });
    </script>
    <div class="reborn-config">
        <div>
            <div class="reborn-config-menu">
                <div class="logo">Reborn 2.0</div>
                <ul class="tabs">
                    <li class="tab-item" data-current="reborn-info">主题信息</li>
                    <li class="tab-item" data-current="reborn-global">全局设置</li>
                    <li class="tab-item" data-current="reborn-image">图片设置</li>
                    <li class="tab-item" data-current="reborn-post">文章设置</li>
                    <li class="tab-item" data-current="reborn-sidebar">侧栏设置</li>
                    <li class="tab-item" data-current="reborn-home">首页设置</li>
                    <li class="tab-item" data-current="reborn-other">其他设置</li>
                </ul>
            </div>
        </div>
        <div class="reborn-config-index reborn-info reborn-content">
            <p class="title">最新版本：2.0</p>
        </div>
    <?php
    // ----------------------------全局设置----------------------------
    // Gravatar头像源
    $gravatarPrefix = new Typecho\Widget\Helper\Form\Element\Text(
        'gravatarPrefix',
        NULL,
        'https://cravatar.cn/avatar/',
        _t('Gravatar头像源'),
        _t('Gravatar头像源，默认使用Cravatar')
    );
    $gravatarPrefix->setAttribute('class', 'reborn-content reborn-global');
    $form->addInput($gravatarPrefix);
    // 腾讯位置服务API Key
    $tencentMapApiKey = new Typecho\Widget\Helper\Form\Element\Text(
        'tencentMapApiKey',
        NULL,
        NULL,
        _t('腾讯位置服务API Key'),
        _t('腾讯位置服务API Key，未填写则无法获取用户地理位置')
    );
    $tencentMapApiKey->setAttribute('class', 'reborn-content reborn-global');
    $form->addInput($tencentMapApiKey);
    // Bark通知地址与Key
    $barkUrl = new Typecho\Widget\Helper\Form\Element\Text(
        'barkUrl',
        NULL,
        NULL,
        _t('Bark通知地址与Key'),
        _t('Bark通知地址与Key，填写后可以通过Bark App获取博客评论消息通知')
    );
    $barkUrl->setAttribute('class', 'reborn-content reborn-global');
    $form->addInput($barkUrl);
    // ----------------------------侧栏设置----------------------------
    // 侧边栏最近在玩
    $sidebarRecentPlay = new Typecho\Widget\Helper\Form\Element\Textarea(
        'sidebarRecentPlay',
        NULL,
        NULL,
        _t('最近在玩的游戏'),
        _t('最近在玩的游戏，以 游戏名 | 链接 | 图片链接 的形式填写，一个游戏一行。')
    );
    $sidebarRecentPlay->setAttribute('class', 'reborn-content reborn-sidebar');
    $form->addInput($sidebarRecentPlay);
    // 侧边栏广告
    $sidebarAd = new Typecho\Widget\Helper\Form\Element\Textarea(
        'sidebarAd',
        NULL,
        NULL,
        _t('侧边栏广告'),
        _t('侧边栏广告')
    );
    $sidebarAd->setAttribute('class', 'reborn-content reborn-sidebar');
    $form->addInput($sidebarAd);
    // 侧边栏设置
    $sidebarBlock = new Typecho\Widget\Helper\Form\Element\Checkbox(
        'sidebarBlock',
        [
            'ShowRecentPosts'    => _t('显示最新文章'),
            'ShowRecentComments' => _t('显示最近评论'),
            'ShowCategory'       => _t('显示分类'),
            'ShowOther'          => _t('显示其它杂项')
        ],
        ['ShowRecentPosts', 'ShowRecentComments', 'ShowCategory', 'ShowOther'],
        _t('侧边栏显示')
    );
    $sidebarBlock->setAttribute('class', 'reborn-content reborn-sidebar');
    $form->addInput($sidebarBlock->multiMode());
    // ----------------------------首页设置----------------------------
    // 主页头像
    $avatarEmail = new Typecho\Widget\Helper\Form\Element\Text(
        'avatarEmail',
        NULL,
        NULL,
        _t('主页头像邮箱'),
        _t('主页头像邮箱，调用Gravatar头像')
    );
    $avatarEmail->setAttribute('class', 'reborn-content reborn-home');
    $form->addInput($avatarEmail);



}

// 文章自定义字段
function themeFields($layout): void {
    $postType = new Typecho\Widget\Helper\Form\Element\Radio (
        'postType',
        array(
            'post' => _t('文章（默认）'),
            'shuoshuo'  => _t('说说')
        ),
        'post',
        _t('请选择文章类型'),
        _t('发布文章时的文章类型，默认为文章')
    );
    $layout->addItem($postType);
    $location = new Typecho\Widget\Helper\Form\Element\Text (
        'location',
        NULL,
        NULL,
        _t('位置'),
        _t('发布内容所在位置')
    );
    $layout->addItem($location);
    $thumbnail = new Typecho\Widget\Helper\Form\Element\Text (
        'thumbnail',
        NULL,
        NULL,
        _t('文章略缩图'),
        _t('首页文章略缩图，若未设置则使用默认图像')
    );
    $layout->addItem($thumbnail);
}



function themeInit($self): void {
    if ($self->request->getPathInfo() == "/reborn/api") {
        // error_log("Path matched: /reborn/api");
        // $routeType = $self->request->routeType;
        // error_log("Route type: " . $routeType);
        switch ($self->request->routeType) {
            case 'postLike':
                postLike($self);
                break;
            case 'postView':
                addPostView($self);
                break;
            case 'commentLike':
                commentLike($self);
                break;
        }
    }
}

