/**
 *
 */
$(function() {
	var record_id; //删除的微博的ID
    var record_row; //删除的微博
    var record_type;//删除微博的类型
    var a_id;  //删除评论所属的微博
    var c_id;  //删除的评论的ID
    var del_row;  //删除的评论
	$('.weibo_box').on('click', function(e) {
		var _this = $(e.target);
		//删除微博
		if(_this.hasClass('delete_weibo')) {
            record_type= _this.attr('data-type');
			record_id  = _this.attr('id');
			$('#record-num').val(record_id);
			record_row = _this;
		}
		//删除评论
    	if(_this.hasClass('delete_comment')) {
    		$('#comment-del-num').val(_this.attr('data-num'));
    		a_id = _this.siblings('input').val();
    		c_id = _this.attr('data-num');
    		del_row = _this;
    	}
	})
    //删除确认和取消
    $('#record-del-confirm').on('click', function() {

    	$.ajax({
			type: "POST",
            url: 'index.php?control=weibo&action=delete',
            data: {id: record_id, type: record_type},
            success: function(rtnData) {
            	let rtnObject = JSON.parse(rtnData);
                if(rtnObject.status == 1) {
                	alert(rtnObject.msg);
                	record_row.parent().parent().parent().parent().parent().remove();
                }else if(rtnObject.status == 0) {
                    alert(rtnObject.msg);
                }
            }
		});
    })
	$('#comment-del-confirm').on('click', function() {
    	$.ajax({
			type: "POST",
            url: 'index.php?control=comment&action=del',
            data: {comment_id: c_id, article_id: a_id, type: 'del'},
            success: function(rtnData) {
            	let rtnObject = JSON.parse(rtnData);
                if(rtnObject.status == 1) {
                	alert(rtnObject.msg);
                	del_row.parent().parent().parent().parent().parent().remove();
                }else if(rtnObject.status == 0) {
                    alert(rtnObject.msg);
                }
            }
		});
    })
})