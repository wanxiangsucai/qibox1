//这个函数将弃用,以前框架APP时候用的
function apk_recode_end(url){
	//layer.msg(url);	
	$.post("/member.php/member/wxapp.msg/add.html",{
		content:'<audio controls="controls"><source src="'+url+'" type="audio/mp3" />不支持的浏览器</audio>',
		uid:uid,
		},function(res){
			refresh_timenum = 1;	//加快刷新时间
			if(res.code==0){
				layer.msg('发送成功');
			}else{
				layer.alert(res.msg);
			}
	});
}


//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.sound = {

	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		var str = `<link rel="stylesheet" href="/public/static/libs/bui/pages/sound/style.css" />
				<div class="sound_warp">
					<ul class="voicemenu">
							<div id="change_word_btn"><i class="fa fa-list"></i></div>
							<div id="voiceBtn">按住说话</div>
					</ul>
				</div>`;
		router.$(".chat_mod_btn").after(str);

		router.$("#btn_sound").click(function(){
			if(typeof(wx)=='undefined' && typeof(window.inApk)!='object'){
				bui.hint('只有在APP或微信中才能使用语音聊天!');
				return ;
			}
			router.$(".chatbar>div").hide();
			router.$(".sound_warp").show();
		});

		router.$('#change_word_btn').click(function(){
			router.$(".sound_warp").hide();
			router.$(".bui-input").show();		
		});

		var btnRecord = router.$('#voiceBtn');
		btnRecord.on('touchstart', function(event) {
			event.preventDefault();
			btnRecord.addClass('re_cord');	
			startTime = new Date().getTime();
			// 延时后录音，避免误操作
			recordTimer = setTimeout(function() {
				layer.msg('请不要松手,录音正在进行中...',{time:60000});
				start_recode();	//执行录音
			}, 300);
		}).on('touchend', function(event) {
			event.preventDefault();
			btnRecord.removeClass('re_cord');	
			// 间隔太短
			if (new Date().getTime() - startTime < 300) {
				startTime = 0;
				// 不录音
				clearTimeout(recordTimer);
				layer.alert('请按住不要松开,才能继续录音');
			} else { // 松手结束录音
				layer.closeAll();
				layer.msg('录音完毕,上传中...');
				stop_recode(); //录音结束
			}
		});

		if(typeof(wx)!='undefined'){
			wx.onVoiceRecordEnd({
				complete: function (res) {
				  //voice.localId = res.localId;
				  layer.alert('每次录音不得超过一分钟');
				}
			});
		}


		function start_recode(){
			if(typeof(window.inApk)=='object'){
				if(token==''){
					layer.alert("你还没登录");
				}else{
					window.inApk.voice_record(token);
				}
			}else{
				wx_record_start();
			}
		}

		function stop_recode(){
			if(typeof(window.inApk)=='object'){
				if(token!=''){
					window.inApk.voice_record("end");
					//异步还要执行一个全局函数,因为全局函数才能被APP调用
				}
			}else{
				wx_record_end();
			}
		}

		function wx_record_start(){
			wx.startRecord({
				success: function() {
				},
				cancel: function() {
					layer.alert('你拒绝了授权录音');
				}
			});
		}

		function wx_record_end(){
			wx.stopRecord({
				success:function(res) {
							//voice.localId = res.localId;				
							//tran_record();
						wx.playVoice({	//播放录音
							localId: res.localId,
						});

						wx.uploadVoice({
							localId: res.localId,
							success: function (res) {
								//layer.alert(' 上传语音成功，serverId 为' + res.serverId);
								$.post("/member.php/member/wxapp.msg/add.html",{voiceid:res.serverId,uid:uid},function(res){
									refresh_timenum = 1;	//加快刷新时间
									if(res.code==0){
										layer.msg('发送成功');
									}else{
										layer.alert(res.msg);
									}
								});
							  }
						});
				},
				fail:function(res) {
						layer.alert('录音无法识别,请重新按住录音!'+JSON.stringify(res));
				}
			});
		}
	},
	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){  //init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
	},

}

