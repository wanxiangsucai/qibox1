// 默认已经定义了main模块
loader.define(function() {

    var pageview = {};
	var uid;
	var voice = {
		localId: '',
		serverId: ''
	  };

 

    // 主要业务初始化
    pageview.init = function() {
        // 这里写main模块的业务
		//var option = router.option();
		//console.log("dd"+uid);
        //bui.hint("loadPart_part.js was loaded="+uid);
		console.log("语音模块");
		if(typeof(wx)=='undefined'){
			router.$('#choose_voice_btn').click(function(){
				bui.hint('在微信中才能录音!');
			});
			return ;
		}
		
		router.$('#choose_voice_btn').click(function(){
			console.log("#choose_voice_btn");
			$(".more_hack .bmenu").hide();
			$(".bui-input").hide();
			$(".more_hack .voicemenu").show();			
		});

		router.$('#change_word_btn').click(function(){
			$(".more_hack .voicemenu").hide();
			$(".more_hack .bmenu").show();
			$(".bui-input").show();
			$("#hack_wrap").hide();
			
		})


		var btnRecord = router.$('#voiceBtn');
		btnRecord.on('touchstart', function(event) {
			event.preventDefault();
			btnRecord.addClass('re_cord');	
			startTime = new Date().getTime();
			// 延时后录音，避免误操作
			recordTimer = setTimeout(function() {
				layer.msg('请不要松手,录音正在进行中...',{time:60000});
				wx.startRecord({
					success: function() {
					},
					cancel: function() {
						layer.alert('你拒绝了授权录音');
					}
				});
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
				wx.stopRecord({
					success: function(res) {
						//voice.localId = res.localId;				
						//tran_record();
						wx.playVoice({
							localId: res.localId,
						});
						wx.uploadVoice({
						  localId: res.localId,
						  success: function (res) {
							//layer.alert(' 上传语音成功，serverId 为' + res.serverId);
							$.post("/member.php/member/wxapp.msg/add.html",{voiceid:res.serverId,uid:uid},function(res){
								if(res.code==0){
									layer.msg('发送成功');
								}else{
									layer.alert(res.msg);
								}
							});
						  }
						});
					},
					fail: function(res) {
						layer.alert('录音无法识别,请重新按住录音!');
						//alert(JSON.stringify(res));
					}
				});
			}
		});

		wx.onVoiceRecordEnd({
			complete: function (res) {
			  //voice.localId = res.localId;
			  layer.alert('每次录音不得超过一分钟');
			}
		});

    }

    // 事件绑定
    pageview.bind = function() {
    }
	

	var getParams = bui.getPageParams();
    getParams.done(function(result){
		uid = result.uid; 
    })

    // 初始化
    pageview.init();
    // 绑定事件
    pageview.bind();
    
    return pageview;
})