<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<section class="widget admin-card">
    <div class="admin-info flex">
        <img src="<?php echo getGravatarUrl($this->authorId ? $this->author->mail : $this->user->mail, 160); ?>" alt="<?php $this->authorId ? $this->author->screenName() : $this->user->screenName(); ?>">
        <div class="flex-1">
            <div class="admin-name">
                <?php $this->authorId ? $this->author->screenName() : $this->user->screenName(); ?>
                <?php if($this->options->adminGender) {
                    if ($this->options->adminGender === 'female') {
                        echo '<i class="reborn rb-female"></i>';
                    } else {
                        echo '<i class="reborn rb-male"></i>';
                    }
                } ?>
            </div>
            <?php $stats = getAuthorPostStats(); ?>
            <div class="admin-post-stats">文章：<?php echo $stats['numPosts']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;阅读量：<?php echo $stats['totalViews']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;获赞数：<?php echo $stats['totalLikes']; ?></div>
            <div class="admin-location">地区：<?php if ($this->options->adminLocation) echo $this->options->adminLocation; ?></div>
        </div>
    </div>
    <?php
    $this->widget('Widget_Metas_Category_List')->to($categories);
    if ($categories->have()):
        echo '<div class="admin-meta admin-tags">';
        echo '<span class="meta-name">文章分类</span>';
        echo '<span class="meta-value">';
        while ($categories->next()):
            if ( $categories->levels === 0 ):
                echo '<a class="post-category" href="' . $categories->permalink . '" title="' . $categories->name . '" target="_self">' . $categories->name . '(' . $categories->count . ')</a>';
            endif;
        endwhile;
        echo '</span></div>';
    endif;


    if($this->options->adminTags) {
        echo '<div class="admin-meta admin-tags">';
        echo '<span class="meta-name">个人标签</span>';
        echo '<span class="meta-value">' . $this->options->adminTags . '</span>';
        echo '</div>';
    }
    if ($this->options->adminRecentPlay):
        // 将配置项按行分割
        $games = explode("\n", $this->options->adminRecentPlay);
        echo '<div class="admin-meta admin-tags">';
        echo '<span class="meta-name">最近在玩</span>';
        echo '<span class="meta-value">';
        // 输出 HTML 结构
        if (!empty($games)) {
            echo '<ul class="widget-list game-list">';
            foreach ($games as $game) {
                // 按 '|' 分割每一行
                $gameDetails = explode('|', $game);
                // 确保游戏名、链接、图片链接都存在
                if (count($gameDetails) === 3) {
                    $gameName = trim($gameDetails[0]);
                    $gameLink = trim($gameDetails[1]);
                    $gameImage = trim($gameDetails[2]);
                    // 输出每个游戏的 HTML 结构
                    echo '<li class="game-item">';
                    echo '<a href="' . htmlspecialchars($gameLink) . '" class="game-link" title="' . htmlspecialchars($gameName) . '">';
                    echo '<img src="' . htmlspecialchars($gameImage) . '" alt="' . htmlspecialchars($gameName) . '" />';
                    echo '</a>';
                    echo '</li>';
                }
            }
        }
        echo '</span></div>';
    endif;
    ?>

</section>