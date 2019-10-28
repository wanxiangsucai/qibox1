function qq_login(){
	if(typeof(window.inApk)=='object'){	//套壳APP登录
		window.inApk.app_qq_login(jump_from_url);
	}else if(typeof(api)=="object"){	//仿原生APP登录
		apicloudQqLogin();
	}else{
		window.location.href = qq_login_url;
	}
}

//仿原生APP之QQ登录
function apicloudQqLogin(){
	var qiboBase = api.require('qiboBase');
	var param = {appParam:"Hello APICloud!"};
	var resultCallback = function(ret, err){
		layer.msg("请稍候,正在检验数据...",{time:5000});
		var url = ck_qqlogin_url + "access_token=" + ret.access + "&openid=" + ret.openid;
		$.get(url,function(res){
			if(res.code==1){
				layer.alert(res.msg);
			}else{	//检验资料正确,ajax跨域无法同步登录
				layer.msg("登录成功");
				setTimeout(function(){
					window.location.href = decodeURIComponent(jump_from_url);
				},500);
			}
		});
	}
	qiboBase.qqLogin(param,resultCallback);
}

function weixin_login(){
	if(typeof(window.inApk)=='object'){	//套壳APP登录
		window.inApk.app_weixin_login(jump_from_url);
	}else if(typeof(api)=="object"){	//仿原生APP登录
		apicloudWxLogin();
	}else{
		window.location.href = wx_login_url;
	}
}

//仿原生APP之微信登录
function apicloudWxLogin(){
	var qiboBase = api.require('qiboBase');
	var param = {appParam:"Hello APICloud!"};
	var resultCallback = function(ret, err){
		layer.msg("请稍候,正在检验数据...",{time:5000});
		var url = ck_wxlogin_url + "token=" + ret.token;
		$.get(url,function(res){
			if(res.code==1){
				layer.alert(res.msg);
			}else{	//检验资料正确,ajax跨域无法同步登录
				layer.msg("登录成功");
				setTimeout(function(){
					window.location.href = decodeURIComponent(jump_from_url);
				},500);
			}
		});
	}
	qiboBase.wxLogin(param,resultCallback);
}