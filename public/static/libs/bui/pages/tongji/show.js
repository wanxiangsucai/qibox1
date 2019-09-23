/**
 * 聊天对话模板
 * 默认模块名: pages/chat/chat
 * @return {[object]}  [ 返回一个对象 ]
 */
loader.define(function(require,exports,module) {

    var pageview = {};
	var tj_type;
	var msg_scroll = true;
	var show_msg_page  = 1;
	

    // 模块初始化定义
    pageview.init = function () {
        this.bind();
		var that = $("#tongji_win");
		that.parent().scroll(function () {
			var h = that.height()-that.parent().height()-that.parent().scrollTop();
			//console.log(h);
			if( h<300 && msg_scroll==true){
				get_tongji_msg(tj_type);
			}
		})
		console.log(chat_timer);
    }

    pageview.bind = function () {            
    }
	
	//加载统计动态的详细内容数据
	function get_tongji_msg(type){
		if(show_msg_page==1){
			$.get(tongjiCountUrl+"?set_read=1&type="+type,function(res){});//把新数据标志为已读
			layer.msg("数据加载中,请稍候...");
		}
		msg_scroll = false;
		$.get(tongjiMsgUrl + show_msg_page + "&type="+type,function(res){
			if(res.code==0){
				layer.closeAll();
				var that = $('#tongji_win');
				if(res.data==''){
					if(show_msg_page==1){
						//that.parent().scrollTop(0)
						layer.msg("没有记录！",{time:1000});
					}else{
						layer.msg("已经显示完了！",{time:500});
					}		
				}else{
					if(show_msg_page==1){
						that.html(res.data);
						//that.parent().scrollTop(1000)
					}else{
						that.append(res.data);
						//that.parent().scrollTop(50);
					}     
					show_msg_page++;
					msg_scroll = true;
				}
			}
		});
	}
		



	var getParams = bui.getPageParams();
    getParams.done(function(result){
		tj_type = result.type;
        //console.log(uid);
		show_msg_page = 1;			//重新恢复第一页
		msg_scroll = true;			//恢复可以使用滚动条
		get_tongji_msg(tj_type);	//加载相应用户的记录
    })
	

    // 初始化
    pageview.init();

    // 输出模块
    return pageview;
})
