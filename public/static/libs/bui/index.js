window.router = bui.router();
window.loader = bui.loader({cache: false});	//false代表不要缓存

bui.ready(function(){
	window.store = bui.store({
				scope: "app",
				isPublic: true,
				data: {
					userinfo: {username:"我的",uid:my_uid},
				},
				mounted: function () {
					$.get(get_user_info_url+"?uid="+my_uid,function(res){
						//console.log(res.data);
						if(res.code==0){
							res.data.face = "<img class='ring userface' onerror=\"this.src='/public/static/images/noface.png'\" src='"+res.data.icon+"'>";
							window.store.set("userinfo",res.data);
						}else{
							window.store.set("userinfo",{username:"游客",uid:0});
						}						
					});
				}
    });

	var map = {
          moduleName: "main",
          template: "/public/static/libs/bui/pages/main/main.html",
          script: "/public/static/libs/bui/pages/main/main.js"
    }

	if(to_uid!=""){ //查看短消息详情页或者发送短消息
		map = {
          moduleName: "main",
          template: "/public/static/libs/bui/pages/chat/chat.html",
          script: "/public/static/libs/bui/pages/chat/chat.js"
		}
	}


	loader.map(map)

    // 初始化路由
    router.init({
			id: "#bui-router",
			progress: true,
			hash: true,
			store: store,
			cache: false,	//false代表不要缓存
    });
	
	//检查新消息的条数
	function check_newmsg_num(){
		if(typeof(checkNewMsgUrl)=='undefined'){
			return ;
		}
		$.get(checkNewMsgUrl,function(res){
			if(res.code==0){
				$("#chat_num").html(res.data.num);	
				$("#chat_num").show();
			}else{
				$("#chat_num").hide();
			}
		})
	}

	function tongji_new_num(type){
		if(typeof(tongjiAllCountUrl)=='undefined'){
			return ;
		}
		$.get(tongjiAllCountUrl+type,function(res){
			if(res.code==0){
				if(res.data>0){
					$(".tongji_num").html(res.data>999?'99+':res.data);	
					$(".tongji_num").show();
				}
			}
		})
	}
	
	var i=0;
	setInterval(function() {		
		var url = window.location.href;
		if(url.indexOf('#/')==-1){	//跳转到其它模块,就不要刷新了
			if(i%3==0)check_newmsg_num();
			if(i%5==0)tongji_new_num(0);
			if(to_uid!=""){		//指定了id , 一般是从公众号进入的情况
				if(i%4==0)tongji_new_num(1);
			}
		}
		i++;
	}, 1000);
		

	//loader.set("main",{
     //      template: "pages/login/login.html",
     //      script: "pages/login/login.html"
     //  })

		

    // 绑定事件
    bind();

})

// 事件类定义
function bind() {
    // 绑定页面的所有按钮有href跳转
    bui.btn({id:"#bui-router",handle:".bui-btn,.a"}).load();

    // 统一绑定页面所有的后退按钮
    $("#bui-router").on("click",".btn-back",function (e) {
        // 支持后退多层,支持回调
        bui.back();
    });

	$("#bui-router").on("click",".iframe",function (e) {
        // 框架打开
		var url = $(this).attr('href');
		var title = typeof($(this).attr('title'))!='undefined'?$(this).attr('title'):$(this).html();
		if(url!=undefined){
			bui.load({ url: "/public/static/libs/bui/pages/frame/show.html",param:{url:url,title:title}});
			return false;
		}
    });
}


function weixin_share(info){
	if(typeof(have_load_wx_config)=="undefined"){
		return ;
	}else if(have_load_wx_config==false){
		return ;
	}

	if( (info.url).indexOf('://')==-1 ){
		var urls = window.location.href;
		var ar = urls.split('/');
		info.url = ar[0] + '//' + ar[2] + info.url;
	}
	if(typeof(info.picurl)!="undefined" && info.picurl!='' && (info.picurl).indexOf('://')==-1 ){
		var urls = window.location.href;
		var ar = urls.split('/');
		info.picurl = ar[0] + '//' + ar[2] + info.picurl;
	}
    wx.onMenuShareTimeline({
      title: info.title+info.about,
      link: info.url,
      imgUrl: info.picurl,
      trigger: function (res) {
      },
      success: function (res) {
        layer.msg('成功分享到朋友圈');
      },
      cancel: function (res) {
        //alert('系统监测到你没有真正分享到朋友圈，请重新分享！');
      },
      fail: function (res) {
        alert(JSON.stringify(res));
      }
    });

	wx.onMenuShareAppMessage({
      title: info.title,
      desc: info.about,
      link: info.url,
      imgUrl: info.picurl,
      trigger: function (res) {
      },
      success: function (res) {
		  layer.msg('成功分享到好友');
      },
      cancel: function (res) {
      },
      fail: function (res) {
        alert(JSON.stringify(res));
      }
    });
}