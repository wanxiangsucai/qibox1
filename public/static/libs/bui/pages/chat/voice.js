//要定义一个全局函数给APP回调
function apk_recode_end(url){
	//layer.msg(url);	
	$.post("/member.php/member/wxapp.msg/add.html",{
		content:'<audio controls="controls"><source src="'+url+'" type="audio/mp3" />不支持的浏览器</audio>',
		uid:send_to_uid,
		},function(res){
			refresh_timenum = 1;	//加快刷新时间
			if(res.code==0){
				layer.msg('发送成功');
			}else{
				layer.alert(res.msg);
			}
	});
}
var send_to_uid;

// 默认已经定义了main模块
loader.define(function() {

    var pageview = {};
	var uid;
	var voice = {
		localId: '',
		serverId: ''
	  };
	var in_qbapp = false;
	var token = "";

 

    // 主要业务初始化
    pageview.init = function() {
        // 这里写main模块的业务
		//var option = router.option();
		//console.log("dd"+uid);
        //bui.hint("loadPart_part.js was loaded="+uid);
		console.log("语音模块");
		

		if(typeof(window.inApk)=='object'){		//在APP中访问网站
			in_qbapp = true;
			$.get("/index.php/index/ajax/get_token.html",function(res){
				if(res.code==0){
					token = res.data;
				}else{
					console.log('TOKEN获取失败,'+res.msg);
				}
			});
		}

		if(typeof(wx)=='undefined' && in_qbapp==false){
			router.$('#choose_voice_btn').click(function(){
				bui.hint('只有在APP或微信中才能录音!');
			});
			return ;
		}
		
		router.$('#choose_voice_btn').click(function(){
			console.log("#choose_voice_btn");
			router.$(".more_hack .bmenu").hide();
			router.$(".bui-input").hide();
			router.$(".more_hack .voicemenu").show();			
		});

		router.$('#change_word_btn').click(function(){
			router.$(".more_hack .voicemenu").hide();
			router.$(".more_hack .bmenu").show();
			router.$(".bui-input").show();
			router.$(".hack_wrap").hide();
			
		})


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

		wx.onVoiceRecordEnd({
			complete: function (res) {
			  //voice.localId = res.localId;
			  layer.alert('每次录音不得超过一分钟');
			}
		});

    }

	function start_recode(){
		if(in_qbapp==true){
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
		if(in_qbapp==true){
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

    // 事件绑定
    pageview.bind = function() {
    }
	

	var getParams = bui.getPageParams();
    getParams.done(function(result){
		uid = result.uid; 
		send_to_uid = uid;
    })

    // 初始化
    pageview.init();
    // 绑定事件
    pageview.bind();
    
    return pageview;
})

