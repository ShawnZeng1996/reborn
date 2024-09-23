<?php
// 最近在玩
if ($this->options->sidebarRecentPlay):
    // 将配置项按行分割
    $games = explode("\n", $this->options->sidebarRecentPlay);
    // 输出 HTML 结构
    if (!empty($games)) {
        echo '<section class="widget"><h3 class="widget-title">最近在玩</h3>';
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
        echo '</ul></section>';
    }
endif;