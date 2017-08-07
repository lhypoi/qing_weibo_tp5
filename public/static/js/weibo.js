/**
 *
 */

window.weibo = {
	type: '',
	str:"",
	tof:"",
	showHTML: function(data) {
        if (data['status'] == 1) {
			if(this.type == "short_content" || this.type == "pic_text" || this.type == "music") {
				$('.weibo_box').prepend(data['html']);
	            $('#' + this.type).val('');
	            $('#tag').val('');
	            $('.tags').eq(0).html('');
	            $('#main .weibo_content .tab-content .pic_file_see').empty();
			}else if(this.type == "video") {
				$('#video_progress').attr('style', 'width:0%');
	            $('.weibo_box').prepend(data['html']);
	            $('#' + this.type).val('');
	            $('#tag').val('');
	            $('.tags').eq(0).html('');
	            $('.progress').attr('style','display:none');
			}
        }
	},

	ajaxDo: function(fd, xhrOnProgress) {
		var text = $('#' + this.type).val();
		if($.trim(text)==""){
			$('.contentError').html("内容不能为空！");
		}else{
			fd.append('type', this.type);
			fd.append('weibo_content', text);
			$('.contentError').html("");
			if(this.type == "video") {
				$.ajax({
		            url: "/public/whome/weibo/sendWeibo",
		            type: "POST",
		            contentType: false,
		            processData: false,
		            xhr:xhrOnProgress(function(e){
		                var percent=e.loaded / e.total;//计算百分比
		                $('.progress').attr('style','display:block');
		                $('#video_progress').attr('style', 'width:'+(percent * 100)+'%');
		            }),
		            data: fd,
		            success: rtnData => {
		            	this.showHTML(rtnData);
		            }
		        });
			} else {
				$.ajax({
		            url: "/public/whome/weibo/sendWeibo",
		            type: "POST",
		            contentType: false,
		            processData: false,
		            data: fd,
		            success: rtnData => {
		            	this.showHTML(rtnData);
		            }
		        });
			}
		}
	},

    fileContent:function(tid){
        str=$.trim($('#'+tid).get(0).files[0]);
        if(str==""){
            $('.contentError').html("上传内容不能为空！");
            return false;
        }else{
            return true;
        }
    },

	submit_weibo: function(tagname_arr) {
		if (!user.haslogin()) {
            alert('请先登陆');
            return;
        }
        tof="true";
		this.type = $('.menu_box input[type=hidden]').val();
		var fd = new FormData();
		var xhr=new XMLHttpRequest();
		xhr.upload.onprogress=function(e){};
        var xhrOnProgress = function(fun) {
            xhrOnProgress.onprogress = fun; //绑定监听
            //使用闭包实现监听绑
            return function() {
                //通过$.ajaxSettings.xhr();获得XMLHttpRequest对象
                var xhr = $.ajaxSettings.xhr();
                //判断监听函数是否为函数
                if (typeof xhrOnProgress.onprogress !== 'function')
                    return xhr;
                //如果有监听函数并且xhr对象支持绑定时就把监听函数绑定上去
                if (xhrOnProgress.onprogress && xhr.upload) {
                    xhr.upload.onprogress = xhrOnProgress.onprogress;
                }
                return xhr;
            }
        };
        $.each(tagname_arr, function(key, val) {
            fd.append('tagname_arr[]', val);
        });
		if (this.type == "pic_text") {
			tof=this.fileContent("pic_file");
			fd.append('pic_file', $('#pic_file').get(0).files[0]);
		}else if (this.type == "music") {
			fd.append('music_file', /id\=([0-9]*)/.exec($('#tab3 iframe').attr('src'))[1]);
		}else if (this.type == "video") {
			tof=this.fileContent("video_file");
			fd.append('video_file', $('#video_file').get(0).files[0]);
		}
		if(tof){
			this.ajaxDo(fd,xhrOnProgress);
		}
	},
}