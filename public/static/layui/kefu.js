var LayIm;
if(WS.my_uid()<1 || WS.guest_id()>0){
	let kefu_list = [];
	KF.kefu_list.forEach(function(rs,id){
		kefu_list.push({
			id:id,
			username:rs.name,
			avatar:rs.icon?rs.icon:'/public/static/images/noface.png',
			sign:rs.sign ? rs.sign : '顾客至上，用心服务',
		});
	});
	KF.layui_init = {
		mine: {
			username:'访客',
			id: WS.guest_id(),
			status:'online',
			sign:'欢迎你的到来!',
			avatar:'/public/static/images/noface.png',
		},
		friend: [{
			groupname:'客服列表',
			id:1,
			online:2,
			list:kefu_list,
		},{
			groupname:'交流合作',
			id:2,
			online:0,
			list:[],
		}],
		group: [],
	};
}else{
	KF.layui_init = {
		url: '/index.php/index/wxapp.layim/msg_user_list.html'
		,data: {}
	};
}


layui.use('layim', function(layim){
	layim.config({
		//初始化接口		
		init: KF.layui_init

		//查看群员接口
		,members: {
			url: '/json/getMembers.json'
			,data: {}
		}
			//上传图片接口
			,uploadImage: false
			
			//上传文件接口
			,uploadFile: false
			,isAudio: false //开启聊天工具栏音频
			,isVideo: false //开启聊天工具栏视频
			
			//扩展工具栏
			,tool: false
			
			//,brief: true //是否简约模式（若开启则不显示主面板）
			
			,title: '在线客服' //自定义主面板最小化时的标题
			//,right: '100px' //主面板相对浏览器右侧距离
			//,minRight: '90px' //聊天面板最小化时相对浏览器右侧距离
			,initSkin: '2.jpg' //1-5 设置初始背景
			//,skin: ['aaa.jpg'] //新增皮肤
			//,isfriend: false //是否开启好友
			,isgroup: (WS.my_uid()<1 || WS.guest_id()>0)?false:true //是否开启群组
			,min: true //是否始终最小化主面板，默认false
			,notice: true //是否开启桌面消息提醒，默认false
			//,voice: false //声音提醒，默认开启，声音文件为：default.mp3
			
			//,msgbox: layui.cache.dir + 'css/modules/layim/html/msgbox.html' //消息盒子页面地址，若不开启，剔除该项即可
			//,find: layui.cache.dir + 'css/modules/layim/html/find.html' //发现页面地址，若不开启，剔除该项即可
			,chatLog: layui.cache.dir + 'css/modules/layim/html/chatlog.html' //聊天记录页面地址，若不开启，剔除该项即可
			
	});

	//监听layim建立就绪
	layim.on('ready', function(res){
		if(WS.my_uid()>0 && WS.my_uid()<9999999){
			KF.kefu_list.forEach(function(rs,id){
				layim.addList({
				  type: 'friend'
				  ,avatar: rs.icon?rs.icon:'/public/static/images/noface.png'
				  ,username: rs.name
				  ,groupid: 1
				  ,id: id
				  ,remark: rs.sign ? rs.sign : '顾客至上，用心服务'
				});
			});

		}
		
	});

	//监听发送消息
	layim.on('sendMessage', function(data){
		console.log(data);
		var To = data.to;
		if(To.type === 'friend'){
		  layim.setChatStatus('<span style="color:#FF5722;">对方正在输入。。。</span>');
		}

		WS.postmsg({
			content:data.mine.content,
			uid:data.to.id,
		});
	});


	//监听聊天窗口的切换
	  layim.on('chatChange', function(res){
		var type = res.data.type;
		uid = res.data.id;
		console.log('切换了窗口',uid);
		var str = $.cookie('layim_msg_id');
		if(str && str.indexOf(","+uid+",")>-1){
			return ;
		}
		str = str ? str+uid+"," : ","+uid+"," ;
		$.cookie('layim_msg_id', str, { expires: 30, path: '/' });
		
		var index = layer.msg("请稍候,正在加载数据...");
		$.get("/index.php/index.php/index/wxapp.layim/get_more_msg.html?uid="+uid,function(res){
				layer.close(index);
				if(res.code==0){
					for(var i=res.data.length-1;i>=0;i--){
						var rs = res.data[i];
						layim.getMessage({
							username: rs.from_username
							,avatar: rs.from_icon
							,id: uid
							,type: type //"friend"
							,mine:WS.my_uid()==rs.uid?true:false
							,content: rs.content
						});
					}
				}else{
					layer.msg('没有任何聊天记录!');
				}
		});

		if(type === 'friend'){			
		  //模拟标注好友状态
		  layim.setChatStatus('<span style="color:#FF5722;">在线</span>');
		} else if(type === 'group'){
		  //模拟系统消息
		  /*
		  layim.getMessage({
			system: true
			,id: res.data.id
			,type: "group"
			,content: '模拟群员'+(Math.random()*100|0) + '加入群聊'
		  });*/
		}
	  });

	LayIm = layim;
});


//接收各种WS的消息处理
WS.onmsg(function(obj){
	if(obj.type=='have_new_msg' || obj.type=='qun_sync_msg'){
		var data;
		if(obj.type=='qun_sync_msg'){	//兼容群聊的模式
			data = obj.data[0];
			if(data.uid==WS.my_uid()){
				return ;
			}
		}else{	//私信的专有模式
			data = obj.data.msgdata;
		}		
		LayIm.getMessage({
			username: data.from_username
			,avatar: data.from_icon==''?'/public/static/images/noface.png':data.from_icon
			,id: data.uid
			,type: data.qun_id?"group":"friend"
			,content: data.content
      });
	}
});


//重置会话窗口
KF.chat_win = function(touid){
	if(!touid){
		$(".layui-layim-min").trigger("click");
		return ;
	}
	layui.layim.setFriendStatus(touid, 'online');
	let username,user = WS.user_db(touid);
	if(user){
		username = user.name;
	}else if(touid==WS.kefu()){
		username = '客服MM';
	}else if(WS.my_uid()==WS.kefu()){
		username = '客户';
	}else{
		username = '网友';
	}console.log('dddddd',user);
	LayIm.chat({
		name: username
		,type: 'friend'
		,avatar: user&&user.icon ? user.icon : '/public/static/images/noface.png'
		,id: touid,
	});
}