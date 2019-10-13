/**
 * 聊天对话模板
 * 默认模块名: pages/chat/chat
 * @return {[object]}  [ 返回一个对象 ]
 */
loader.define(function(require,exports,module) {

    var pageview = {};
	var uid,qid;
	var msg_scroll = 1;
	var show_msg_page  = 1;
	var maxid = -1;
	var getShowMsgUrl = "/index.php/index/wxapp.msg/get_more.html?rows=15&page=";
	var userinfo = {};
	var need_scroll = false;

    // 模块初始化定义
    pageview.init = function () {
        this.bind();

		$("#chat_win").parent().scroll(function () {
			var h = $("#chat_win").parent().scrollTop();
			//console.log(h);
			if( h<100){
				if(msg_scroll!=-1){
					//console.log(h+"--"+msg_scroll);
					router.$('#chat_win').parent().scrollTop(150);
				}
				if(msg_scroll==1){//console.log("++");
					msg_scroll = 0;					
					layer.msg("内容加载中！请稍候...",{time:3000});
					showMoreMsg(uid);
				}								
			}
		});

		if(typeof(to_uid)!='undefined' && to_uid!=""){	//详情页或发送页
			uid = to_uid;
			set_user_name(uid);	//设置当前会话的用户名
			showMoreMsg(uid);	//加载相应用户的聊天记录
			$(".right-tongji").on("click",function(e){
			  bui.load({ url: "/member.php/member/msg/index.html" ,reload:true});
			});
			router.$(".bui-bar-left a").removeClass('btn-back');
			router.$(".bui-bar-left i").removeClass('icon-back');
			router.$(".bui-bar-left i").addClass('fa fa-home');
			$(".bui-bar-left a").on("click",function(e){
			  bui.load({ url: "/",reload:true});
			});
		}else{
			router.$(".bui-bar-right a").on("click",function(e){
				clearInterval(chat_timer);
				//bui.back();
			});
			router.$(".btn-back").on("click",function(e){
				clearInterval(chat_timer);
			  //bui.back();
			});			
		}


		//console.log(chat_timer);
		if(typeof(chat_timer)!='undefined')clearInterval(chat_timer);
		chat_timer = setInterval(function() {
			if(maxid>=0)check_new_showmsg();	//刷新会话用户中有没有新消息,必须要加载到内容后有maxid值才去刷新
		}, 1000);
		
		//this.upload();
		//loader.import(["/public/static/js/exif.js"],function(){});	//上传图片要获取图片信息

		if(uid<0){
			$.get("/index.php/qun/wxapp.visit/check_visit/id/"+(-uid)+".html",function(res){});	//更新圈子浏览日志
		}
    }

    pageview.bind = function () {

		$("#choose_qqface").on("click",function () {
			$("#hack_wrap").hide();
			if($("#face_wrap").html()!=""){
				if($("#face_wrap").is(":hidden")){
					$("#face_wrap").show();
				}else{
					$("#face_wrap").hide();
				}
			}else{
				$("#face_wrap").show();
				router.loadPart({
					id: "#face_wrap",
					url: "/public/static/libs/bui/pages/chat/qqface.html"
				})
			}
        })

		$("#show_hack").on("click",function () {
			$("#face_wrap").hide();
			if($("#hack_wrap").html()!=""){
				if($("#hack_wrap").is(":hidden")){
					$("#hack_wrap").show();
				}else{
					$("#hack_wrap").hide();
				}
			}else{
				$("#hack_wrap").show();
				router.loadPart({
					id: "#hack_wrap",
					url: "/public/static/libs/bui/pages/chat/hack.html?f",
				}).then(function (module) {
					//pageview.upload();
					loader.require("/public/static/libs/bui/pages/chat/voice",function (voice) {
						 console.log(voice)
					 })
				});
			}
        })

            // 发送的内容
        var $chatInput = router.$("#chatInput"),
            // 发送按钮
            $btnSend = router.$("#btnSend"),
            // 聊天的容器
            $chatPanel = router.$(".chat-panel");

        // 绑定发送按钮
        $btnSend.on("click",function (e) {
            var val = $chatInput.val();
            //var tpl = chatTpl(val);
            if( !$(this).hasClass("disabled") ){
                //$chatPanel.append(tpl);
				postmsg(val);
                //$chatInput.val('');
                $(this).removeClass("primary").addClass("disabled");
            }else{
                return false;
            }
        });
		
		 $chatInput.click(function(){
			if(typeof(userinfo.uid)=='undefined'){
				userinfo = window.store.get('userinfo');
			}
			if(userinfo.uid<1){
				layer.confirm("你还没登录，不能发言，是否立即登录？",{btn:['立即登录','取消'],title:"提示"},function () {
					window.location.href = '/index.php/index/login/index.html?fromurl='+encodeURIComponent(window.location.href);
				});
			}
		 });

        // 延迟监听输入
        $chatInput.on("input",bui.unit.debounce(function () {
            var val = $chatInput.val();
            if( val ){
                $btnSend.removeClass("disabled").addClass("primary");

            }else{
                $btnSend.removeClass("primary").addClass("disabled");

            }
        },100))

        var interval = null;
        var count = 3;
        // 安卓键盘弹出的时间较长;
        var time = bui.platform.isIos() ? 200 : 400;
        // 为input绑定事件
        $chatInput.on('focus', function () {

            var agent = navigator.userAgent.toLowerCase();
            interval = setTimeout(function() {
                if (agent.indexOf('safari') != -1 && agent.indexOf('mqqbrowser') == -1 &&
                    agent.indexOf('coast') == -1 && agent.indexOf('android') == -1 &&
                    agent.indexOf('linux') == -1 && agent.indexOf('firefox') == -1) {
                    //safari浏览器
                    window.scrollTo(0, 1000000);
                    setTimeout(function() {
                        window.scrollTo(0, window.scrollY - 45);
                    }, 50)

                } else {
                    //其他浏览器
                    window.scrollTo(0, 1000000);
                }

            }, time);
        }).on('blur', function () {
            if( interval ){
                clearTimeout(interval);
            }

            var agent = navigator.userAgent.toLowerCase();
            interval = setTimeout(function() {
                if (!(agent.indexOf('safari') != -1 && agent.indexOf('mqqbrowser') == -1 &&
                        agent.indexOf('coast') == -1 && agent.indexOf('android') == -1 &&
                        agent.indexOf('linux') == -1 && agent.indexOf('firefox') == -1)) {
                        //safari浏览器
                    window.scrollTo(0, 30);
                }
            }, 0);
        });
    }

	var num = ck_num = 0;
	//刷新会话用户中有没有新消息
	function check_new_showmsg(){//console.log(qid+"&uid="+uid);
		if(ck_num>num){
			console.log("服务器还没反馈数据过来");
			//layer.msg("服务器反馈超时",{time:500});
			return ;
		}
		$.get(getShowMsgUrl+"1&maxid="+maxid+"&uid="+uid+"&num="+num,function(res){			
			if(res.code!=0){				
				layer.alert('页面加载失败,请刷新当前网页');
				return ;
			}
			num++;
			ck_num = num;
			if(res.data.length>0){	//有新的聊天内容
				layer.closeAll();
				need_scroll = true;
				//vues.set_data(res.data);
				add_msg_data(res,'new');
			}
			maxid = res.ext.maxid;
			if(res.ext.lasttime<3){	//3秒内对方还在当前页面的话,就提示当前用户不要关闭当前窗口
				$("#remind_online").show();
			}else{
				$("#remind_online").hide();
			}
		});
		ck_num++;
	}

	//加载更多的会话记录
	function showMoreMsg(uid){
		if(show_msg_page==1){
			maxid = -1;
			layer.msg("数据加载中,请稍候...");
		}	
		$.get(getShowMsgUrl+show_msg_page+"&uid="+uid,function(res){			
			//console.log(res);
			if(res.code==0){
				if(show_msg_page==1){
					maxid = res.ext.maxid;
				}
				layer.closeAll();
				var that = router.$('#chat_win');
				if(res.data.length<1){
					if(show_msg_page==1){
						that.parent().scrollTop(0)
						layer.msg("没有任何聊天记录！",{time:1000});
					}else{
						layer.msg("已经显示完了！",{time:500});
					}
					msg_scroll = -1;	//允许滚动条滚到尽头
				}else{
					show_msg_page++;
					//msg_scroll = 1;
					//vues.set_data(res.data);
					add_msg_data(res);
				}				
			}else{
				layer.msg(res.msg,{time:2500});
			}
		});
	}

	//添加删除信息的功能按钮
	function add_btn_delmsg(){
		$(".chat-panel .del").off("click");
		$(".chat-panel .del").click(function(){
			var id = $(this).data("id");
			var that = $(this);
			$.get("/member.php/member/wxapp.msg/delete.html?id="+id,function(res){
				if(res.code==0){
					layer.msg("删除成功");
					var father = that.parent().parent().parent();
					father.hide();
					if(father.prev().hasClass("show_username")||father.prev().hasClass("bui-box-center")){
						father.prev().hide();
					}
				}else{
					layer.alert(res.msg);
				}
			});
		});
	}
	

	//设置当前聊天的用户名
	function set_user_name(uid){
		if(uid>0){
			$.get("/index.php/index/wxapp.member/getbyid.html?uid="+uid,function(res){
				if(res.code==0){
					$("#send_user_name").html(res.data.username);
				}
			});
		}else if(uid<0){
			$.get("/index.php/qun/wxapp.qun/getbyid.html?id="+Math.abs(uid),function(res){
				if(res.code==0){
					$("#send_user_name").html(res.data.title);
				}
			});
		}else{
			$("#send_user_name").html("系统消息");
		}		
	}

	//发送消息
	function postmsg(content){
		if(content==''){
			layer.alert('消息内容不能为空');
			return ;
		}
		$.post("/member.php/member/wxapp.msg/add.html",{'uid':uid,'content':content,},function(res){		
			if(res.code==0){
				$("#chatInput").val('');
				$("#hack_wrap").hide();
				$("#face_wrap").hide();
				layer.msg('发送成功');
			}else{
				$("#btnSend").removeClass("disabled").addClass("primary");
				layer.alert('发送失败:'+res.msg);
			}
		});
	}
	
	function add_msg_data(res,type){
		var timer = setInterval(function(){
			if(typeof(userinfo.uid)=='undefined'){
				userinfo = window.store.get('userinfo');
			}
			if(typeof(userinfo.uid)!='undefined'){
				clearInterval(timer);
				var myid = typeof(my_uid)!='undefined'?my_uid:userinfo.uid;
				//console.log(res.data);
				format_msg_data(res.data , myid , type)
			}
			//console.log('userinfo=',userinfo);
		},500);
	}

	function format_msg_data(array , myid , type){
		var str = '';
		var del_str = '';
		var user_str = '';
		var userdb = userinfo;
		array.forEach((rs)=>{
			del_str = '';
			if(userdb.uid>0 && (rs.uid==userdb.uid || rs.touid==userdb.uid) ){
				del_str = `<i v-if="rs.uid==userdb.uid||rs.touid==userdb.uid" :data-id="rs.id" class="del glyphicon glyphicon-remove-circle"></i>`;
			}

			user_str = `<div class="chat-icon"><a href="#" class="iframe"><img src="${rs.from_icon}" onerror="this.src='__STATIC__/images/noface.png'" title="${rs.from_username}"></a></div>`;
			if(rs.uid!=myid){
				str += `
					<div class="bui-box-align-top chat-target">
						${user_str}
						<div class="span1">
							<div class="chat-content bui-arrow-left">${rs.content}</div>
							${del_str}
						</div>
					</div>	`;
			}else{
				str += `
					<div class="bui-box-align-top chat-mine">
					<div class="span1">
						<div class="bui-box-align-right">
						  ${del_str}
						  <div class="chat-content bui-arrow-right">${rs.content}</div>
						</div>
					</div>
					${user_str}
				</div>`;
			}

			if(rs.qun_id>0 && rs.uid!=myid){
				str += `<div class="show_username">${rs.from_username}</div>`;
			}

			str += `
					<div class="bui-box-center">
						<div class="time">${rs.create_time}</div>
					</div>`;
		});
		if(type == 'new'){
			router.$("#chat_win").prepend(str);
		}else{
			router.$("#chat_win").append(str);
		}

		if(show_msg_page==2 || need_scroll==true){
			router.$('#chat_win').parent().scrollTop(20000);
			need_scroll = false;
		}else{
			router.$('#chat_win').parent().scrollTop(400);
		}
		add_btn_delmsg();
		msg_scroll = 1;
	}

	var getParams = bui.getPageParams();
    getParams.done(function(result){
		console.log(result.uid);
		if(result.uid!=undefined){
			qid = uid = result.uid;
			//vues.set_id(uid);
			show_msg_page = 1; //重新恢复第一页
			msg_scroll = 1; //恢复可以使用滚动条
			set_user_name(uid);	//设置当前会话的用户名
			showMoreMsg(uid);	//加载相应用户的聊天记录
		}
    })


	/*
	var vues = new Vue({
				el: '.page-chat',
				data: {
					from_id: typeof(to_uid)!='undefined'?to_uid:"",
					to_id:0,
					me_id:0,
					userdb:{},
					listdb:[],
				},
				watch:{
					listdb: function() {
						this.$nextTick(function(){	//数据渲染完毕才执行
							if(show_msg_page==2 || router.$('#chat_win').parent().scrollTop()>300){
								router.$('#chat_win').parent().scrollTop(20000);
							}else{
								router.$('#chat_win').parent().scrollTop(200);
							}
							add_btn_delmsg();
						})
					},
				},
				methods: {
					set_id:function(id){
						this.to_id = id;
					},
					set_data:function(array){
						var ar = this.listdb;
						var userinfo = {};
						var that = this;
						var timer = setInterval(function() {							
							if(typeof(userinfo.uid)=='undefined'){
								userinfo = window.store.get('userinfo');
							}
							if(typeof(userinfo.uid)!='undefined'){								
								that.me_id = typeof(my_uid)!='undefined'?my_uid:userinfo.uid;
								that.userdb = Object.assign({}, that.userdb, userinfo);								
								that.listdb = [];
								array.forEach((rs)=>{
									that.listdb.push(rs);
								});
								ar.forEach((rs)=>{
									that.listdb.push(rs);
								});
								clearInterval(timer);
							}
							console.log('userinfo=',userinfo);
						},500);
					},
				}		  
			});
	*/

    // 初始化
    pageview.init();

    // 输出模块
    return pageview;

})
