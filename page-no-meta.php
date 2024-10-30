<?php
/**
 * 无头尾 meta 信息页面模板
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
    </header>
    <div id="container">
        <div class="sidebar">
            <?php $this->need('/modules/sidebar/sidebar-post.php'); ?>
        </div>
        <div class="main-content">
            <?php $this->need('/modules/content/post-no-meta.php'); ?>
        </div>
        <?php $this->need('footer.php'); ?>
    </div>
    </body>
    </html>
<?php