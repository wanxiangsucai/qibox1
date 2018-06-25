if(typeof(Qibo)=='undefined'){

var Qibo = function () {
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
			var title = typeof($(this).data('height'))=='undefined'?'快速操作':$(this).data('title');
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

	return {
		init:function(){
			pop();
		},
	};
}();

$(document).ready(function(){
	Qibo.init();
});

}