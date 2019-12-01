// 默认已经定义了main模块
loader.define(function() {

    var pageview = {};

    // 主要业务初始化
    pageview.init = function() {
		//loader.require("public/static/libs/ckplayer/ckplayer",function () {});
		loader.require("/public/static/libs/ckplayer/hls/hls.min",function () {});
    }
	
	//此方法弃用
	pageview.bind = function() {
		router.$(".chat-content .live_video_player").each(function(i){
			var flv_url = $(this).data('flv');
			var m3u8_url = $(this).data('m3u8');
			/*
			$(this).addClass("live_video_player_"+i);
			var videoObject = {
                		container: '.'+"live_video_player_"+i, //“#”代表容器的ID，“.”或“”代表容器的class
                		variable: 'player1',  //该属性必需设置，值等于下面的new chplayer()的对象
                		//poster:'pic/wdm.jpg',//封面图片
                        //loaded: 'loadedHandler1', //当播放器加载后执行的函数	
                		video:{
							file:m3u8_url,   //视频地址
							type:'video/m3u8'
						},isM3u8:true,
             };
             var player1 = new ckplayer(videoObject);
			 */
			 $(this).html('<video id="video'+i+'" controls loop="false" width="100%"></video>');
			 var video = document.getElementById('video'+i);
			  if(Hls.isSupported()) {
				var hls = new Hls();
				hls.loadSource(m3u8_url);
				hls.attachMedia(video);
				hls.on(Hls.Events.MANIFEST_PARSED,function() {
				  video.play();
			  });
			 } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
				video.src = m3u8_url;
				video.addEventListener('loadedmetadata',function() {
				  video.play();
				});
			  }
		});
    }


	pageview.play = function(urls) {
		var m3u8_url = urls.m3u8_url;
		var rtmp_url = urls.rtmp_url;
		var flv_url = urls.flv_url;
		//router.$("#chat_win .qun-live-video").attr("id","");
		var myDate = new Date();
		var rand = myDate.getTime();
		router.$(".live-player-warp").css('opacity',1);
		router.$(".live-player-warp").html('<video class="qun-live-video" id="qunvideo'+rand+'" controls loop="false" width="100%"></video>');
		var video = document.getElementById('qunvideo'+rand);
		if(Hls.isSupported()) {
			var hls = new Hls();
			hls.loadSource(m3u8_url);
			hls.attachMedia(video);
			hls.on(Hls.Events.MANIFEST_PARSED,function() {
				video.play();
			});
		} else if (video.canPlayType('application/vnd.apple.mpegurl')) {
			video.src = m3u8_url;
			video.addEventListener('loadedmetadata',function() {
				video.play();
			});
		}	
	}

    // 初始化
    pageview.init();
    
    return pageview;
})

