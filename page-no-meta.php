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
        <div class="main-content" style="width: 100%;">
            <?php $this->need('/modules/content/post-no-meta.php'); ?>
        </div>
        <?php $this->need('footer.php'); ?>
    </div>
    </body>
    </html>
<?php