// 默认已经定义了main模块
loader.define(function() {

    var pageview = {};
	var id = 0;
	var qun_userinfo = {};
	var userinfo = {};
	var quninfo = {};

	var vues = new Vue({
				el: '.signInBox',
				data: {
					id:0,
					signday:'0',
					imgurl:'',
					listdb:[],
					//quninfo:window.store.get("quninfo"),
				},
				watch:{
					listdb: function() {
						this.$nextTick(function(){	//数据渲染完毕才执行
						})
					},
				},
				methods: {
					set_num:function(num){
						this.signday = num;
					},
					set_data:function(array){
						array.forEach((rs)=>{
							this.listdb.push(rs);
						});
					},
				}		  
			});

    // 主要业务初始化
    pageview.init = function() {// 这里写main模块的业务
		router.$("#signInBtn").click(function(){
			if(typeof(userinfo.uid)=='undefined'){
				layer.confirm("你还没登录,是否立即登录?",{btn:['立即登录','取消']},function(){
					window.location.href = "/index.php/index/login/index.html?fromurl="+encodeURIComponent(window.location.href);
				});
			}else if(qun_userinfo==''){	//不是本圈内成员 注意:如果跳转到了其它申请页面,再返回来的话,这里的值有必要重新获取一次.不然还是空的
				if(quninfo._autoyz==1){	//随意加入的话,就自动加入
					$.get("/index.php/qun/wxapp.member/join.html?id="+quninfo.id,function(res){
						if(res.code==0){	//自动加入成功
							//layer.msg(res.msg);
							bui.hint(res.msg);
							pageview.signIn();
						}else{
							layer.alert(res.msg);
						}
					});
				}else{ //需要审核或需要验证码才能加入的情况
					layer.confirm("你还不是本圈子成员,是否先加入本圈子?",{btn:['立即加入','取消']},function(){
						layer.closeAll();
						bui.load({url: "/public/static/libs/bui/pages/frame/show.html",param: {"url": "/index.php/qun/content/apply.html?id="+quninfo.id}});
					});
				}
			}else if(qun_userinfo.type==0){
				layer.alert("你还没通过审核,暂时还不能签到");
			}else{
				pageview.signIn();
			}
		});
    };

	pageview.signIn = function(){
		$.get("/index.php/p/signin-api-sign/id/"+quninfo.id+".html",function(res){
			if(res.code==0){
				layer.msg(res.msg);
				router.$('.signInBox').fadeOut();
			}else{
				router.$('.signInBox').fadeOut();
				layer.alert(res.msg);
			}
		});
	}


	pageview.api = function(_quninfo , _qun_userinfo , _userinfo , cfg) {
		quninfo = _quninfo;
		qun_userinfo = _qun_userinfo;
		userinfo = _userinfo;
        console.log(cfg);
		vues.set_data(cfg.money_days);
		vues.set_num(cfg.continue_num);
    };


    // 事件绑定
    pageview.bind = function() {
    }



    // 初始化
    pageview.init();
    // 绑定事件
    pageview.bind();
    
    return pageview;
})

