bui.ready(function(){
	var pageview = {};
	var oldurl = '';
	var oldtime = 0;

	function check_if_open(url){
		if(oldurl == url && (new Date().getTime()-oldtime)<2000 ){	//重复点击了两次并且小于2秒，判断为超过小程序最多的10层页面栈
			wx.miniProgram.reLaunch( { url: "/pages/hy/web/index?url="+url} );
		}
		oldtime = new Date().getTime();
		oldurl = url;
	}

	function wxapp_open_win(url){
		if(!url){
			return ;
		}
		if(url.indexOf('https://')!=0 && url.indexOf('http://')!=0){
			url = web_domain+url;
		}
		url = encodeURIComponent(url);
		wx.miniProgram.navigateTo( { url: "/pages/hy/web/index?url="+url,success:function(){
			check_if_open(url);
		} } );
	}

    // 模块初始化定义    
    pageview.init = function () {

		if(inWxapp){
			$(".bui-btn").unbind('click').click(function(){
				var url = $(this).attr("href");
				wxapp_open_win(url);
				return false;
			});
			$(".a").unbind('click').click(function(){
				var url = $(this).attr("href");
				wxapp_open_win(url);
				return false;
			});
		}else{
			bui.btn({id:".bui-page",handle:".bui-btn,.a"}).load();
			bui.btn({id:".rolltype",handle:".bui-btn,.a"}).load();
		}
		setTimeout(function(){
			if(typeof(api)=='object'){
				$(".bui-btn").click(function(){
					var url = $(this).attr("href");
					if(url)Qibo.open(web_domain+url,'会员中心',win_name)
					return false;
				});
				$(".a").click(function(){
					var url = $(this).attr("href");
					if(url)Qibo.open(web_domain+url,'会员中心',win_name)
					return false;
				});
			}
		},800);

		$(".fullbg").click(function(){
			hide_nav($('#editmodes'),$('.fullbg'));
		});

		$(".show-more-menu").click(function(){
			var str = $(this).next().html();
			showEditMode(str);
		});

    }



	function showEditMode(str){
		$('#editmodes').html(str);
		show_nav($('#editmodes'),$('.fullbg'));
		var height = $("main").height();
		var header_h = $("header").height();
		$('.fullbg').css({height:height,top:header_h});
		$('#editmodes').css({height:height,top:header_h});
	}
	function show_nav(node,fullbg){
		fullbg.css({'display':'block'}).stop().animate({'opacity':.6},200,function(){
			node.stop().animate({'width':'200px','padding':'0px 10px 0 10px'},100);
		});
		
		if(inWxapp){
			$(".morelink").unbind('click').click(function(){
				var url = $(this).attr("href");
				wxapp_open_win(url);
				return false;
			});
		}else if(typeof(api)=='object'){
			$(".morelink").unbind('click').click(function(){
				var url = $(this).attr("href");
				Qibo.open(web_domain+url,'会员中心',win_name)
				return false;
			});
		}
		
	}
	function hide_nav(node,fullbg){
		fullbg.animate({'opacity':0},200,function(){
			$(this).css({'display':'none'});
		});
		node.stop().animate({'width':'0px','padding':'0px 0px 0 0px'},200);
	}

	 // 初始化
    pageview.init();
    
    // 输出模块
    return pageview;
})