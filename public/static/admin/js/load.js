/**
 * 
 */
$(function() {
	//异步加载列表
    var page = 1;
    var pageList = 0;
    var lock = true;
//    var lock_photo = true;
//    var photoList = 0;
//    var photo_page = 1;
    $(window).scroll(function() {
    	if(typeof($('#menu').val()) == 'undefined' || $('#menu').val() == '0') {
	    	if($(window).scrollTop() == $(document).height() - $(window).height()) {
	    		if(user.haslogin() && lock == true) {
	    			var page_mark = $('#page-mark').attr('data-page');
	    			var id = getUrlParam('id');
	    			pageList = page * 10;
	    			$.ajax({
	    	    		type: "POST",
	    	    		url: "index.php?control=weibo&action=get",
	    	    		data: {pageList, page_mark, id},
	    	    		success: function(data) {
	    	    			data = $.parseJSON(data);
	    	                if (data['status'] == 1) {
	    	                    $('.weibo_box').eq(0).append(data['html']);
	    	                    lock = true;
	    	                } else {
	    	                	lock = false;
	    	                }
	    	    		}
	    	    	});
	    			page += 1;
	    		}
	    	}
    	}else if($('#menu').val() == '1') {
    		if($(window).scrollTop() == $(document).height() - $(window).height()) {
    			if(user.lock_photo == true) {
    				var page_mark = $('#page-mark').attr('data-page');
	    			user.photoList = user.photo_page * 9;
	    			$.ajax({
	    	    		type: "POST",
	    	    		url: "index.php?control=user&action=getPhoto",
	    	    		data: {photoList: user.photoList},
	    	    		success: function(data) {
	    	    			data = $.parseJSON(data);
	    	                if (data['status'] == 1) {
	    	                    $('.photo_list').eq(0).append(data['html']);
	    	                    user.lock_photo = true;
	    	                } else {
	    	                	user.lock_photo = false;
	    	                }
	    	    		}
	    	    	});
	    			user.photo_page += 1;
    			}
    		}
    	}
    })
})

function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return null;
}