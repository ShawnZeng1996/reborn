<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
       </header>
        <div id="container">
            <div class="sidebar">
                <?php $this->need('/modules/sidebar/sidebar-post.php'); ?>
            </div>
            <div class="main-content">
                <?php if ($this->fields->postType == 'shuoshuo'): ?>
                    <!-- 说说 -->
                    <?php $this->need('/modules/content/shuoshuo.php'); ?>
                <?php else: ?>
                    <!-- 文章 -->
                    <?php $this->need('/modules/content/post.php'); ?>
                <?php endif; ?>
            </div>
            <?php $this->need('footer.php'); ?>
        </div>
    </body>
</html>
<?php
