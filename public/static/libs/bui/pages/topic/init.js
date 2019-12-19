//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.topic = {

	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要

		if(in_pc==true){/*
			$('#btn_topic').click(function(){

				layer.open({
						type: 2,
						shadeClose: true,
						shade: 0.3,
						area: ['800px', '650px'],
						content: '',
					});
			});*/
		}else{
			router.$("#btn_topic").click(function(){
				bui.load({ 
					url: "/public/static/libs/bui/pages/topic/index.html",
					param:{
						type:$(this).data("type"),
						uid:uid,
					}
				});
				router.$(".hack_wrap").hide();	
			});
		}
	},

	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){  //init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
	},

}



//对聊天内容进行重新转义显示
format_content.topic = function(res,type){
	if(in_pc==true){
	}else{
		router.$(".chat-panel .model-list").each(function(){
			var type = $(this).data("type");
			var imgurl = $(this).data("imgurl");
			var id = $(this).data("id");			
			if(imgurl!=""){
				$(this).find(".model-more").css({"height":"60px"});				
			}else{
				$(this).find(".model-content").css({"margin-right":"2px"});
			}
			var url = "/index.php/" + type + "/content/show/id/" + id + ".html";
			var title = $(this).find(".model-title").html().substring(0,14) + "...";
			$(this).click(function(){
				bui.load({ 
					url: "/public/static/libs/bui/pages/frame/show.html",
					param:{
						url:url,
						title:title,
					}
				});
			});
		});
	}
}