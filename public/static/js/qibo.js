if(typeof(Qibo)=='undefined'){

var Qibo = function () {
	//超级链接那里加上 class="_pop" 就可以实现弹窗, 设置 data-width="600" data-height="600" 就可以指定弹窗大小 , 设置  data-title="标题XXX" 就可以设置弹窗标题
	var pop = function(){
		jQuery(document).delegate('a._pop', 'click', function () {
			if((navigator.userAgent.match(/(iPhone|iPod|Android|ios|iPad)/i))||$("body").width()<1000){
				var default_width = "95%";
				var default_height = "90%";
			}else{
				var default_width = "1000px";
				var default_height = "650px";
			}
			var width = typeof($(this).data('width'))=='undefined'?default_width:$(this).data('width');
			var height = typeof($(this).data('height'))=='undefined'?default_height:$(this).data('height');
			var title = typeof($(this).data('title'))=='undefined'?'快速操作':$(this).data('title');
			layer.open({
			  type: 2,
			  title: title,
			  shade: [0.3,'#333'], 
			  area: [width, height],
			  anim: 1,
			  content: $(this).attr("href"),
			  end: function(){ //关闭事件	
			  }
			});
			return false;
		});
	}
	
	//超级链接那里加上  class="alert" 就可以实现弹窗确认. 设置 data-alert="你确认要修改吗?"  就可以指定提示语,这个参数也可以不设置.
	var _confirm = function(){
		jQuery(document).delegate('a.alert', 'click', function () {
			var url = $(this).attr("href");
			var msg = typeof($(this).data('alert'))=='undefined'?'你确认要删除吗?':$(this).data("alert");
			var title = typeof($(this).data('title'))=='undefined'?'提示':$(this).data('title');
			layer.confirm(msg, {title:title, btn : [ '确定', '取消' ]}, function(index) {
				window.location.href = url;
			});
			return false;
		});
	}
	
	//配合下面这个方法 _ajaxget 使用
	var _ajaxgoto = function(url,id){
		var index = layer.msg('请稍候...');
		$.get(url,function(res){
			layer.close(index);
			if(res.code==1){	//成功提示
				layer.msg(res.msg);
				setTimeout(function(){
					if(res.url!=''){
						window.location.href = res.url;
					}else{
						window.location.reload();
					}						
				},500);
			}else{	//错误提示
				if(res.url!=''){
					layer.confirm(res.msg, {title:'提示', btn : [ '确定', '取消' ]}, function(index) {
						window.location.href = res.url;
					});
				}else{
					layer.open({title: '提示!',content:res.msg});
					if(typeof(ajax_get)=='function'){
						ajax_get(res,id);		//页面里定义的函数
					}
				}
			}
		});
	}
	
	//超级链接那里加上  class="ajaxget" 就可以实现ajax访问. 设置 data-alert="你确认这么做吗?"  就可以指定提示语,这个参数也可以不设置.
	var _ajaxget = function(){
		
		jQuery(document).delegate('a.ajax_get', 'click', function () {
			var url = $(this).attr("href");
			var msg = $(this).data("alert");
			var id = $(this).data("id");
			if(typeof(msg)!='undefined'){
				layer.confirm(msg, {title:'提醒', btn : [ '确定', '取消' ]}, function(index) {
					_ajaxgoto(url,id);
				});
			}else{
				_ajaxgoto(url,id);
			}
			return false;
		});
	}
	
	//直接使用window.history.go(-1) window.history.back() 遇到新开的页面,就导致无法返回, 用这个函数可以给他默认指定一个返回页面
	var goBack = function(url) {
		if ((navigator.userAgent.indexOf('MSIE') >= 0) && (navigator.userAgent.indexOf('Opera') < 0)) { // IE 
			if (history.length > 0) {
				window.history.go(-1);
			} else {
				window.location.href = url;
				//window.opener = null;
				//window.close();
			}
		} else { //非IE浏览器 
			if (navigator.userAgent.indexOf('Firefox') >= 0 || navigator.userAgent.indexOf('Opera') >= 0 || navigator.userAgent.indexOf('Safari') >= 0 || navigator.userAgent.indexOf('Chrome') >= 0 || navigator.userAgent.indexOf('WebKit') >= 0) {

				if (window.history.length > 1) {
					window.history.go(-1);
				} else {
					window.location.href = url;
					//window.opener = null;
					//window.close();
				}
			} else { //未知的浏览器 
				window.history.go(-1);
			}
		}
	}

	return {
			init:function(){
				pop();
				_confirm();
				_ajaxget();
			},
			goBack:function(url){
				goBack(url);
			},
	};
}();

$(document).ready(function(){
	Qibo.init();
});

}