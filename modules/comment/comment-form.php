<?php $this->comments()->to($comments);?>
<?php if($this->allow('comment')): ?>
<div id="<?php $this->respondId();?>" class="respond-form">
    <form method="post" action="<?php $this->commentUrl() ?>" class="comment-form" role="form" data-cid="<?php echo $this->cid; ?>">
        <div class="flex comment-meta">
            <?php if($this->user->hasLogin()) { ?>
                <div><img class="avatar" src="<?php echo getGravatarUrl($this->user->mail)?>" alt="<?php $this->user->screenName(); ?>"><span class="user"><?php $this->user->screenName(); ?></span>&nbsp;已登录</div>
            <?php } else { ?>
                <?php
                $email = $this->remember('mail', true);
                // 默认头像列表
                $defaultAvatars = [
                    '/assets/img/欢乐马.jpg',
                    '/assets/img/神经蛙.jpg',
                    '/assets/img/阿白.jpg',
                    '/assets/img/momo.jpg',
                    '/assets/img/哄哄.jpg'
                ];
                // 随机选择一个默认头像
                $defaultAvatarUrl = \Utils\Helper::options()->themeUrl . $defaultAvatars[array_rand($defaultAvatars)];
                $avatarUrl = !empty($email)
                    ? getGravatarUrl($email)
                    : $defaultAvatarUrl;
                ?>
                <img class="avatar" src="<?php echo $avatarUrl; ?>" alt="头像">
                <div class="comment-input-area flex flex-1">
                    <input placeholder="昵称" type="text" class="comment-input comment-input-author" name="author" value="<?php $this->remember('author'); ?>"/>
                    <input placeholder="邮箱" type="text" class="comment-input comment-input-mail" name="mail" value="<?php $this->remember('mail'); ?>" <?php if ($this->options->commentsRequireMail): ?> required<?php endif;?> />
                    <input placeholder="网址" type="text" class="comment-input comment-input-url" name="url" value="<?php $this->remember('url'); ?>" <?php if ($this->options->commentsRequireURL): ?> required pattern="\S+.*"<?php endif;?> />
                </div>
            <?php } ?>
        </div>
        <div class="comment-area">
            <textarea placeholder="回复内容" class="comment-textarea comment-input-text" name="text" required></textarea>
        </div>
        <div class="comment-footer relative flex">
            <span class="reborn rb-smile comment-emoji" id="toggle-emoji-picker"></span>
            <div class="emoji-container absolute">
                <div class="emoji-list"></div>
                <div class="emoji-bar flex">
                    <div class="emoji-category active" data-category="wechat">微信</div>
                    <div class="emoji-category" data-category="xiaodianshi">小电视</div>
                </div>
            </div>
            <div class="comment-footer-btn">
                <a class="comment-cancel underline" data-cid="<?php echo $this->cid; ?>">取消</a>
                <button class="comment-btn underline submit" type="submit">回复</button>
                <?php if ($this->options->commentsAntiSpam){
                    $security = $this->widget('Widget_Security'); ?>
                    <input type="hidden" name="_" value="<?php echo $security->getToken($this->request->getReferer()) ?>">
                <?php } ?>
                <input type="hidden" name="parent" id="comment-parent" value="0">
            </div>
        </div>
    </form>
</div>
<?php endif; ?>
