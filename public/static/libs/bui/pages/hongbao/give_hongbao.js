loader.define(function() {
	var pageview = {};
	var uid='';
	var userdb = window.store.get('userinfo');

	pageview.init = function () {

		if(userdb.rmb==undefined){	//充值刷新过网页的情况
			setTimeout(function(){				
				userdb = window.store.get('userinfo');
				router.$("#user_rmb").html(userdb.rmb);
			},1000);
		}
		bui.input({
            id: ".user-input",
            callback: function (e) {
                // 清空数据
                this.empty();
            }
        });
		
		router.$("#user_rmb").html(userdb.rmb);
		
		//确认打赏
		router.$("#giveBtn").click(function(){			
			if( router.$(".hongbao_warp input[name='money']").val()<0.01 ){
				layer.alert("单个最小红包不能小于0.01元");
				return ;
			}else if( router.$(".hongbao_warp input[name='num']").val()<1 ){
				layer.alert("红包个数必须要1个起");
				return ;
			}
			var total = router.$(".hongbao_warp input[name='num']").val() * router.$(".hongbao_warp input[name='money']").val();
			if( router.$(".hongbao_warp input[name='ifrand']:checked").val()==1 ){
				if(  router.$(".hongbao_warp input[name='totalmoney']").val()=='' ){
					layer.alert("红包总金额不能为空!");
					return ;
				}				
				if(router.$(".hongbao_warp input[name='totalmoney']").val()<total){
					layer.alert("红包总金额不能小于"+total+"元!");
					return ;
				}
				total = router.$(".hongbao_warp input[name='totalmoney']").val();
			}			
			if( router.$(".hongbao_warp input[name='title']").val()=='' ){
				router.$(".hongbao_warp input[name='title']").val('恭喜发大财')
			}
			if(total>userdb.rmb){
				//layer.alert("你的可用余额只有"+userdb.rmb+"元,请先充值");
			}
			Qibo.post('/index.php/p/hongbao-wxapp.post-add.html?ext_id='+(-uid),$('.hongbao_warp').serialize(),function(res){
				if(res.code==0){
							if(typeof(refresh_timenum)!='undefined')refresh_timenum = 1;	//加快刷新时间
							if(res.code==0){
								layer.msg(res.msg);
								if( typeof(refresh_timenum)=='undefined' ){ //可能是充值过来时刷新过网页
									bui.load({ 
										url: "/public/static/libs/bui/pages/chat/chat.html",
										param:{
											uid:uid,
										}
									});
								}else{
									bui.back();
								}
							}else{
								layer.alert(res.msg);
							}
				}else{
					layer.alert(res.msg);
				}
			});
		});

		router.$(".hongbao_warp input[name='ifrand']").click(function(){
			choose_type();
		});
		
		//充值
		router.$("#addrmb").click(function(){
			bui.load({ 
				url: "/public/static/libs/bui/pages/frame/show.html",
				param:{
					url:"/member.php/member/plugin/execute/plugin_name/marketing/plugin_controller/rmb/plugin_action/add.html?callback_url="+encodeURIComponent(window.location.href),
				}
			});
		});
	}

	function choose_type(){
		var ifrand = router.$(".hongbao_warp input[name='ifrand']:checked").val();
		if(ifrand==0){
			router.$("#totalmoney_warp").hide();
			router.$(".hongbao_warp input[name='totalmoney']").val('');
		}else{
			router.$("#totalmoney_warp").show();
		}
	}

	var getParams = bui.getPageParams();
	getParams.done(function(result){
		uid = result.uid;
	});

	pageview.init();
})