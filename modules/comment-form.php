<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<div class="comment-form" data-cid="<?php echo $this->cid; ?>" data-coid="">
    <div class="flex comment-meta">
        <?php if($this->user->hasLogin()) { ?>
            <div><span class="color-link"><?php $this->user->screenName(); ?></span>&nbsp;已登录</div>
            <input placeholder="昵称" type="text" class="comment-input comment-input-author hidden" name="comment-author" value="<?php $this->user->screenName(); ?>"/>
            <input placeholder="邮箱" type="text" class="comment-input comment-input-email hidden" name="comment-email" value="<?php $this->user->mail(); ?>"/>
            <input placeholder="网址" type="text" class="comment-input comment-input-url hidden" name="comment-url" value="<?php $this->user->url(); ?>" />
        <?php } else { ?>
            <input placeholder="昵称" type="text" class="comment-input comment-input-author" name="comment-author" value="<?php $this->remember('author'); ?>"/>
            <input placeholder="邮箱" type="text" class="comment-input comment-input-email" name="comment-email" value="<?php $this->remember('mail'); ?>"/>
            <input placeholder="网址" type="text" class="comment-input comment-input-url" name="comment-url" value="<?php $this->remember('url'); ?>" />
        <?php } ?>
    </div>
    <div class="comment-area">
        <textarea placeholder="回复内容" class="comment-textarea comment-input-text" name="comment-text"></textarea>
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
            <button class="comment-cancel" data-cid="<?php echo $this->cid; ?>">取消</button>
            <button class="comment-btn underline" data-cid="<?php echo $this->cid; ?>" data-coid="">回复</button>
        </div>
    </div>
</div>