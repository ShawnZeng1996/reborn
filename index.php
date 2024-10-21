<?php
/**
 * Theme reborn for Typecho
 *
 * @package reborn
 * @author Shawn
 * @version 1.0.2
 * @link https://shawnzeng.com
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
            <div class="rb-site-info">
                <div class="rb-site-info-inner">
                    <?php $siteLogo = $this->options->avatarEmail ?: ''; ?>
                    <img class="rb-site-logo" alt="站点头像" src="<?php echo getGravatarUrl($siteLogo, 160); ?>" />
                    <h1 class="rb-site-title"><?php $this->options->title(); ?></h1>
                    <p class="rb-site-description"><?php $this->options->description(); ?></p>
                </div>
            </div>
        </header>
        <div id="container">
            <div class="sidebar">
                <?php $this->need('/modules/sidebar/sidebar-index.php'); ?>
            </div>
            <div class="main-content">
                <?php if($this->is('index')) {
                    $sticky_cids = getStickyPostsCids();
                    if (count($sticky_cids) > 0) {
                        $db = Typecho_Db::get();
                        $pageSize = $this->options->pageSize;
                        $select1 = $this->select()->where('type = ?', 'post');
                        $select2 = $this->select()->where('type = ? && status = ? && created < ?', 'post', 'publish', time());
                        $this->row = [];
                        $this->stack = [];
                        $this->length = 0;
                        $order = '';
                        foreach($sticky_cids as $i => $cid) {
                            if($i == 0) $select1->where('cid = ?', $cid);
                            else $select1->orWhere('cid = ?', $cid);
                            $order .= " when $cid then $i";
                            $select2->where('table.contents.cid != ?', $cid);
                        }
                        if ($order) $select1->order('', "(case cid$order end)");
                        if (($this->_currentPage || $this->currentPage) == 1) foreach($db->fetchAll($select1) as $sticky_post){
                            $this->push($sticky_post);
                        }
                        $uid = $this->user->uid;
                        if($uid) $select2->orWhere('authorId = ? && status = ?', $uid, 'private');
                        $sticky_posts = $db->fetchAll($select2->order('table.contents.created', Typecho_Db::SORT_DESC)->page($this->_currentPage, $this->parameter->pageSize));
                        foreach($sticky_posts as $sticky_post) $this->push($sticky_post);
                        $this->setTotal($this->getTotal()-count($sticky_cids));
                    }
                } else { ?>
                    <div class="page-meta">
                    <?php $this->archiveTitle(array('category' => '分类 <span>%s</span> 下的文章', 'search' => '包含关键字 <span>%s</span> 的文章', 'tag' => '标签 <span>%s</span> 下的文章', 'author' => '<span>%s</span> 发布的文章'), '', ''); ?>
                    </div>
                <?php }
                if($this->have()): ?>
                    <?php while($this->next()): ?>
                        <?php if ($this->fields->postType == 'shuoshuo'): ?>
                            <!-- 说说 -->
                            <?php $this->need('/modules/index/shuoshuo-item.php'); ?>
                        <?php else: ?>
                            <!-- 文章 -->
                            <?php $this->need('/modules/index/post-item.php'); ?>
                        <?php endif; ?>
                    <?php endwhile; ?>
                    <div class="pagination">
                        <?php $this->pageLink('点击查看更多','next'); ?>
                    </div>
                <?php else: ?>
                    <div class="page-no-content">
                        暂无内容
                    </div>
                <?php endif; ?>
            </div>
            <?php $this->need('footer.php'); ?>
        </div>
    </body>
</html>
<?php 