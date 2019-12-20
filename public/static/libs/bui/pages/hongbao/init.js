//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.hongbao = {

	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要

		if(in_pc==true){
			$('#btn_hongbao').click(function(){
				if(uid>0){
					layer.alert('只有群聊才能发红包');
					return ;
				}
				layer.open({
						type: 2,
						shadeClose: true,
						shade: 0.3,
						area: ['800px', '650px'],
						content: '/member.php/member/plugin/execute/plugin_name/hongbao/plugin_controller/content/plugin_action/add/mid/1.html?fromtype=msg&ext_id='+(-uid),
					});
			});
		}else{
			router.$("#btn_hongbao").click(function(){
				if(uid>0){
					layer.alert("只有群聊才能发红包!");
					return ;
				}
				bui.load({ 
					url: "/public/static/libs/bui/pages/hongbao/give_hongbao.html",
					param:{
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
format_content.hongbao = function(res,type){
	if(in_pc==true){
		$(".office_text .hack-hongbao").each(function(){
			var id = $(this).data("id");
			var title = $(this).data("title");
			var str = `<a href="#" title="${title}" onclick="layer.open({type: 2,title: '${title}',shadeClose: true,shade: 0.3,area: ['600px', '600px'],content: '/index.php/p/hongbao-content-show/id/${id}.html'});"><img src="/public/static/plugins/voicehb/hongbao.png"></a>`;
			$(this).html(str);
		});
	}else{				
		//显示红包
		router.$(".chat-panel .hack-hongbao").each(function(){
			var id = $(this).data("id");
			var title = $(this).data("title");
			var str = `<div onclick="layer.open({type: 2,title: '${title}',shadeClose: true,shade: 0.3,area: ['95%', '80%'],content: '${web_url}/index.php/p/hongbao-content-show/id/${id}.html'});"><img src="/public/static/plugins/voicehb/hongbao.png"></div>`;
			$(this).html(str);
		});
	}
}