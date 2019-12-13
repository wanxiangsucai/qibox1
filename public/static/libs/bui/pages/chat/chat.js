/**
 * 聊天对话模板
 * 默认模块名: pages/chat/chat
 * @return {[object]}  [ 返回一个对象 ]
 */
var refresh_i,refresh_timenum;//初始化8秒刷新一次
var is_live = 0; //是否在直播
var live_urls = {flv_url:'',m3u8_url:'',rtmp_url:''}; //直播地址
var w_s,clientId;

//CMS音频 用户请求同步播放 给框架窗口使用
function user_ask_sync_vod_voice(){
	w_s.send('{"type":"user_ask_quner","tag":"ask_vod_voice_sync","user_cid":"'+clientId+'"}');
}

loader.define(function(require,exports,module) {

    var pageview = {};
	var uid,qid;
	var msg_scroll = 1;
	var show_msg_page  = 1;
	var maxid = -1;
	var getShowMsgUrl = "/index.php/index/wxapp.msg/get_more.html?rows=15&page=";
	var userinfo = {};	//当前登录用户的基础信息
	var quninfo = {};	//圈子的信息
	var need_scroll = false;
	var touser = {uid:0};	//@TA
	var qun_userinfo = '';	//当前用户所在当前圈子的信息
	var user_list = {}; //圈子用户列表
	var user_num = 0; //圈子成员总数
	var uiSidebar;          // 侧边栏
	var video_player;
	var have_load_live_player=false;
	var ws_url,ws_have_link;
	var voice_iframeWin,voice_urls;
	var pushIdArray = [];

	//建立WebSocket长连接
	pageview.ws_connect = function(){
		if( typeof(w_s)=='object' ){
			w_s.close();
			console.log("#########中断了,重新连接!!!!!!!!!!"+Math.random());
			setTimeout(function(){	//避免还没有完全关闭连接
				pageview.ws_link();
			},3000);
		}else{
			pageview.ws_link();
		}
	}
	pageview.ws_link = function(){
		
		ws_have_link = true;
		w_s = new WebSocket(ws_url);
		w_s.onmessage = function(e){
			var obj = {};
			try {
				obj = JSON.parse(e.data);
			}catch(err){
				console.log(err);
			}
			if(obj.type=='newmsg'){
				check_new_showmsg(obj);	//推数据
				$.get("/index.php/index/wxapp.msg/update_user.html?uid="+uid+"&id="+obj.data[0].id,function(res){//更新记录
					console.log(res.msg);
				});	
				console.log("有新消息来了");
				console.log(obj);
			}else if(obj.type=='new_msg_id'){	//圈子直播文字最后得到的真实ID
				pushIdArray[obj.data.push_id] = obj.data.id; //删除内容的时候要用到
				$.get("/index.php/index/wxapp.msg/update_user.html?uid="+uid+"&id="+obj.data.id,function(res){//更新记录
					console.log(res.msg);
				});
			}else if(obj.type=='qun_sync_msg'){	//圈子直播文字  
				check_new_showmsg(obj);
			}else if(obj.type=='connect'){	//建立链接时得到客户的ID
				if(uid==0){
					return ;
				}
				clientId = obj.client_id;
				$.get("/index.php/index/wxapp.msg/bind_group.html?uid="+uid+"&client_id="+obj.client_id,function(res){	//绑定用户
					if(res.code==0){
						//layer.msg('欢迎到来!',{time:500});
					}else{
						layer.alert(res.msg);
					}
				});
				var my_uid = userinfo.uid;
				var username = my_uid>0?userinfo.username:'';
				var icon = my_uid>0?userinfo.icon:'';
				var is_quner = my_uid==quninfo.uid ? 1 : 0;	//圈主
				w_s.send('{"type":"connect","url":"'+window.location.href+'","uid":"'+uid+'","my_uid":"'+my_uid+'","is_quner":"'+is_quner+'","userAgent":"'+navigator.userAgent+'","my_username":"'+username+'","my_icon":"'+icon+'"}');
			}else if(obj.type=='count'){  //用户连接成功后,算出当前在线数据统计
				 show_online(obj,'goin');
			}else if(obj.type=='leave'){	//某个用户离开了
				show_online(obj,'getout')
				console.log(obj);
			}else if(obj.type=='give_vod_voice_state'){	//CMS音频 成功获取到直播信息,访客得到音频的播放状态
				voice_urls = obj.data.play_urls;	//首次点播要用
				vod_voice_play(obj.data,'ok');
			}else if(obj.type=='error#ask_vod_voice_state'){	//CMS音频 圈主首次进入或者圈主不在,按默认的播放
				vod_voice_play({play_urls:voice_urls},'err');
			}else if(obj.type=='ask_vod_voice_state'||obj.type=='ask_vod_voice_sync'){	//CMS音频 访客请求圈主的音频播放状态,圈主进行反馈,圈主如有多个窗口最后一次登录窗口才收到,第一次窗口不会收到.
				var arr ={};
				if(typeof(voice_iframeWin)=='object'){
					arr = {
						play_index:voice_iframeWin.get_now_state()[0],
						play_time:voice_iframeWin.get_now_state()[1],		
					};
				}
				arr.play_urls = voice_urls;
				var tag = obj.type=='ask_vod_voice_sync'?'give_vod_voice_sync':'give_vod_voice_state';
				w_s.send('{"type":"quner_to_user","tag":"'+tag+'","data":' + JSON.stringify( arr ) + ',"user_cid":"' + obj.user_cid + '"}');
			}else if(obj.type=='vod_voice_sync_play'){  //CMS音频 同步播放
				voice_iframeWin.control(obj.data);
			}else if(obj.type=='give_vod_voice_sync'){  //CMS音频 获取到同步信息
				voice_iframeWin.vod.sync_play({index:obj.data.play_index,time:obj.data.play_time});
			}else{
				console.log(e.data);
			}
		};
		
		w_s.onopen = function(e) {};
		w_s.onerror = function(e){
			ws_have_link = false;
		};
		w_s.onclose = function(e){
			ws_have_link = false;
		};
		
		if(typeof(chat_timer)!='undefined')clearInterval(chat_timer);
		chat_timer = setInterval(function() {
			if(ws_have_link != true){
				w_s = new WebSocket(ws_url);
			}else{
				w_s.send('{"type":"refresh"}');
			}
		}, 1000*50);	//50秒发送一次心跳
		
		var show_online = function(obj,type){
				 var total = obj.total; //在线窗口,同一个人可能有多个窗口				 
				 var data = obj.data;
				 var usernum = obj.data.length;  //在线会员人数,已注册的会员
				 if(total>1){
					 if(type=='goin'){
						 bui.hint("有新用户："+data[data.length-1].username+" 进来了");
					 }else if(type=='getout'){
						 bui.hint(obj.msg);
					 }
					 $("#remind_online").show();
					 if(uid>0){
						 $("#remind_online").html('对方在线,请不要离开!');
					 }else{
						 $("#remind_online").html('共有 '+total+' 个访客,会员有 '+usernum+' 人! 查看详情');
						 $("#remind_online").off('click');
						 $("#remind_online").click(function(){
							 view_online_user(data);
						 });
					 }
				 }else if( !$("#remind_online").is(':hidden') ){
					 if(uid>0){
						 bui.hint('对方已离开!');
					 }else{
						 bui.hint('人全走光了!'+obj.msg);
					 }
					 $("#remind_online").hide();
				 }
		}

		var view_online_user = function(data){
			var str = '';
			data.forEach((rs)=>{
				str += '<a href="/member.php/home/'+rs.uid+'.html" target="_blank">'+rs.username+'</a>、';
			});
			layer.open({
					type: 1,
					anim: 5,
					shade: 0,
					title: '仅列出已注册的会员，不含游客',
					area: ['90%', '65%'],
					content: '<div style="padding:20px;line-height:180%;">'+str+'</div>',
			});
		}
	}

	//加载到第一页成功后,就获得了相关数据,才好进行其它的操作
	function load_first_page(res){
		if_load = true;
		maxid = res.ext.maxid;

		quninfo = res.ext.qun_info;	//圈子信息
		window.store.set("quninfo",quninfo);
		//vues.set_quninfo(quninfo);
		router.$("#send_user_name").html(quninfo.title);

		qun_userinfo = res.ext.qun_userinfo;	//当前圈子用户信息 不存的话,就是为空即==''
		userinfo = res.ext.userinfo;	//当前用户登录信息
		head_menu(uid,quninfo,qun_userinfo,userinfo);
		
		ws_url = res.ext.ws_url;
		
		if(ws_url==''){	//没有设置WS的话,就用AJAX轮询
			if(typeof(chat_timer)!='undefined')clearInterval(chat_timer);
			refresh_i=0;
			refresh_timenum = 8;//初始化8秒刷新一次
			chat_timer = setInterval(function() {
				refresh_i++;
				//刷新会话用户中有没有新消息,必须要加载到内容后有maxid值才去刷新 初始化还没互动之前,不要刷新太快
				if(maxid>=0 && refresh_i%refresh_timenum==0)check_new_showmsg();	
			}, 1000);
		}else{
			pageview.ws_connect();	//建立长链接
		}

		setTimeout(function(){
			set_live_player(res);	//设置视频直播的播放器

			if(have_load_live_player==true){	//直播的时候,就不弹出签到了,影响界面布局
				$.get("/index.php/p/signin-api-get_cfg/id/"+quninfo.id+".html",function(res){
					if(res.code==0){
						if(res.data.today_have_signin==true){
							console.log('今天已经签到过了');
						}else{
							router.loadPart({
								id: "#hack_signin",
								url: "/public/static/libs/bui/pages/signin/pop.html?fdd",
							}).then(function (module) {
								module.api(quninfo,qun_userinfo,userinfo,res.data);
							});
						}					
					}
				});			
			}

			pageview.weixin_share();
		},1000);
	}

    // 模块初始化定义
    pageview.init = function () {

        this.bind();
		this.right_btn();

		setTimeout(function(){
			uiSidebar = bui.sidebar({
				id      : "#sidebar",
				handle: ".page-chat",
				width   : 550
			});
		},1500);

		router.$("#chat_win").parent().scroll(function () {
			var h = router.$("#chat_win").parent().scrollTop();
			//console.log(h);
			if( h<100){
				if(msg_scroll!=-1){
					//console.log(h+"--"+msg_scroll);
					router.$('#chat_win').parent().scrollTop(150);
				}
				if(msg_scroll==1){//console.log("++");
					msg_scroll = 0;					
					layer.msg("内容加载中！请稍候...",{time:3000});
					showMoreMsg(uid);
				}								
			}
		});

		if(typeof(to_uid)!='undefined' && to_uid!=""){	//详情页或发送页
			uid = to_uid;
			set_user_name(uid);	//设置当前会话的用户名
			showMoreMsg(uid);	//加载相应用户的聊天记录
			router.$(".right-tongji").on("click",function(e){
			  bui.load({ url: "/member.php/member/msg/index.html" ,reload:true});
			});
			router.$(".bui-bar-left a").removeClass('btn-back');
			router.$(".bui-bar-left i").removeClass('icon-back');
			router.$(".bui-bar-left i").addClass('fa fa-home');
			router.$(".bui-bar-left a").on("click",function(e){
			  bui.load({ url: "/",reload:true});
			});
		}else{
			router.$(".bui-bar-right a").on("click",function(e){
				clearInterval(chat_timer);
				//bui.back();
			});
			router.$(".btn-back").on("click",function(e){
				clearInterval(chat_timer);
			  //bui.back();
			});			
		}


		//this.upload();
		//loader.import(["/public/static/js/exif.js"],function(){});	//上传图片要获取图片信息

		if(uid<0){
			setTimeout(function(){
				$.get("/index.php/qun/wxapp.visit/check_visit/id/"+(-uid)+".html",function(res){});	//更新圈子浏览日志
			},2000);
		}

		loader.require("/public/static/libs/bui/pages/chat/play_video",function (play) {
			video_player = play;
		});

    }
	pageview.right_btn = function () {
        // 初始化下拉更多操作
        var uiDropdownMore = bui.dropdown({
          id: "#right_more",
          showArrow: true,
          width: 165
        });

        // 下拉菜单有遮罩的情况
        var uiMask = bui.mask({
          appendTo:"#chat_win",
          opacity:"0.3",
          zIndex:1,
          callback: function (argument) {
            // 隐藏下拉菜单
            uiDropdownMore.hide();
          }
        });

        // 通过监听事件绑定
        uiDropdownMore.on("show",function () {
			uiMask.show();
        })
        uiDropdownMore.on("hide",function () {
			uiMask.hide();
        });
	}

    pageview.bind = function () {

		router.$("#choose_qqface").on("click",function () {
			router.$(".hack_wrap").hide();
			if(router.$(".face_wrap").html()!=""){
				if(router.$(".face_wrap").is(":hidden")){
					router.$(".face_wrap").show();
				}else{
					router.$(".face_wrap").hide();
				}
			}else{
				router.$(".face_wrap").show();
				router.loadPart({
					id: ".face_wrap",
					url: "/public/static/libs/bui/pages/chat/qqface.html"
				})
			}
        })

		router.$("#show_hack").on("click",function () {
			router.$(".face_wrap").hide();
			if(router.$(".hack_wrap").html()!=""){
				if(router.$(".hack_wrap").is(":hidden")){
					router.$(".hack_wrap").show();
				}else{
					router.$(".hack_wrap").hide();
				}
			}else{
				router.$(".hack_wrap").show();
				router.loadPart({
					id: ".hack_wrap",
					url: "/public/static/libs/bui/pages/chat/hack.html?f",
				}).then(function (module) {
					//pageview.upload();
					loader.require("/public/static/libs/bui/pages/chat/voice",function (voice) {
						 console.log(voice)
					 })
				});
			}
        })

            // 发送的内容
        var $chatInput = router.$(".chatInput"),
            // 发送按钮
            $btnSend = router.$("#btnSend"),
            // 聊天的容器
            $chatPanel = router.$(".chat-panel");

        // 绑定发送按钮
        $btnSend.on("click",function (e) {
            var val = $chatInput.val();
            //var tpl = chatTpl(val);
            if( !$(this).hasClass("disabled") ){
                //$chatPanel.append(tpl);
				postmsg(val);
                //$chatInput.val('');
                $(this).removeClass("primary").addClass("disabled");
            }else{
                return false;
            }
        });
		
		 $chatInput.click(function(){
			if(typeof(userinfo.uid)=='undefined'){
				userinfo = window.store.get('userinfo');
			}
			if(userinfo.uid<1){
				layer.confirm("你还没登录，不能发言，是否立即登录？",{btn:['立即登录','取消'],title:"提示"},function () {
					window.location.href = '/index.php/index/login/index.html?fromurl='+encodeURIComponent(window.location.href);
				});
			}
		 });

        // 延迟监听输入
        $chatInput.on("input",bui.unit.debounce(function () {
            var val = $chatInput.val();
            if( val ){
                $btnSend.removeClass("disabled").addClass("primary");

            }else{
                $btnSend.removeClass("primary").addClass("disabled");

            }
        },100))

        var interval = null;
        var count = 3;
        // 安卓键盘弹出的时间较长;
        var time = bui.platform.isIos() ? 200 : 400;
        // 为input绑定事件
        $chatInput.on('focus', function () {

            var agent = navigator.userAgent.toLowerCase();
            interval = setTimeout(function() {
                if (agent.indexOf('safari') != -1 && agent.indexOf('mqqbrowser') == -1 &&
                    agent.indexOf('coast') == -1 && agent.indexOf('android') == -1 &&
                    agent.indexOf('linux') == -1 && agent.indexOf('firefox') == -1) {
                    //safari浏览器
                    window.scrollTo(0, 1000000);
                    setTimeout(function() {
                        window.scrollTo(0, window.scrollY - 45);
                    }, 50)

                } else {
                    //其他浏览器
                    window.scrollTo(0, 1000000);
                }

            }, time);
        }).on('blur', function () {
            if( interval ){
                clearTimeout(interval);
            }

            var agent = navigator.userAgent.toLowerCase();
            interval = setTimeout(function() {
                if (!(agent.indexOf('safari') != -1 && agent.indexOf('mqqbrowser') == -1 &&
                        agent.indexOf('coast') == -1 && agent.indexOf('android') == -1 &&
                        agent.indexOf('linux') == -1 && agent.indexOf('firefox') == -1)) {
                        //safari浏览器
                    window.scrollTo(0, 30);
                }
            }, 0);
        });
    }

	var num = ck_num = 0;
	//刷新会话用户中有没有新消息
	function check_new_showmsg(obj){
		if(ws_url==''){	//没有设置WS的话,就用AJAX轮询
			if(ck_num>num){
				console.log("服务器还没反馈数据过来");
				//layer.msg("服务器反馈超时",{time:500});
				return ;
			}
		}

		if( typeof(obj)=='object' && typeof(obj.data)=='object' && obj.data.length>0 ){		//服务端推数据, 即被动获取数据
			var res = obj;
			//layer.closeAll();
			need_scroll = true;
			add_msg_data(res,'new');
			if(typeof(res.ext)!='undefined')maxid = res.ext.maxid;	//不主动获取数据的话,这个用不到
			set_live_player(res,'cknew');	//设置视频直播的播放器
		}else{	//客户端拉数据, 主动获取数据
			$.get(getShowMsgUrl+"1&maxid="+maxid+"&uid="+uid+"&num="+num,function(res){			
				if(res.code!=0){				
					layer.alert('页面加载失败,请刷新当前网页');
					return ;
				}
				num++;
				ck_num = num;
				if(res.data.length>0){	//有新的聊天内容
					//layer.closeAll();
					need_scroll = true;
					//vues.set_data(res.data);
					add_msg_data(res,'new');
				}
				maxid = res.ext.maxid;
				if(res.ext.lasttime<3){	//3秒内对方还在当前页面的话,就提示当前用户不要关闭当前窗口
					if(uid>0){
						router.$("#remind_online").html("对方正在输入中，请稍候...");
					}else{
						router.$("#remind_online").html("有用户在线");
					}
					router.$("#remind_online").show();
				}else{
					router.$("#remind_online").hide();
				}
				set_live_player(res,'cknew');	//设置视频直播的播放器
			});
			ck_num++;
		}
	}

	//设置视频直播的播放器
	function set_live_player(res,type){
		if(quninfo.uid==my_uid){
			//console.log('自己不需要播放');
			//return ;//自己就不要显示播放了
		}
		if(type=='cknew' && res.data.length>0){
			res.data.forEach((rs)=>{
				if(rs.content.indexOf('live_video_start')>0){
					if(have_load_live_player==true)have_load_live_player = false;	//中断过的 . 重新发起直播
				}
			});
		}
		//live_video参数存在,代表正在直播中
		if(have_load_live_player!=true && typeof(res.ext)!='undefined' && (typeof(res.ext.live_video)!='undefined'||typeof(res.ext.vod_voice)!='undefined') ){
			
			have_load_live_player = true;

			if(typeof(res.ext.vod_voice)!='undefined'){
				vod_voice_play(res.ext.vod_voice);	//设置点播转直播音频播放器
			}else{
				router.$(".live-player-warp").show();
				video_player.play(res.ext.live_video);	//设置视频直播的播放器
			}
		
		}else if(typeof(res.ext)!='undefined' && typeof(res.ext.live_video)=='undefined' && typeof(res.ext.vod_voice)=='undefined' ){
			have_load_live_player = false;
		}
	}
	

	//加载更多的会话记录
	function showMoreMsg(uid){
		if(show_msg_page==1){
			maxid = -1;
			layer.msg("数据加载中,请稍候...");
		}	
		$.get(getShowMsgUrl+show_msg_page+"&uid="+uid,function(res){			
			//console.log(res);
			if(res.code==0){
				if(show_msg_page==1){					
					load_first_page(res);					
				}
				//layer.closeAll();
				var that = router.$('#chat_win');
				if(res.data.length<1){
					if(show_msg_page==1){
						that.parent().scrollTop(0)
						layer.msg("没有任何聊天记录！",{time:1000});
					}else{
						layer.msg("已经显示完了！",{time:500});
					}
					msg_scroll = -1;	//允许滚动条滚到尽头
				}else{
					console.log(res);
					show_msg_page++;
					//msg_scroll = 1;
					//vues.set_data(res.data);
					add_msg_data(res);
				}				
			}else{
				layer.msg(res.msg,{time:2500});
			}
		});
	}

	//添加删除信息的功能按钮
	function add_btn_delmsg(){
		router.$(".chat-panel .del").off("click");
		router.$(".chat-panel .del").click(function(){
			var id = $(this).data("id");
			var that = $(this);
			if(pushIdArray[id]!=undefined){
				id = pushIdArray[id];
			}
			$.get("/member.php/member/wxapp.msg/delete.html?id="+id,function(res){
				if(res.code==0){
					layer.msg("删除成功");
					router.$(".chat-box-"+id).hide();
				}else{
					layer.alert(res.msg);
				}
			});
		});

		router.$(".chat-panel .chat-icon").click(function(){
			router.$(".chatbar").hide();
			setTimeout(function(){
				router.$(".bui-mask").click(function(){
					router.$(".chatbar").show();
				});
			},500);
			touser.uid = $(this).data('uid');
			touser.name = $(this).data('name');
			console.log(touser);
		});
		
		//显示红包
		router.$(".chat-panel .hack-hongbao").each(function(){
			var id = $(this).data("id");
			var title = $(this).data("title");
			var str = `<div onclick="layer.open({type: 2,title: '${title}',shadeClose: true,shade: 0.3,area: ['95%', '80%'],content: '/index.php/p/hongbao-content-show/id/${id}.html'});"><img src="/public/static/plugins/voicehb/hongbao.png"></div>`;
			$(this).html(str);
		});

		format_nickname();	//设置圈子昵称
		format_hack_data();  //处理各频道调用的数据
		
		//设置用户菜单
		bui.actionsheet({
					trigger: ".chat-icon",
					opacity:"0.5",
					buttons: [{ name: "@TA", value: "1" }, { name: "与TA私聊", value: "2" }, { name: "加TA为好友", value: "3" }, { name: "访问TA的主页", value: "4" }],
					callback: function(e) {						
						var ui = this;
						var val = $(e.target).attr("value");
						switch (val) {
							case "1":
								router.$(".chatInput").val("@"+touser.name+" ").focus();
								router.$(".chatbar").show();
								ui.hide();
								break;
							case "2":
								ui.hide();
								bui.load({url: "/public/static/libs/bui/pages/chat/chat.html",param: {"uid": touser.uid}});
								break;
							case "3":
								ui.hide();
								router.$(".chatbar").show();
								add_friend(touser.uid);
								break;
							case "4":
								ui.hide();
								var url = '/member.php/home/'+touser.uid+'.html';
								bui.load({url: "/public/static/libs/bui/pages/frame/show.html",param: {"url":url,"title":touser.name+"的主页"}});
								break;
							case "cancel":
								ui.hide();
								router.$(".chatbar").show();
								break;
						}
					}
				});
	}

	function add_friend(uid){
		if(uid==userinfo.uid){
			layer.alert('你不能加自己为好友');
		}
		$.get("/member.php/member/wxapp.friend/act.html?type=add&uid="+uid,function(res){
			if(res.code==0){
				layer.msg(res.msg);
			}else{
				layer.alert(res.msg);
			}
		});
	}
	

	//设置当前聊天的用户名
	function set_user_name(uid){
		if(uid>0){
			$.get("/index.php/index/wxapp.member/getbyid.html?uid="+uid,function(res){
				if(res.code==0){
					router.$("#send_user_name").html(res.data.username);
				}
			});
		}else if(uid<0){
			$.get("/index.php/qun/wxapp.qun/getbyid.html?id="+Math.abs(uid),function(res){
				if(res.code==0){
					router.$("#send_user_name").html(res.data.title);
				}
			});
		}else{
			router.$("#send_user_name").html("系统消息");
		}		
	}

	//发送消息
	function postmsg(content){
		if(content==''){
			layer.alert('消息内容不能为空');
			return ;
		}

		var push_id = 0;
		if(ws_url!=''){
			push_id = (Math.random()+'').substring(2);
			//if(ws_have_link!=true){	//如果中断了,就要重连
			//	pageview.ws_connect();
			//}
			router.$(".chatInput").val('');
			router.$(".hack_wrap").hide();
			router.$(".face_wrap").hide();
			var time = 0;
			if(ws_have_link!=true){	//上面链接需要时间
				layer.msg("请稍候,正在建立连接...");
				time = 3000;
			}
			setTimeout(function(){
				w_s.send('{"type":"qun_sync_msg","data":' + JSON.stringify( {'content':content,'push_id':push_id} ) + '}');
			},time);			
		}

		$.post("/member.php/member/wxapp.msg/add.html",{
			'uid':uid,
			'content':content,
			'send_to':touser.uid,
			'push_id':push_id
			},function(res){
				
				if(ws_url==''){	//没有设置WS的话,就用AJAX轮询
					clearInterval(chat_timer);
					chat_timer = setInterval(function() {
						check_new_showmsg();
					}, 1500);	//互动之后,加快刷新,重新setInterval是兼容之前任务已死掉
				}

				if(res.code==0){
					router.$(".chatInput").val('');
					router.$(".hack_wrap").hide();
					router.$(".face_wrap").hide();
					//layer.msg('发送成功');
				}else{
					//router.$("#btnSend").removeClass("disabled").addClass("primary");
					layer.alert('本条信息已发出,但并没有入库,原因:'+res.msg);
				}
		});
	}
	
	function add_msg_data(res,type){
		var timer = setInterval(function(){
			if(typeof(userinfo.uid)=='undefined'){
				userinfo = window.store.get('userinfo');
			}
			if(typeof(userinfo.uid)!='undefined'){
				clearInterval(timer);
				var myid = typeof(my_uid)!='undefined'?my_uid:userinfo.uid;
				//console.log(res.data);
				format_msgdata_tohtml(res.data , myid , type)
			}
			//console.log('userinfo=',userinfo);
		},200);	//定时器,为了反复刷新用户登录数据
	}

	function format_msgdata_tohtml(array , myid , type){
		var str = '';
		var del_str = '';
		var user_str = '';
		var userdb = userinfo;
		var old_html = router.$("#chat_win").html();
		array.forEach((rs)=>{
			if(old_html.indexOf( ' chat-box-'+rs.id )>0){
				console.log('有重复的消息'+rs.id);
				return true;
			}
			del_str = '';
			if(userdb.uid>0 && (rs.uid==userdb.uid || rs.touid==userdb.uid) ){
				del_str = `<i data-id="${rs.id}" class="del glyphicon glyphicon-remove-circle"></i>`;
			}

			user_str = `<div class="chat-icon" data-uid="${rs.uid}" data-name="${rs.from_username}"><img src="${rs.from_icon}" onerror="this.src='/public/static/images/noface.png'" title="${rs.from_username}"></div>`;
			if(rs.uid!=myid){
				str += `
					<div class="bui-box-align-top chat-box-${rs.id} chat-target">
						${user_str}
						<div class="span1">
							<div class="chat-content bui-arrow-left">${rs.content}</div>
							${del_str}
						</div>
					</div>	`;
			}else{
				str += `
					<div class="bui-box-align-top chat-box-${rs.id} chat-mine">
					<div class="span1">
						<div class="bui-box-align-right">
						  ${del_str}
						  <div class="chat-content bui-arrow-right">${rs.content}</div>
						</div>
					</div>
					${user_str}
				</div>`;
			}

			if(rs.qun_id>0 && rs.uid!=myid){
				str += `<div class="show_username chat-box-${rs.id}" data-uid="${rs.uid}">${rs.from_username}</div>`;
			}

			str += `
					<div class="bui-box-center chat-box-${rs.id}">
						<div class="time">${rs.create_time}</div>
					</div>`;
		});
		if(type == 'new'){
			router.$("#chat_win").prepend(str);
		}else{
			router.$("#chat_win").append(str);
		}
		
		//对聊天中的链接地址做框架访问
		router.$(".chat-panel .chat-content a").each(function(){
			$(this).removeClass('iframe');
			$(this).addClass('iframe');
		});

		if(show_msg_page==2 || need_scroll==true){
			setTimeout(function(){
				router.$('#chat_win').parent().scrollTop(20000);
			},300);
			need_scroll = false;
		}else{
			router.$('#chat_win').parent().scrollTop(400);
		}
		add_btn_delmsg();
		msg_scroll = 1;
	}


	function head_menu(to_uid,_quninfo,_quser,_user){
		var str = '';
		if(to_uid>0){
			str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/member.php/home/${to_uid}.html&title=个人主页">
                        <i class="icon-jiahao">&#xe660;</i>
                        <div class="span1">TA的主页</div>
                    </li>
					`;
		}else{
			to_id = -to_uid;
			var jifen_str = home_str = listmember_str = more_str = join_str = nickname_str = '';
			if(_quninfo.uid==_user.uid){
				jifen_str = `<li class="bui-btn bui-box">
                        <i class="icon-jiahao"><dd class="fa fa-calendar"></dd></i>
                        <div class="span1 a"  href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/p/signin-index-index/id/${to_id}.html&title=签到领积分">签到</div>
						<div class="span1 a" href="/public/static/libs/bui/pages/frame/show.html?url=/member.php/member/plugin/execute/plugin_name/signin/plugin_controller/manage/plugin_action/set/ext_id/${to_id}.html&title=签到设置">设置</div>
                    </li>`;
				
				home_str = `<li class="bui-btn bui-box">
                        <i class="icon-jiahao"><i class="si si-support"></i></i>
                        <div class="span1 a" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/qun/show-${to_id}.html&title=圈子主页">主页</div>
						<div class="span1 a" href="/public/static/libs/bui/pages/frame/show.html?url=/member.php/qun/content/edit/id/${to_id}.html&title=签到设置">设置</div>
                    </li>`;

				listmember_str = `<li class="bui-btn bui-box">
                        <i class="icon-jiahao"><i class="si si-users"></i></i>
                        <div class="span1 a" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/qun/member/index/id/${to_id}.html&title=成员列表">成员</div>
						<div class="span1 a" href="/public/static/libs/bui/pages/frame/show.html?url=/member.php/qun/member/index/id/${to_id}.html&title=成员管理">管理</div>
                    </li>`;

				more_str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/member.php/qun/msgtask/add/id/${to_id}.html&title=群发消息">
							<i class="icon-jiahao"><i class="si si-speech"></i></i>
							<div class="span1">群发消息</div>
						</li>
						<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/qun/content/my/type/0.html&title=我加入的圈子">
							<i class="icon-jiahao"><i class="fa fa-connectdevelop"></i></i>
							<div class="span1">我加入的圈子</div>
						</li>
					`;
			}else{
				if(_quser==''){
					join_str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/qun/content/apply/id/${to_id}.html&title=加入圈子">
                        <i class="icon-jiahao"><i class="fa fa-child"></i></i>
                        <div class="span1">加入圈子</div>
                    </li>`;
				}
				jifen_str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/p/signin-index-index/id/${to_id}.html&title=签到领积分">
                        <i class="icon-jiahao"><dd class="fa fa-calendar"></dd></i>
                        <div class="span1">签到领积分</div>
                    </li>`;

				home_str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/qun/show-${to_id}.html&title=圈子主页">
                        <i class="icon-jiahao"><i class="si si-support"></i></i>
                        <div class="span1">圈子主页</div>
                    </li>`;

				listmember_str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/qun/member/index/id/${to_id}.html&title=成员列表">
                        <i class="icon-jiahao"><i class="si si-users"></i></i>
                        <div class="span1">圈子成员列表</div>
                    </li>`;
				listmember_str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/qun/member/index/id/${to_id}.html&title=成员列表">
                        <i class="icon-jiahao"><i class="si si-users"></i></i>
                        <div class="span1">圈子成员列表</div>
                    </li>`;

				more_str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/chat/chat?uid=${_quninfo.uid}">
                        <i class="icon-jiahao"><i class="si si-speech"></i></i>
                        <div class="span1">与群主私聊</div>
                    </li>
					<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/qun/content/my/type/1.html&title=我的圈子">
							<i class="icon-jiahao"><i class="fa fa-connectdevelop"></i></i>
							<div class="span1">我的圈子(创建)</div>
						</li>`;
			}

			if(_quser!=''){
				nickname_str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/chat/nickname?id=${to_id}">
                        <i class="icon-jiahao"><i class="fa fa-pencil-square-o"></i></i>
                        <div class="span1">修改群内昵称</div>
                    </li>`;
			}

			str = `${home_str} 
					${join_str}
                    <li class="bui-btn bui-box" href="/public/static/libs/bui/pages/chat/codeimg.html?type=msg&id=${to_id}">
                        <i class="icon-jiahao">&#xe657;</i>
                        <div class="span1">直播群聊二维码</div>
                    </li>
					<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/chat/codeimg.html?type=home&id=${to_id}">
                        <i class="icon-jiahao">&#xe657;</i>
                        <div class="span1">圈子主页二维码</div>
                    </li>
					${listmember_str}
					${jifen_str}
					${more_str}
					${nickname_str}
					`;
		}
		if(_user.uid>0){
			str += `<li class="bui-btn bui-box">
                        <i class="icon-jiahao"><i class="fa fa-user-circle-o"></i></i>
                        <div class="span2 a" href="/public/static/libs/bui/pages/frame/show.html?url=/member.php/home/${_user.uid}.html&title=我的主页">我的主页</div>
						<div class="span1 a" href="/public/static/libs/bui/pages/frame/show.html?url=/member.php/member/user/edit.html&title=资料修改">设置</div>
                    </li>`;
		}
		
		router.$("#TopRightBtn").html(str);
	}

	/*
	var vues = new Vue({
				el: '#chat_head',
				data: {
					from_id: typeof(to_uid)!='undefined'?to_uid:"",
					to_id:0,
					me_id:0,
					userdb:{},
					quninfo:{},
					listdb:[],
				},
				watch:{
					listdb: function() {
						this.$nextTick(function(){	//数据渲染完毕才执行
							if(show_msg_page==2 || router.$('#chat_win').parent().scrollTop()>300){
								router.$('#chat_win').parent().scrollTop(20000);
							}else{
								router.$('#chat_win').parent().scrollTop(200);
							}
							add_btn_delmsg();
						})
					},
				},
				methods: {
					set_id:function(id){
						this.to_id = id;
					},
					set_quninfo:function(o){
						this.quninfo = Object.assign({}, this.quninfo, o);
					},
					set_data:function(array,type){
						var ar = this.listdb;
						var userinfo = {};
						var that = this;
						var timer = setInterval(function() {							
							if(typeof(userinfo.uid)=='undefined'){
								userinfo = window.store.get('userinfo');
							}
							if(typeof(userinfo.uid)!='undefined'){								
								that.me_id = typeof(my_uid)!='undefined'?my_uid:userinfo.uid;
								that.userdb = Object.assign({}, that.userdb, userinfo);
								if(type=='new'){	//有新数据进来的话,要重新处理排序
									that.listdb = [];
									array.forEach((rs)=>{
										that.listdb.push(rs);
									});
									ar.forEach((rs)=>{
										that.listdb.push(rs);
									});
								}else{	//多页的话,直接追加就好了
									array.forEach((rs)=>{
										that.listdb.push(rs);
									});
								}								
								clearInterval(timer);
							}
							console.log('userinfo=',userinfo);
						},500);
					},
				}		  
			});
	*/
	
	//微信分享
	pageview.weixin_share = function(){
		if(typeof(wx)=='object' && have_load_wx_config==true && uid<0){
			weixin_share({
				title:quninfo.title!=''?quninfo.title:'欢迎加入圈子群聊',
				about:quninfo.content!=''?quninfo.content.replace('&nbsp;',''):'欢迎加入圈子群聊,不错过每一个精彩的话题!',
				picurl:quninfo.picurl!=''?quninfo.picurl:'',
				url:"/index.php/index/msg/index.html#/public/static/libs/bui/pages/chat/chat?uid="+uid,
			});
		}		
	}


		//格式化各频道的数据
	function format_hack_data(){
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
	
	//获取所有成员信息
	pageview.get_user_list = function(id){
		$.get("/index.php/qun/wxapp.member/get_member.html?id="+id+"&rows=1000&order=update_time&get_username=0",function(res){
			if(res.code==0){
				res.data.forEach((rs)=>{
					user_list[rs.uid] = rs;
				});
				user_num = res.data.length;
				format_nickname();				
			}
		});
	}

	function format_nickname(){
		if(uid>0 || user_num<1){
			return ;
		}
		router.$("#chat_win .show_username").each(function(){
			var _uid = $(this).data('uid');
			if(typeof(user_list[_uid]) == 'object' && typeof(user_list[_uid].nickname)!='undefined' && user_list[_uid].nickname!=''){
				$(this).html(user_list[_uid].nickname);
			}
		});
	}
	
	//CMS音频直播
	function vod_voice_play(obj,etype){
		var oo;
		if(etype=='ok'||etype=='err'){	//请求完成
			var url_array = obj.play_urls;
			if(etype=='ok'){
				oo = {index:obj.play_index,time:obj.play_time}
			}
		}else{	//请求直播信息
			voice_urls = obj.urls;
			var url_array = obj.urls;
			var timer = setInterval(function() {
				if(clientId!=''){
					clearInterval(timer);
					w_s.send('{"type":"user_ask_quner","tag":"ask_vod_voice_state","user_cid":"'+clientId+'"}');	//向圈主请求当前播放信息
				}	
			}, 1000);
			return ;
		}	
		
		layer.open({  
			  type: 2,    
			  title: false,  
			  fix: false,  
			  shadeClose: false,  
			  offset: ['73px', '0'],
			  shade: 0,
			  maxmin: true,
			  scrollbar: false,
			  closeBtn:2,  
			  area: ['100%', my_uid==quninfo.uid?'100px':'50px'],  
			  content: "/public/static/libs/bui/pages/chat/vod_player.html?aid="+Math.abs(uid)+"&cid="+clientId,
			  success: function(layero, index){  
				var body = layer.getChildFrame('body', index);  //body.find('#dd').append('ff');    
				voice_iframeWin = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：iframeWin.method();  
				voice_iframeWin.voice_player(url_array,oo);
				//$('.layui-layer-min').trigger("click");
				setTimeout(function(){
					//$('.layui-layer-max').trigger("click");
					//body.find(".player-button").hide();
				},3000);	
				if(my_uid==quninfo.uid){
					setTimeout(function(){
						body.find('.syscn').show();
						body.find('.jp-previous').show();
						body.find('.jp-next').show();
						body.find('.jp-play').off('click');
					},2000);
				}
			  }
		});
	}
	
	var getParams = bui.getPageParams();
    getParams.done(function(result){
		console.log("当前用户ID-"+result.uid);
		if(result.uid!=undefined){
			qid = uid = result.uid;
			//vues.set_id(uid);
			//head_menu(uid);	//设置菜单
			show_msg_page = 1; //重新恢复第一页
			msg_scroll = 1; //恢复可以使用滚动条			
			showMoreMsg(uid);	//加载相应用户的聊天记录
			if(uid<0){	//群聊,获取群信息
				/*
				$.get("/index.php/qun/wxapp.qun/getbyid.html?id="+(-uid),function(res){
					if(res.code==0){
						quninfo = res.data
						window.store.set("quninfo",quninfo);
						vues.set_quninfo(quninfo);
						$("#send_user_name").html(res.data.title);
						console.log('quninfo=',quninfo);
					}
				});
				*/
				pageview.get_user_list(-uid);
			}else{	//私聊
				console.log("当前用户UID-"+uid);
				set_user_name(uid);	//设置当前会话的用户名
			}
		}


    })

    // 初始化
    pageview.init();

    // 输出模块
    return pageview;

})
