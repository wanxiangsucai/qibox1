//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.zhibo = {
	zhibo_obj:null,
	urls:{},
	zhibo_status:false,		//是否在直播
	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){
		//this.check_play(res);
	},
	once:function(){
	},
	only_sound:function(){	//是否仅为音频推送
		if(this.zhibo_obj!==null){
			return this.zhibo_obj.only_sound;
		}else{
			return false;
		}
	},
	add_btn:function(){
		var str = `<link rel="stylesheet" href="public/static/libs/bui/pages/zhibo/style.css">
				<div class="post_btn_wrap" style="display:none;">
					<div class="btnmenu"><span class="post_btn_menu fa fa-video-camera"></span></div>
					<!--<div class="post_btn btn1"><span>对焦</span></div>-->
					<div class="post_btn btn2"><span>静音</span></div>
					<div class="post_btn btn3"><span>后摄像</span></div>
					<div class="post_btn btn4"><span>退出</span></div>
				</div>`;
		$(".chatbar").after(str);
	},
	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		var that = this; //参数引用
		if(this.zhibo_status==true && my_uid==quninfo.uid){	//解决用户切换到了其它圈子,再回来就没有菜单的情况
			this.add_btn();
			this.zhibo_obj.add_btn_fun();
			this.zhibo_obj.showbtn();
		}
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
			}else if(quninfo.uid!=my_uid){
				layer.alert('只有圈主才能直播，你如果没有圈子的话，可以创建一个！');
				return ;
			}else if(typeof(api)=='object'){
				that.zhibo_status = true;
				that.app_start_zhibo();
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
	app_start_zhibo:function(){	//在APP中直播		
		if(this.zhibo_obj!=null){
			this.zhibo_obj.start();
		}else{
			this.add_btn();		//添加菜单元素
			var that = this; //变量引用			
			loader.require("public/static/libs/bui/pages/zhibo/bo",function (o) {
				that.zhibo_obj = o;				
				that.zhibo_obj.start();
				that.zhibo_obj.add_btn_fun();			//直播菜单加点击事件
				//that.zhibo_obj.showbtn();
			});
		}
	},
	win_player:null,	//播放器框架窗口对象
	player:function(urls,only_sound){
		if( this.zhibo_status==true ){
			layer.msg('自己就不播放了,避免出现回音');		//刷新数据的时候,有可能会出现的
			return ;
		}
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
				  area: ['520px', only_sound==true?'170px':'370px'],  
				  content: "/public/static/libs/bui/pages/zhibo/player.html",
				  success: function(layero, index){  
					//var body = layer.getChildFrame('body', index);  //body.find('#dd').append('ff');    
					win_player = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：win.method();  
					win_player.palyer(flv_url, only_sound==true?'100px':'300px',only_sound);			
				  }
			});
		}else{			
			window.parent.load_chat_iframe("/public/static/libs/bui/pages/zhibo/player.html",function(win,body){
				win.palyer(m3u8_url,only_sound==true?'30px':'200px',only_sound);
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
			//if(this.play_status!=true){	//首次 请求圈主当前播放状态是不是纯音频 请求成功后,再播放,要保证WS服务器正常连上.否则永远不播放.
			//	this.urls = res.ext.live.live_video; 		
			//	ws_send({type:"user_ask_quner",tag:"ask_live_state"},'user_cid');
			//}else{
				this.haveLoadPlayer = true;
				this.player(res.ext.live.live_video);	//设置播放器	
			//}			
		}
	},
	sync_play:function(urls,only_sound){  //收到打开播放器的请求指令
		this.play_status = true;
		if(this.haveLoadPlayer == false){
			this.haveLoadPlayer = true;
			console.log('地址',urls.length==0?this.urls:urls);
			this.player(urls.length==0?this.urls:urls,only_sound);
		}		
	},
}


//类接口,WebSocket下发消息的回调接口
ws_onmsg.zhibo = function(obj){
	if(obj.type=='ask_live_state'){	//访客请求播放状态 ,圈主进行上传回馈.
		var msgarray = {
			type: "quner_to_user",		//群主发给指定会员的指令
			user_cid: obj.user_cid,		//某个会员的ID标志			
			tag: 'give_live_state' ,	//访客接收标志
			data: {
				urls:mod_class.zhibo.urls,	//这个值有可能不存在,因为圈主发起推流的时候没设置
				only_sound:mod_class.zhibo.only_sound(),
			},
		}
		ws_send(msgarray); //通知服务器,将上面的信息发给指定会员		
	}else if(obj.type=='give_live_state'){  //访问收到的播放信息 , 由上面圈主发出的指令
	    //这里重新给网址是考虑可以更换网址
		mod_class.zhibo.sync_play(obj.data.urls,obj.data.only_sound);
	}else if(obj.type=='error#give_live_state'){  //圈主不在,或者是圈主首次访问 就 直播播放 , 用第三方推流工具的时候,才用到的.
		mod_class.zhibo.sync_play(mod_class.zhibo.urls);
	}
}


//类接口,加载到聊天会话数据时执行的  刷新数据的时候也会有到.不仅仅是初次加载
load_data.zhibo = function(res,type){
	mod_class.zhibo.check_play(res,type);
}