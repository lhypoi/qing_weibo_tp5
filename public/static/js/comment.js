window.comment={
	// 获取评论列表
	//this_elm:'',
	getComment: function(this_elm) {
		var comment_box = this_elm.parent().parent().parent().parent().siblings('.comment_row').find('.commont_box');
        if (comment_box.css('display') != 'block') {
            var article_id = this_elm.attr('data-num');
            $.ajax({
                type: "POST",
                url: '/public/whome/commont/getComment',
                data: {
                    article_id: article_id,
                    commentList: null
                },
                success: rtnData=> {
                    comment_box.find('.commont_list').html(rtnData.html);
                }
            });
        }
        $(this_elm).closest("li").find('.commont_box').slideToggle();
	},
	
	addComment:function(){
		let weibo_id = $(this_elm).closest("li").attr('weibo-id');
		// 这里要加用户对象的判断
        if (!haslogin()) {
            alert('请先登陆');
        };
        $.ajax({
            url: "index.php?control=comment&action=add",
            type: "POST",
            data: {
                commet_content: $(this_elm).parent().prev().find('input').val(),
                weibo_id
            },
            success: data=> {
                data = $.parseJSON(data);
                if (data['status'] == 1) {
                    $('li[weibo-id=' + weibo_id + '] .commont_list').eq(0).prepend(data['html']);
                    $(this_elm).parent().prev().find('input').val('');
                }
            }
        });
        return false;
	},
	
	edit:function(){
		$('#edit_weibo_modal textarea').val($(this_elm).parent().parent().prev().text().trim());
        $('#edit_weibo_modal input[type=hidden]').val($(this_elm).closest('li').attr('weibo-id'));
	},
	
	load:function(){
		var comment = this_elm.attr('data-page');
        var commentList = 0;
        commentList = comment * 5;
        var article_id = this_elm.attr('data-id');
        $.ajax({
            type: "POST",
            url: "/public/whome/commont/getComment",
            data: {
                article_id,
                commentList,
                comment
            },
            success: data=> {
                if (data['status'] == 1) {
                    this_elm.parent().parent().append(data['html']);
                    this_elm.parent().remove();
                }
            }
        });
        return false;
	}
}