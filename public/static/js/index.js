$(function() {

    // 图片预览,绑定注册，编辑头像，发图微博
    // var fileInput = document.getElementById("register_user_pic");
    // fileInput.addEventListener('change', function(event) {
    //     var file = fileInput.files[0];
    //     var objecturl = window.URL.createObjectURL(file);
    //     $('.register_user_pic_see .col-lg-10').empty().append('<img src=' + objecturl + '>');
    // }, false);

    var fileInput2 = $("#pic_file");
    fileInput2.on('change', function(event) {
        var file = fileInput2[0].files[0];
        var objecturl = window.URL.createObjectURL(file);
        $('#main .weibo_content .tab-content .pic_file_see').empty().append('<img src=' + objecturl + '>');
    });

    //上传头像
    var fileInput3 = $("#edit_pic");
    fileInput3.on('change', function(event) {
        var file = fileInput3[0].files[0];
        var objecturl = window.URL.createObjectURL(file);
        $('.edit_pic_see').empty().append(`<img src="${objecturl}" style="width:200px;">`);
    });

    // 切换菜单栏改变隐藏的值,微博类型
    $('.menu_box .menu_tab').click(function() {
        if ($(this).index() == 0) {
            $('#type').val("short_content");
        } else if ($(this).index() == 1) {
            $('#type').val("pic_text");
        } else if ($(this).index() == 2) {
            $('#type').val("music");
        } else if ($(this).index() == 3) {
            $('#type').val("video");
        } else if ($(this).index() == 4) {
            location.href = 'index.php?control=longcontent&action=index';
        }
    });


    //发布微博
    $('.menu_box input[type=button]').click(function() {
    	weibo.submit_weibo(tagname_arr);
    	tagname_arr = [];
        $('#tag').val('');
        $('.tags').eq(0).html('');
    })
    
    // 事件委托绑定评论下拉框，评论增删功能，编辑微博
    $('.weibo_list').click(function(event) {
    	//event.preventDefault();
        let this_elm = $(event.target);
        // 评论下拉框
        if (this_elm.hasClass('commet_btn')) {
        	comments.getComment(this_elm);
            return false;
        } else if (this_elm.hasClass('commet_send')) {
            // 评论发送
            comments.addComment(this_elm);
            return false;
        } else if (this_elm.hasClass('edit_weibo')) {
            comments.edit(this_elm);
        } else if (this_elm.hasClass('more')) { //异步加载评论
            comments.load(this_elm);
            return false;
        }
    })

    // 管理本站按钮跳转
    $('#manage_server').click(function () {
        console.log(1);
        location.href = $(this).attr('href');
    });

    // 事件委托处理音乐搜索结果
    $('.search_list').click(function(event) {
        let this_elm = $(event.target);
        let music_id = $(this_elm).attr('data-id');
        if (music_id) {
            $('#tab3').prepend(`
                <iframe frameborder="no" border="0" marginwidth="0" marginheight="0" width=330 height=86 src="//music.163.com/outchain/player?type=2&id=${music_id}&auto=0&height=66"></iframe>
            	<span class="glyphicon glyphicon-remove-circle"></span>
                `);
            $('.search_list').empty();
            $('#tab3 input').val('');
            $('#tab3 input').hide();
        }
    });
    $('#tab3').on('click', '.glyphicon', function() {
    	$('#tab3 iframe').remove();
    	$('#tab3 input').css('display', 'block');
    })

    //头像滑过
    var infoTarget = null;
    $('.weibo_box').on("mouseenter", 'img.w_img', function(e) {
        infoTarget = $(e.target);
        let touxiang_box = infoTarget.parent().parent().find('.info-box');
        $user_id = infoTarget.parent().find('.w_img').attr('data-id');
        // console.log($user_id);
        touxiang_box.show(600);
        $.ajax({
            url: "/public/whome/weibo/headSelect",
            type: "POST",
            data: {
                user_id:$user_id
            },
            success: function(data) {

                let p_html = ""
                data.forEach(item => {
                    p_html += "<li class='photo_weibo'>" + item.weibo_content + "&nbsp;&nbsp;" + item.create_time + "</li>";

                })
                $('.road_list').html(p_html);
            }
        });
    }).on("mouseleave", '.head_box', function() {
        if (infoTarget != null) {
            infoTarget.parent().parent().find('.info-box').hide(300);
        }
    });

    //滑过标签显示
    var timer_enter = 0;
    var timer_leave = 0;
    $('.weibo_box').on("mouseenter",'.tag', function(e) {
    	timer_enter = setTimeout(function(){
    		infoTarget = $(e.target);
            let touxiang_box=infoTarget.siblings('.tag_info_box');
            timer_enter = setTimeout(function() {touxiang_box.show(500).css("left",infoTarget.offset().left-385)},500);
            var tag_id = infoTarget.attr('data-id');
            $.ajax({
    			url: "/public/whome/tag/tagSelect",
    			type: "POST",
    			data: {
    			     tag_id
    			 },
    			 success: function(rdata) {
    			     //data = $.parseJSON(data);
    			     let p_html = ""
    				 rdata.forEach(item=>{
                     //{:url('whome/tag/show',array('id'=>tag_id))}
    				     p_html+= "<a href='/public/whome/tag/show/id/"+tag_id+"'><li style='overflow:hidden;'>"+item.weibo_content+"</li></a>";
    				 })
    				 infoTarget.siblings('.tag_info_box').find('.road_tag').html(p_html);
    			 }
            });
    	},400);

    }).on("mouseleave",'.tags_box', function() {
        clearTimeout(timer_enter);
        timer_leave = setTimeout(function() {   infoTarget.siblings('.tag_info_box').hide(500)}, 400);
    }).on("mouseenter",'.tag_info_box', function() {
    	clearTimeout(timer_leave);
    })
    //进入标签显示
    // $(".tag_info_box").on("mouseenter",function  () {
    //     $(".tag_info_box").show()
    // }).on("mouseleave",function  () {
    //     $(".tag_info_box").hide()
    // })

    //判断是否是登陆状态
    if (user.haslogin()) {
        $.ajax({
            type: "POST",
            url: "/public/whome/user/check_local",
            data: {
                id: localStorage.getItem('uid')
            },
            success: function(data) {
                if (data['status'] == 1) {
                    $('#accountmenu').html(data['html']);
                }
            }
        });
    }

    //添加标签
    var tagname_arr = [];
    $('#tag').on('keydown', function(e) {
    	if(e.keyCode == 13) {
    		if($(this).val() == '') {
    			$(this).siblings('.warning').css('display','block').html('标签不能为空');
    		}else {
    			if(tagname_arr.length >= 5) {
        			$(this).siblings('.warning').css('display','block');
        		}else{
        			$(this).siblings('.warning').css('display','none');
    	    		tagname_arr.push($(this).val());
    	    		var _html = '';
    	    		for(var i = 0; i < tagname_arr.length; i ++) {
    	    			_html += '<span>#'+tagname_arr[i]+'</span> ';
    	    		}
    	    		$(this).siblings('.tags').html(_html);
    	    		$(this).val('');
        		}
    		}
    	}else if(e.keyCode == 8) {
    		if($(this).val() == '') {
    			tagname_arr.splice(tagname_arr.length-1, 1);
    			var _html = '';
	    		for(var i = 0; i < tagname_arr.length; i ++) {
	    			_html += '#'+tagname_arr[i]+' ';
	    		}
	    		$(this).siblings('.tags').html(_html);
    		}
    		$(this).siblings('.warning').css('display','none').html('最多添加5个标签');
    		$(this).val('');
    	}
    })
    $('.tags').on('click', 'span', function(e) {
    	var this_elm = $(e.target);
    	var _this = this_elm.html().replace('#','');
    	var _index = $.inArray(_this, tagname_arr);
    	tagname_arr.splice(_index, 1);
    	this_elm.remove();
    })
    
    //修改个人信息
    $('#info-form').on('click', function(e) {
    	e.preventDefault();
    	var _this = $(e.target).parent().siblings();
    	user.change_info(_this);
    })
})



// 搜索音乐lll
function search_music(this_elm) {
    if ($(this_elm).val().trim().length != 0) {
        $.getJSON("http://s.music.163.com/search/get/?version=1&src=lofter&type=1&filterDj=false&s=" + $(this_elm).val() + "&limit=8&offset=0&callback=?", function(rtnData) {
            console.log(rtnData)
            if (rtnData.result) {
                let li_html = ''
                rtnData.result.songs.forEach(item => {
                    li_html += "<li data-id='" + item.id + "'>" + item.name + "-" + item.artists[0].name + "</li>"
                })
                $(".search_list").html(li_html);
            } else {
                $(".search_list").html('');
            }

        })
    } else {
        $(".search_list").html('');
    }
}

// 验证码改变
function changeCaptcha (this_elm) {
    $(this_elm).attr('src', '/public/index/home/getCaptcha?' + Math.random());
}