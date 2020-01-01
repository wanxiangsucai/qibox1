//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153 
mod_class.zhibo = {
	zhibo_obj:null,	
	urls:{},
	zhibo_status:false,		//是否在直播进行中
	finish:function(res){  //所有模块加载完才执行
	},
	limit_time:'forever',	//圈主直播可用流量
	user_time:360000,	//会员的可观看时间
	logic_init:function(res){
		//this.check_play(res);
	},
	once:function(){
	},
	connect_timer:null,
	repeat_connect:function(){	//掉线重连
		if(this.connect_timer!=null){
			clearInterval( this.connect_timer );
		}
		//this.zhibo_obj.destroy_push();
		var that = this;
		this.connect_timer = setInterval(function() {
			layer.msg('偿试重连');
			that.zhibo_obj.destroy_push();
			setTimeout(function(){
				that.zhibo_obj.start_push();
			},2000)
		},15000);	//掉线断流后,每15秒刷新一次重连
	},
	only_sound:function(){	//是否仅为音频推送
		if(this.zhibo_obj!==null){
			return this.zhibo_obj.only_sound;
		}else{
			return false;
		}
	},
	success_push:function(){	//成功推流直播
		this.zhibo_status = true;
		layer.msg('直播推流开始了!!');
		postmsg('<div class="live_video_start">APP端开始直播了...</div>');	//发送数据到服务器		
		this.zhibo_obj.showbtn();  //推流成功,才显示菜单
		if(this.zhibo_obj.only_sound==true){
			router.$(".post_btn_wrap .btn3").hide(); //没有切换摄像头的功能
		}else{
			router.$(".btnmenu .btn3").show(); //恢复只推音频没有切换摄像头的功能	
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
	//自建服务器的话要做通知开播处理
	selfsever_url:'',
	notify_selfsever:function(url){
		if(typeof(url)=='string' && url!=''){
			this.selfsever_url = url;	//停播后,还要做刷新处理
			var that = this;
			setTimeout(function(){
				$.get("/index.php/p/alilive-api-server_status.html?id="+Math.abs(uid),function(res){
					if(res.code==0){//开播了
					}else{	//还没开播,继续刷新,如果一直不开播,就会一直刷新不停
						that.notify_selfsever(url);
					}
				});
			},3000);
		}
	},
	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		var that = this; //参数引用
		this.haveLoadPlayer = false; //用户重新进来的时候,不能缺少这个处理,不然无法弹出播放器
		if(this.zhibo_status==true && my_uid==quninfo.uid){	//解决用户切换到了其它圈子,再回来就没有菜单的情况 ,这里只在APP中执行
			this.add_btn();
			this.zhibo_obj.add_btn_fun();
			this.zhibo_obj.showbtn();
		}

		$("#btn_zhibo").click(function(){
			if(uid>=0){
				layer.alert('只有群聊才能直播!');
				return ;
			}else if(quninfo.uid!=my_uid){
				layer.alert('只有圈主才能直播，你如果没有圈子的话，可以创建一个！');
				return ;
			}else if(typeof(api)=='object'){	//在APP中执行,打开直播			
				that.app_start_zhibo();
				return ;
			}
			$.get("/index.php/p/alilive-api-url.html?id="+Math.abs(uid),function(res){
					if(res.code==0){
						that.notify_selfsever(res.data.self_server_api);	//自建服务器的话要做通知开播处理
						var play_url = '';
						if( typeof(res.data.limit_time)!='number' ){	//需要购买直播流量的圈主,就不要显示播流地址,避免拿到站外去播放
							play_url = `播流地址m3u8(PC/WAP/APP都能播放)：<input class="m3u8_url" type="text"><br>
							播流地址FLV(只能PC播放)：<input class="flv_url" type="text"><br>	
							播流地址rtmp(只能PC/APP能播放)：<input class="rtmp_url" type="text"><br>`;
						}
						var show_str  = `
							<div class="live_video_warp">
							<div class="codeimg"><img src="" onerror="this.src='http://www.qibosoft.com/images/showad/h_wei.png'"><br>手机扫码推流</div>
							推流地址：<input class="push_url" type="text"><br>
							${play_url}
							</div>
						`;

						if(!in_pc){	//手机端
							show_str  = `
							<div class="live_video_warp">
							<div class="codeimg"><img src="" onerror="this.src='http://www.qibosoft.com/images/showad/h_wei.png'"><br>其它手机扫码推流</div>
							推流地址：<input class="push_url" type="text"><br>
							<!--播流地址：<input class="m3u8_url" type="text"><br>
							播流地址FLV(只能PC播放)：<input class="flv_url" type="text"><br>	
							播流地址rtmp(只能PC/APP能播放)：<input class="rtmp_url" type="text"><br>-->
							</div>
							`;
						}
						layer.alert("只有在app中才能直播，请点击确定，可以获取推流码或者是推流地址用其它第三方APP推流直播",function(index){
							layer.close(index);
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
	count_handle:null,
	win_player:null,	//播放器框架窗口对象
	player_index:null,
	player:function(urls,only_sound){
		if( this.zhibo_status==true ){
			layer.msg('自己就不播放了,避免出现回音');		//刷新数据的时候,有可能会出现的
			return ;
		}

		if( parseFloat(quninfo.minute_money)>0.00001 ){
			if(this.count_handle != null){
				clearInterval(this.count_handle);
			}
			var that = this;
			this.count_handle = setInterval(function(){
				that.user_time--;
				if(that.user_time<1){
					clearInterval(that.count_handle);
					that.stop();
					layer.alert("你的直播课堂时长已用完了，是否马上充值？",{
						btn:['马上充值','不要了'],
						yes: function(index) {
							window.location.reload();
						},
					});
				}
			}, 1000 );
		}

		ws_send({type:'count_zhibo_viewtime_satrt'});	//打开播放器就要通知服务器更新当前用户的观看时间

		this.haveLoadPlayer = true;

		var m3u8_url = urls.m3u8_url;
		var rtmp_url = urls.rtmp_url;
		var flv_url = urls.flv_url;
		var that = this;
		if(in_pc==true){
			this.player_index = layer.open({  
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
				  content: "/public/static/libs/bui/pages/zhibo/player.html?w",
				  success: function(layero, index){  
						//var body = layer.getChildFrame('body', index);  //body.find('#dd').append('ff');    
						that.win_player = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：win.method();  
						that.win_player.palyer(flv_url, only_sound==true?'100px':'300px',only_sound);			
				  }
			});
		}else{			
			load_chat_iframe("/public/static/libs/bui/pages/zhibo/dplayer.html",function(win,body){
				win.palyer(m3u8_url,only_sound==true?'40px':'200px',only_sound);
			});
		}
	},
	stop:function(){	//结束直播

		ws_send({type:'count_zhibo_viewtime_stop'});	//通知服务器更新当前用户的观看时间

		if(this.zhibo_obj!=null){	//圈主关闭直播功能
			this.zhibo_obj.stop();
		}		
		this.zhibo_status = false;
		this.haveLoadPlayer = false;
		if(in_pc==true){
			layer.close(this.player_index);			
		}else{
			load_chat_iframe('');
		}
		layer.msg('直播结束了!');
	},
	haveLoadPlayer:false,
	waitTime:null,
	check_play:function(res,type){ //用户刚进来时,检查聊天记录中,是否包含直播信息
		if(type=='cknew'){	//刷新到数据就不需要了,因为WS有另外传数据过来
			return ;
		}
		if( typeof(res.ext)=='object' && typeof(res.ext.live)=='object' && typeof(res.ext.live.live_video)=='object' ){
			this.limit_time = res.ext.live.live_video.limit_time;	//圈主直播可用流量统计
			this.prepare_play( res.ext.live.live_video );
		}
	},
	prepare_play:function(urls){
		var that = this; //参数引用传递
		if( parseFloat(quninfo.minute_money)>0.00001 && quninfo.uid!=my_uid ){
			that.user_time = 0;
			$.get("/index.php/qun/wxapp.viewtime/count_time.html?aid="+Math.abs(uid),function(res){
				if(res.code==0){
					that.user_time = parseInt(res.data.time);
					if(res.data.time_type==2){
						layer.msg('提示：本直播课堂内容属于收费内容，你是VIP以上级别会员，享受不限量，请尽情收看!');
						that.ask_play(urls);
					}else if(res.data.time_type==1 || that.user_time<7200){
						var msg = "";
						if(res.data.time_type==1){
							msg = "本直播课堂属于收费内容，你可以试看 "+parseInt(that.user_time/60)+" 分钟，但建议你先充值，不然影响到正常观看!";
						}else if(that.user_time>0){
							msg = "本直播课堂属于收费内容，你所剩余的时间不足两小时，仅有 "+parseInt(that.user_time/60)+" 分钟，建议你先充值，不然影响到正常观看!";
						}else{
							msg = "本直播课堂属于收费内容，你没有可观看的时长，请先充值才能观看!";
						}
						layer.alert(msg,{
							btn:['马上充值','以后再说'],
							yes: function(index) {
								buy_time();
							},
							btn2:function(index) {
								if(that.user_time>0){
									that.ask_play(urls);
								}
							}
						});
					}else{
						layer.msg("本直播课堂内容属于收费内容，你还有 "+parseInt(that.user_time/60)+" 分钟，请尽情观看!");
						that.ask_play(urls);
					}
				}
			});
		}else{
			that.ask_play(urls);
		}
		//选择购买多少分钟时长
		function buy_time(){
			jQuery.getScript( (typeof(web_url)=='undefined'?'/':'')+'public/static/js/pay.js' ).done(function() {});
			layer.prompt({
					formType: 0,
					value: '100',
					title: '请输入要充值多少分钟,单位分钟',
					//area: ['100px', '20px'] //formType:2 自定义文本域宽高
				}, function(value, index, elem){
					value = parseInt(value);
					if(value<1){
						layer.alert('充值不能小于1分钟');
						return ;
					}
					layer.close(index);
					goto_buy(value);
			});
		}
		
		//偿试购买流量,金额不一定够
		function goto_buy(time){
			$.get("/index.php/qun/wxapp.viewtime/buy_time.html?aid="+Math.abs(uid)+"&time="+time,function(res){
				if(res.code==0){
					layer.msg('购买成功,请尽情收看吧!');
					that.user_time += time;
					that.ask_play(urls);
				}else if(res.code==2){
					layer.alert(res.msg,{
						btn:['马上充值','以后再说'],
						yes: function(index) {
							goto_pay(res.data.money,time);
						},
						btn2:function(index) {
							if(that.user_time>0){
								that.ask_play(urls);
							}
						}
					});
				}else{
					layer.alert(res.msg);
				}
			});
		}
		//在线充值
		function goto_pay(money,time){
			var fun = in_pc==true ? Pay.pcpay : Pay.mobpay;
			fun(money,'购买直播流量',function(type,index){
				if(type=='ok'){
					layer.msg('充值成功,系统正在帮你充值时长流量');
					goto_buy(time);
				}else{
					layer.alert('充值失败');
					if(that.user_time>0){
						that.ask_play(urls);
					}
				}
			});
		}
	},
	ask_play:function(urls){
		this.urls = urls;
		//请求圈主当前播放状态是不是纯音频 请求成功后,再播放,要保证WS服务器正常连上.否则3秒后自动播放
		ws_send({type:"user_ask_quner",tag:"ask_live_state"},'user_cid');
		var that = this;
		this.waitTime = setTimeout(function(){
			if( my_uid!=quninfo.uid && typeof(that.limit_time)=='number' ){
				layer.alert('提示:圈主可能掉线了,直播暂停!');
			}else{
				layer.msg('圈主没反馈!');
				that.player(urls);	//自动开启播放器	
			}			
		},3000);	//3秒内没收到圈主的反馈信息,就自动开始播放
	},
	sync_play:function(urls,only_sound){  //收到打开播放器的请求指令
		if(this.waitTime!=null){			
			clearTimeout(this.waitTime);	//清除上面设置的
			this.waitTime = null;
		}
		if(this.haveLoadPlayer == false){
			this.player(urls.length==0?this.urls:urls,only_sound);
		}		
	},
}


//类接口,WebSocket下发消息的回调接口
ws_onmsg.zhibo = function(obj){
	if(obj.type=='ask_live_state'){	//访客请求播放状态 ,圈主进行上传回馈.

		var msgarray = {
			type: "quner_to_user",		//群主发给指定会员的指令 这个是固定标志
			user_cid: obj.user_cid,		//某个会员的ID标志			
			tag: 'give_live_state' ,	//访客接收标志 ,不同插件,这个标志不能雷同,避免冲突
			data: {
				urls:mod_class.zhibo.urls,	//这个值有可能不存在,因为圈主发起推流的时候没设置
				only_sound:mod_class.zhibo.only_sound(),
			},
		}
		ws_send(msgarray); //通知服务器,将上面的信息发给指定会员		

	}else if(obj.type=='give_live_state'){  //访客收到的播放信息 , 由上面圈主发出的指令, 不同插件,这个标志不能雷同,避免冲突

	    //这里重新给网址是考虑可以更换网址
		mod_class.zhibo.sync_play(obj.data.urls,obj.data.only_sound);

	}else if(obj.type=='error#give_live_state'){  //圈主不在,或者是圈主首次访问 就 直接播放 , 用第三方推流工具的时候,才用到的.

		if( my_uid!=quninfo.uid && typeof(mod_class.zhibo.limit_time)=='number' ){
			layer.alert('圈主掉线,直播暂停了!');
		}else{
			mod_class.zhibo.sync_play(mod_class.zhibo.urls);
		}
		
	}else if(obj.type=='zhibo_server_stop'){	//推流服务器发过来的通知,推流断开了,就自动关闭,推流断开有可能是网络的问题,所以不一定是圈主 人为主动关闭直播
		
		mod_class.zhibo.stop();
		if(mod_class.zhibo.zhibo_status==true){	//可能是网络故障掉线的.偿试重连接 ,圈主才执行这里的
			mod_class.zhibo.repeat_connect();
		//}else if(mod_class.zhibo.selfsever_url!=''){	//可能是网络故障掉线的.偿试重连接 ,主要是给第三方推流工具使用
		//	mod_class.zhibo.notify_selfsever(mod_class.zhibo.selfsever_url);
		//}
		//这个相比上面这个好处是,避免用户刷新过开播的界面导致mod_class.zhibo.selfsever_url值并不存在,因为这个值,只有开播的时候,才存在的.
		}else if(quninfo.uid==my_uid){	//可能是网络故障掉线的.偿试重连接 ,主要是给第三方推流工具使用 ,
			mod_class.zhibo.notify_selfsever('reconnect');
		}
		
	}else if(obj.type=='zhibo_server_start'){  //聊天过程中,圈主中途打开直播,通知所有用户打开直播

		if(mod_class.zhibo.connect_timer!=null){	//这里处理圈主直播断线重连,这里是圈主才执行的
			layer.msg('重连成功');
			clearInterval( mod_class.zhibo.connect_timer );
			mod_class.zhibo.connect_timer = null;
		}
		mod_class.zhibo.urls = obj.data;
		mod_class.zhibo.prepare_play(obj.data);

	}else if(obj.type=='count' || obj.type=='leave'){	//用户进来或者离开

		var total_valid_time = obj.total_valid_time;
		if( typeof(mod_class.zhibo.limit_time)=='number' && total_valid_time>=mod_class.zhibo.limit_time ){	//直播流量已用完
			mod_class.zhibo.stop();	//强制关闭
			if( my_uid==quninfo.uid ){
				layer.alert('你的直播流量用完了,被强制关闭');
			}else{
				layer.msg('圈主的直播流量用完了,被强制关闭');
			}
		}

	}
}


//类接口,加载到聊天会话数据时执行的  刷新数据的时候也会有到.不仅仅是初次加载
load_data.zhibo = function(res,type){
	mod_class.zhibo.check_play(res,type);
}