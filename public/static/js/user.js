/**
 *
 */

window.user = {
	photoList: 0,
	lock_photo: true,
	photo_page: 1,

	// 判断是否登录
	haslogin: function() {
	    if (localStorage.getItem('uid') > 0) {
	        return true;
	    } else {
	        return false;
	    }
	},
	//登录注册退出修改信息
	do_register: function() {
	    var fd = new FormData();
	    fd.append('user_nickname', $('#register_user_nickname').val());
	    fd.append('user_name', $('#register_user_name').val());
	    fd.append('user_pwd', $('#register_user_pwd').val());
	    fd.append('user_repwd', $('#register_user_repwd').val());
	    $.ajax({
	        url: "whome/user/reg",
	        type: "POST",
	        contentType: false,
	        processData: false,
	        data: fd,
	        success: function(data) {
	            if (data['status'] == 1) {
	                localStorage.setItem("uid", data.id);
	                location.reload();
	            } else {
	                alert(data['msg']);
	            }

	        }
	    });
	},

	do_quit :function() {
	    $.ajax({
	        url: "/public/whome/user/logout",
	        type: "POST",
	        success: function(data) {
	            if (data['status'] == 1) {
	                localStorage.removeItem('uid');
	                location.reload();
	            }
	        }
	    });
	},

	do_login: function() {

		var promise = new Promise(function (resolve, reject) {
			$.ajax({
				url: "/public/index/home/checkCaptcha",
				type: "POST",
				data: {captcha: $('input[name=captcha]').val()},
				success: function (data) {
					if (data.status == 1) {
						resolve();
					} else {
						reject(data.msg);
					}
				}
			})
		});

		promise.then(function () {
		    $.ajax({
		        url: "/public/whome/user/log",
		        type: "POST",
		        data: {
		            user_name: $('#login_user_name').val(),
		            user_pwd: $('#login_user_pwd').val(),
		            type: 'login'
		        },
		        success: function(data) {
		            if (data['status'] == 1) {
		                localStorage.setItem("uid", data.info.id);
		                location.reload();
		            } else {
		                alert(data['msg']);
						$('#img_captcha').attr('src', '/public/index/home/getCaptcha?' + Math.random());
		            }
		        }
		    });
		}, function (value) {
			alert(value);
			$('#img_captcha').attr('src', '/public/index/home/getCaptcha?' + Math.random());
		})

	},

	do_edit: function() {
	    var fd = new FormData();
	    fd.append('uid', localStorage.getItem('uid'));
	    fd.append('user_pic', $('#edit_pic').get(0).files[0]);
	    fd.append('type', 'edit');
	    $.ajax({
	        url: "/public/whome/user/edit",
	        type: "POST",
	        contentType: false,
	        processData: false,
	        data: fd,
	        success: function(data) {
	            if (data['status'] == 1) {
	                location.reload();
	            }
	        }
	    });
	},

	// 编辑文本类微博
	do_edit_weibo: function() {
	    $.ajax({
	        url: "index.php?control=weibo&action=editWeibo",
	        type: "POST",
	        data: {
	            weibo_content: $('#edit_weibo_modal textarea').val(),
	            id: $('#edit_weibo_modal input[type=hidden]').val(),
	            type: 'edit'
	        },
	        success: function(data) {
	            data = $.parseJSON(data);
	            if (data['status'] == 1) {
	                alert(data['msg']);
	                location.reload();
	            } else {
	                alert(data['msg']);
	            }
	        }
	    });
	},

	//用户主页菜单选择
	menu_select: function(index) {
		var menu = $('.menu ul li');
		var list = $('.weibo_list .row .col-lg-9');
		for(var i = 0; i < menu.length; i ++) {
			if(menu.eq(i).children('span').hasClass('active') && i != index) {
				menu.eq(i).children('span').removeClass('active');
				list.eq(i).hide(500);
			}
		}
		if(index == 2) {
			$('#page-mark').attr('data-page','');
			$.ajax({
				url: "whome/user/getPhoto",
		        type: "POST",
		        success: function(data) {
		            if (data['status'] == 1) {
		                $('.info').html(data['html']);
		            }
		        }
			});
			this.photoList = 0;
			this.lock_photo = true;
			this.photo_page = 1;
		}else if(index == 1) {
			$('#page-mark').attr('data-page','photo');
			$.ajax({
				url: "/public/whome/user/getPhoto",
		        type: "POST",
		        data: {photoList: 0},
		        success: function(data) {
		            data = $.parseJSON(data);
		            if (data['status'] == 1) {
		                $('.photo_list').html(data['html']);
		            }
		        }
			});
			this.photoList = 0;
			this.lock_photo = true;
			this.photo_page = 1;
		}else if(index == 0) {
			$('#page-mark').attr('data-page','home');
			this.photoList = 0;
			this.lock_photo = true;
			this.photo_page = 1;
		}
		menu.eq(index).children('span').addClass('active');
		list.eq(index).show(500);
		$('#menu').val(index);
	},
	
	//修改个人信息
	change_info: function(_this) {
		var _nickname = _this.eq(0).children('.form-box').children('input');
		var _pwd = _this.eq(1).children('.form-box').children('input');
		var _pwd_confirm = _this.eq(2).children('.form-box').children('input');
		var nickname = _nickname.val();
    	var pwd = _pwd.val();
    	var pwd_confirm = _pwd_confirm.val();
    	var info = _this.eq(3).children('.form-box').children('textarea').val();
    	var send = true;
		if(nickname == '') {
			_nickname.next('b').css('color', '#f00');
			send = false;
		}else{
			_nickname.next('b').css('color', '#fff');
			send = true;
		}
		if(pwd == '') {
			_pwd.next('b').css('color', '#f00');
			send = false;
		}else{
			_pwd.next('b').css('color', '#fff');
			send = true;
		}
		if(pwd != pwd_confirm) {
			_pwd_confirm.next('b').css('color', '#f00');
			send = false;
		}else{
			_pwd_confirm.next('b').css('color', '#fff');
			send = true;
		}
		if(send) {
			$.ajax({
				url: "index.php?control=user&action=changeInfo",
		        type: "POST",
		        data: {
		        	nickname, 
		        	pwd, 
		        	info
		        },
		        success: function(data) {
		            data = $.parseJSON(data);
		            if (data['status'] == 1) {
		                alert(data['msg']);
		                location.reload();
		            }
		        }
			})
		}
	}
}