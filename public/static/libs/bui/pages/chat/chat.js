/**
 * 聊天对话模板
 * 默认模块名: pages/chat/chat
 * @return {[object]}  [ 返回一个对象 ]
 */
loader.define(function(require,exports,module) {

    var pageview = {};
	var uid,qid;
	var msg_scroll = true;
	var show_msg_page  = 1;
	var have_load_data = false;
	var maxid = -1;
	

    // 模块初始化定义
    pageview.init = function () {
        this.bind();

		$("#chat_win").parent().scroll(function () {
			var h = $("#chat_win").parent().scrollTop();
			//console.log(h);
			if( h<200 && msg_scroll==true){
				layer.msg("内容加载中！请稍候...",{time:3000});
				showMoreMsg(uid);
			}
		});

		if(to_uid!=""){	//详情页或发送页
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
			if(maxid>=0)check_new_showmsg();	//刷新会话用户中有没有新消息
		}, 1000);
    }
    pageview.bind = function () {
            // 发送的内容
        var $chatInput = $("#chatInput"),
            // 发送按钮
            $btnSend = $("#btnSend"),
            // 聊天的容器
            $chatPanel = $(".chat-panel");

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

	var num = 0;
	//刷新会话用户中有没有新消息
	function check_new_showmsg(){//console.log(qid+"&uid="+uid);
		$.get(getShowMsgUrl+"1&maxid="+maxid+"&uid="+uid+"&num="+num,function(res){			
			if(res.code==0){				
				num++;
				if(res.data!=""){	//有新的聊天内容
					layer.closeAll();
					$('#chat_win').prepend(res.data);
					$("#chat_win").parent().scrollTop(2000);
				}
			}
			maxid = res.ext.maxid;
			if(res.ext.lasttime<3){	//3秒内对方还在当前页面的话,就提示当前用户不要关闭当前窗口
				$("#remind_online").show();
			}else{
				$("#remind_online").hide();
			}
		});
	}

	//加载更多的会话记录
	function showMoreMsg(uid){
		if(show_msg_page==1){
			maxid = -1;
			layer.msg("数据加载中,请稍候...");
		}
		msg_scroll = false;
		$.get(getShowMsgUrl+show_msg_page+"&uid="+uid,function(res){
			
			//console.log(res);
			if(res.code==0){
				if(show_msg_page==1){
					maxid = res.ext.maxid;
				}
				layer.closeAll();
				var that = $('#chat_win');
				if(res.data==''){
					if(show_msg_page==1){
						that.parent().scrollTop(0)
						layer.msg("没有任何聊天记录！",{time:1000});
					}else{
						layer.msg("已经显示完了！",{time:500});
					}		
				}else{
					if(show_msg_page==1){
						that.html(res.data);
						that.parent().scrollTop(3000)
					}else{
						that.append(res.data);
						that.parent().scrollTop(350);
					}        
					show_msg_page++;
					msg_scroll = true;
				}				
			}else{
				layer.msg(res.msg,{time:2500});
			}
		});
	}
	

	//设置当前聊天的用户名
	function set_user_name(uid){
		if(uid>0){
			$.get(get_user_info_url+"?uid="+uid,function(res){
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
		$.post(postMsgUrl,{'uid':uid,'content':content,},function(res){		
			if(res.code==0){
				$("#chatInput").val('');                
				layer.msg('发送成功');
			}else{
				$("#btnSend").removeClass("disabled").addClass("primary");
				layer.alert('发送失败:'+res.msg);
			}
		});
	}

	var vues = new Vue({
				el: '.bui-bar',
				data: {
						from_id: to_uid,
						to_id:0,
						me_id:my_uid,
				},
				watch:{
				},
				methods: {
					set_id:function(id){
						this.to_id = id;
					},
				}		  
			});
	
	var getParams = bui.getPageParams();
    getParams.done(function(result){
		console.log(result.uid);
		if(result.uid!=undefined){
			qid = uid = result.uid;
			vues.set_id(uid);
			show_msg_page = 1; //重新恢复第一页
			msg_scroll = true; //恢复可以使用滚动条
			set_user_name(uid);	//设置当前会话的用户名
			showMoreMsg(uid);	//加载相应用户的聊天记录
		}
    })

    // 初始化
    pageview.init();

    // 输出模块
    return pageview;
})
