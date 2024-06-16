$(document).ready(function() {
    $('.post-more').on('click', function() {
        var $postAction = $(this).siblings('.post-action-container').find('.post-action');
        if ($postAction.hasClass('show')) {
            $postAction.removeClass('show');
        } else {
            $('.post-action').removeClass('show'); // 关闭所有其他打开的post-action
            $postAction.addClass('show');
        }
    });
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.post-meta-2').length) {
            $('.post-action').removeClass('show');
        }
    });

    $('.post-like').on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        var cid = $this.data('cid');
        var isLiked = $this.find('.rb-heart').length > 0;
        $('.post-action').removeClass('show');
        console.log('Sending AJAX request: cid=' + cid + ', action=' + (isLiked ? 'unlike' : 'like'));

        $.ajax({
            url: themeUrl + '/like.php',
            type: 'POST',
            data: {
                cid: cid,
                action: isLiked ? 'unlike' : 'like'
            },
            success: function(data) {
                console.log('AJAX success: ', data); // 输出服务器返回的数据
                if (data.success) {
                    if (isLiked) {
                        $this.html('<span class="reborn rb-heart-o"></span>&nbsp;<span class="underline">赞</span>');
                    } else {
                        $this.html('<span class="reborn rb-heart"></span>&nbsp;<span class="underline">取消</span>');
                    }
                    $('#post-like-area-'+cid).toggleClass('hidden', data.likes == 0).html('<span class="reborn rb-heart-o"></span>&nbsp;' + data.likes + '人喜欢');
                } else {
                    alert('操作失败，请稍后再试。');
                    console.error('操作失败: ', data.message); // 输出错误信息
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('操作失败，请稍后再试。');
                console.error('AJAX error: ' + textStatus, errorThrown); // 输出错误信息
            }
        });
    });

});


