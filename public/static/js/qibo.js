if(typeof(Qibo)=='undefined'){

var Qibo = function () {
	//超级链接那里加上 class="_pop" 就可以实现弹窗, 设置 data-width="600" data-height="600" 就可以指定弹窗大小 , 设置  data-title="标题XXX" 就可以设置弹窗标题
	var pop = function(){
		jQuery(document).delegate('a._pop', 'click', function () {
			if((navigator.userAgent.match(/(iPhone|iPod|Android|ios|iPad)/i))){
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