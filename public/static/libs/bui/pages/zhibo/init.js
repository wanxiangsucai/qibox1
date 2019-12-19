//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.zhibo = {
	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){
		//this.check_play(res);
	},
	once:function(){
	},
	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		var show_str  = `
			<div class="live_video_warp">
			<div class="codeimg"><img src="" onerror="this.src='http://www.qibosoft.com/images/showad/h_wei.png'"><br>手机扫码推流</div>
			推流地址：<input class="push_url" type="text"><br>
			播流地址m3u8(PC/WAP/APP都能播放)：<input class="m3u8_url" type="text"><br>
			播流地址FLV(只能PC播放)：<input class="flv_url" type="text"><br>	
			播流地址rtmp(只能PC/APP能播放)：<input class="rtmp_url" type="text"><br>
			</div>
		`;

		if(!in_pc){
			show_str  = `
			<div class="live_video_warp">
			<div class="codeimg"><img src="" onerror="this.src='http://www.qibosoft.com/images/showad/h_wei.png'"><br>其它手机扫码推流</div>
			推流地址：<input class="push_url" type="text"><br>
			播流地址：<input class="m3u8_url" type="text"><br>
			<!--播流地址FLV(只能PC播放)：<input class="flv_url" type="text"><br>	
			播流地址rtmp(只能PC/APP能播放)：<input class="rtmp_url" type="text"><br>-->
			</div>
		`;
		}
		$("#btn_zhibo").click(function(){
			if(uid>=0){
				layer.alert('只有群聊才能直播!');
				return ;
			}
			$.get("/index.php/p/alilive-api-url.html?id="+Math.abs(uid),function(res){
					if(res.code==0){
						layer.alert("只有在app中才能直播，请点击确定，可以获取推流码或者是推流地址用其它第三方APP推流直播",function(){
							layer.closeAll();
							layer.open({
								type: 1,
								title:'直播推流与拉流地址',
								shift: 1,
								area:in_pc?['600px','400px']:['98%','400px'],
								content: show_str,
							});
							$(".live_video_warp").last().find(".codeimg img").attr('src',res.data.push_img);
							$(".live_video_warp").last().find(".push_url").val(res.data.push_url);
							$(".live_video_warp").last().find(".m3u8_url").val(res.data.m3u8_url);
							$(".live_video_warp").last().find(".rtmp_url").val(res.data.rtmp_url);
							$(".live_video_warp").last().find(".flv_url").val(res.data.flv_url);
						});
					}else{
						layer.alert(res.msg);
					}
			});
		});
	},
	win_player:null,	//播放器框架窗口对象
	player:function(urls){
		var m3u8_url = urls.m3u8_url;
		var rtmp_url = urls.rtmp_url;
		var flv_url = urls.flv_url;
		if(in_pc==true){
			layer.open({  
				  type: 2,    
				  title: '直播开始了...',  
				  fix: false,  
				  shadeClose: false,  
				  offset: ['10px', '10px'],
				  shade: 0,
				  maxmin: true,
				  scrollbar: false,
				  closeBtn:2,  
				  area: ['520px', '370px'],  
				  content: "/public/static/libs/bui/pages/zhibo/player.html",
				  success: function(layero, index){  
					//var body = layer.getChildFrame('body', index);  //body.find('#dd').append('ff');    
					win_player = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：win.method();  
					win_player.palyer(flv_url, '300px');			
				  }
			});
		}else{			
			window.parent.load_chat_iframe("/public/static/libs/bui/pages/zhibo/player.html",function(win,body){
				win.palyer(m3u8_url);
			});
		}
	},
	haveLoadPlayer:false,
	check_play:function(res,type){ //检查聊天记录中,是否包含直播信息
		if(type=='cknew' && res.data.length>0){
			res.data.forEach((rs)=>{
				if(rs.content.indexOf('live_video_start')>0){	//收到新的直播消息提醒
					if(this.haveLoadPlayer==true)this.haveLoadPlayer = false;	//中断过的 . 重新发起直播
				}
			});
		}
		if(this.haveLoadPlayer!=true && typeof(res.ext)=='object' && typeof(res.ext.live)=='object' && typeof(res.ext.live.live_video)=='object'){
			this.haveLoadPlayer = true;
			this.player(res.ext.live.live_video);	//设置播放器	
		}
	},
	sync_play:function(obj){  //收到打开播放器的请求指令
		if(this.haveLoadPlayer == false){
			this.player(obj.urls);
		}		
	},
}


//类接口,WebSocket下发消息的回调接口
ws_onmsg.zhibo = function(obj){
	if(obj.type=='zhibo_sync_goplay'){	//通知访客打开播放器
		mod_class.zhibo.sync_play(obj.data);
	}else if(obj.type=='zhibo_sync_control'){
		if(mod_class.zhibo.win_player!=null){	//播放器已打开
			mod_class.zhibo.win_player.control(obj.data);
		}
	}
}


//类接口,加载到聊天会话数据时执行的
load_data.zhibo = function(res,type){
	mod_class.zhibo.check_play(res,type);
}