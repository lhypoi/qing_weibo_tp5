window.comments={
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
        this_elm.closest("li").find('.commont_box').slideToggle();
	},
	
	addComment:function(this_elm){
		let weibo_id = this_elm.closest("li").attr('weibo-id');
        if (!user.haslogin()) {
            alert('请先登陆');
        }else{
	        $.ajax({
	            url: "/public/whome/commont/addComment",
	            type: "POST",
	            data: {
	                commet_content: this_elm.parent().prev().find('input').val(),
	                weibo_id
	            },
	            success: function(data) {
	                if (data['status'] == 1) {
	                    $('li[weibo-id=' + weibo_id + '] .commont_list').eq(0).prepend(data['html']);
	                    this_elm.parent().prev().find('input').val('');
	                }
	            }
	        });
        }
	},
	
	edit:function(this_elm){
		$('#edit_weibo_modal textarea').val(this_elm.parent().parent().prev().text().trim());
        $('#edit_weibo_modal input[type=hidden]').val(this_elm.closest('li').attr('weibo-id'));
	},
	
	load:function(this_elm){
		var comment = parseInt(this_elm.attr('data-page')) + 1;
        var commentList = comment * 5;
        var article_id = this_elm.attr('data-id');
        $.ajax({
            type: "POST",
            url: "/public/whome/commont/getComment",
            data: {
                article_id,
                commentList,
                page: comment
            },
            success: function(data) {
                if (data['status'] == 1) {
                    this_elm.parent().parent().append(data['html']);
                    this_elm.parent().remove();
                }
            }
        });
	}
}