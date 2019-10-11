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
	var getShowMsgUrl = "/member.php/member/msg/get_more.html?rows=15&page=";
	

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
		
		this.upload();
		loader.import(["/public/static/js/exif.js"],function(){});	//上传图片要获取图片信息
    }
    pageview.bind = function () {

		$("#choose_qqface").on("click",function () {
			if($("#hack_wrap .list_qqface").length>0){
				$("#hack_wrap").html("");
			}else{
				router.loadPart({
					id: "#hack_wrap",
					url: "/public/static/libs/bui/pages/chat/qqface.html"
				})
			}
        })

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
				vues.set_data(res.data);
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
		msg_scroll = false;
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
				}else{
					show_msg_page++;
					msg_scroll = true;
					vues.set_data(res.data);
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
				$("#hack_wrap").html('');
				layer.msg('发送成功');
			}else{
				$("#btnSend").removeClass("disabled").addClass("primary");
				layer.alert('发送失败:'+res.msg);
			}
		});
	}

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


	// 上传图片
	pageview.upload = function() {
		var uiUpload = bui.upload();		
		var uiActionsheet = bui.actionsheet({
					trigger: "#photoBtn",
					buttons: [{ name: "拍照上传", value: "camera" }, { name: "从相册选取", value: "photo" }],
					callback: function(e) {
						var ui = this;
						var val = $(e.target).attr("value");
						switch (val) {
							case "camera":
								ui.hide();
								uiUpload.add({
									"from": "camera",
									"onSuccess": function(val, data) {
										// 展示本地图片
										this.toBase64({
											onSuccess: function(url) {
												upload_pic(url)
											}
										});

										// 也可以直接调用start上传图片
									}
								})
								break;
							case "photo":
								ui.hide();
								uiUpload.add({
									"from": "",
									"onSuccess": function(val, files) {
										 var Orientation=0
										 var filefield = document.getElementById($('input[name="uploadFiles"]').attr('id')) 
											 var file = filefield.files[0];
											EXIF.getData(file, function(){												 
												Orientation = EXIF.getTag(this, 'Orientation'); console.log("d "+Orientation);												
											});										
										//console.log(val);
										//console.log(this.data()[0]);
										//var url = window.URL.createObjectURL(files[0]);
										//document.querySelector('img').src = window.URL.createObjectURL(url);
										// 展示本地图片
										this.toBase64({
											onSuccess: function(url) {
												upload_pic(url,Orientation)
											}
										});
										// 也可以直接调用start上传图片
									}
								})

								break;
							case "cancel":
								ui.hide();
								break;
						}
					}
				})

				function templatePhoto(url) {
					return `<img src="${url}" alt="" />`
				}
				
				function upload_pic(base64,Orientation){
					var image = new Image();
						image.src = base64;
						image.onload = function() {
						var resized = resizeUpImages(image);
						var severUrl = "/index.php/index/attachment/upload/dir/images/from/base64/module/bbs.html";
						$.post(severUrl, {'imgBase64':resized,'Orientation':Orientation,'tags':''}).done(function (res) {
							if(res.code==1){
								//console.log(res);
								var url = res.path;
								if(url.indexOf('://')==-1 && url.indexOf('/public/')==-1){
									url = '/public/'+url;
								}
								$("#chatInput").val( templatePhoto(url)+$("#chatInput").val() )
								if($("#btnSend").hasClass("disabled"))$("#btnSend").removeClass("disabled").addClass("primary");
							}else{
								alert(res.info);
							}
						}).fail(function () {
							alert('操作失败，请跟技术联系');
						});
					}					
				}

				function resizeUpImages(img) {
					//压缩的大小
					var max_width = 1920; 
					var max_height = 1080;
					var canvas = document.createElement('canvas');
					var width = img.width;
					var height = img.height;
					if(width > height) {
						if(width > max_width) {
							height = Math.round(height *= max_width / width);
							width = max_width;
						}
					}else{
						if(height > max_height) {
							width = Math.round(width *= max_height / height);
							height = max_height;
						}
					}
					canvas.width = width;
					canvas.height = height;
					var ctx = canvas.getContext("2d");
					ctx.drawImage(img, 0, 0, width, height);
					//压缩率
					return canvas.toDataURL("image/jpeg",0.72); 
				}
	}


    // 初始化
    pageview.init();

    // 输出模块
    return pageview;
})
