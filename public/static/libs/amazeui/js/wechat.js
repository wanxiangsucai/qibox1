

//底部扩展键
$(function() {
    $('#doc-dropdown-js').dropdown({justify: '#doc-dropdown-justify-js'});
});

$(function(){
	$(".office_text").panel({iWheelStep:32});
});

//功能切换
$(document).ready(function(){
	$(".sidestrip_icon a").click(function(){ 
		var i = $(this).index();
		$(".sidestrip_icon a").eq(i).addClass("cur").siblings().removeClass('cur');
		$(".middle").hide().eq(i).show();
		//$("#windows_body ul").hide();
		//$("#windows_body ul").eq(i).show();
		if(i==0){
			$("#windows_body ul").hide();
			$(".pc_show_all_msg").show();
			
		}
		if(i==2){
			chat_type = 'tongji';
		}else{
			chat_type = 'chat';
		}
	});
});



/*
window.onload=function b(){
	var text = document.getElementById('input_box');
	var chat = document.getElementById('chatbox');
	var btn = document.getElementById('send');
	var talk = document.getElementById('talkbox');
	
	btn.onclick=function(){
		if(text.value ==''){
            alert('不能发送空消息');
        }else{
			chat.innerHTML += '<li class="me"><img src="'+'images/own_head.jpg'+'"><span>'+text.value+'</span></li>';
			text.value = '';
			chat.scrollTop=chat.scrollHeight;
			talk.style.background="#fff";
			text.style.background="#fff";
		};
	};
};
*/

//三图标
window.onload=function(){
	function a(){
		var si1 = document.getElementById('si_1');
		var si2 = document.getElementById('si_2');
		var si3 = document.getElementById('si_3');
		si1.onclick=function(){
			si1.style.background="url(/public/static/libs/amazeui/images/icon/head_2_1.png) no-repeat"
			si2.style.background="";
			si3.style.background="";
		};
		si2.onclick=function(){
			si2.style.background="url(/public/static/libs/amazeui/images/icon/head_3_1.png) no-repeat"
			si1.style.background="";
			si3.style.background="";
		};
		si3.onclick=function(){
			si3.style.background="url(/public/static/libs/amazeui/images/icon/head_4_1.png) no-repeat"
			si1.style.background="";
			si2.style.background="";
		};
	};
	function b(){
		var text = document.getElementById('input_box');
		var chat = document.getElementById('chatbox');
		var btn = document.getElementById('send');
		var talk = document.getElementById('talkbox');
		btn.onclick=function(){
			if(text.value ==''){
				alert('不能发送空消息');
			}else{
				chat.innerHTML += '<li class="me"><img src="'+'/public/static/libs/amazeui/images/own_head.jpg'+'"><span>'+text.value+'</span></li>';
				text.value = '';
				chat.scrollTop=chat.scrollHeight;
				talk.style.background="#fff";
				text.style.background="#fff";
			};
		};
	};
	a();
	//b(); 
};

//检查框架宽度与高度是否足够
$(function(){
	var obj = window.parent.$("iframe");
	for(var i=0;i<obj.length;i++){		
		var url = obj.eq(i).attr('src');
		if(url.indexOf('member/msg/index')){
			if(obj.eq(i).css('height').replace('px','')<750){
				obj.eq(i).parent().parent().css({height:'750px'});
				obj.eq(i).css({height:'707px'});
			}

			if(obj.eq(i).parent().parent().hasClass('layui-layer-iframe') && obj.eq(i).parent().parent().css('width').replace('px','')<950){
				obj.eq(i).parent().parent().css({width:'950px',top:'0px'});
			}
		}
	}	
});




//异步加载被调用的函数  务必注意,这个函数名必须要跟标签名一样
function pc_msg_user_list(res){	
	$.each(res.ext.s_data,function(i,rs){		
		//console.log(rs.uid);		
		uid_array[rs.f_uid] = rs.id;
		if(uid==0){
			//console.log(rs.uid);
			uid = rs.f_uid;
			showMoreMsg(uid);
			set_user_name(uid);
		}
	});
	add_click_user();
}

//信息用户列表添加点击事件
function add_click_user(){

	//未读群消息排在前面
	var that = $(".pc_msg_user_list");
	var obj = $(".pc_msg_user_list .ck");
	for(var i=(obj.length-1);i>=0;i--){
		var o = obj.eq(i).parent();
		that.prepend( o.get(0).outerHTML);
		o.remove();
	}

	$(".pc_msg_user_list li").off('click');
	$(".pc_msg_user_list li").click(function(){
		w_s.close();
		$(this).find(".shownum").removeClass("ck");
		$(".pc_msg_user_list li").removeClass('user_active');
		$(this).addClass('user_active');
		uid = $(this).data('uid');
		console.log(uid);
		show_msg_page = 1; //重新恢复第一页
		msg_scroll = true; //恢复可以使用滚动条
		showMoreMsg(uid);	//加载相应用户的聊天记录
		set_user_name(uid); //设置当前会话的用户名

		//$(".live-player-warp").hide();
		//$("#players").html('<span>视频直播即将开始...</span>');
		have_load_live_player = false;
	});
	
}

//显示更多用户列表
function showMore_User(){
  ListMsgUserPage++;
  user_scroll = false;
  $.get(ListMsgUserUrl+ListMsgUserPage,function(res){  
    //console.log(res);
    //console.log(res.data);
    if(res.code==0){
      if(res.data==''){
        layer.msg("已经显示完了！",{time:500});
      }else{
        $('.pc_msg_user_list').append(res.data);
		pc_msg_user_list(res);
        user_scroll = true;
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

function add_new_user(){
	layer.prompt({
		  formType: 0,
		  value: '',
		  title: '你要发信息给哪个用户名?',
		  //area: ['100px', '20px'] //formType:2 自定义文本域宽高
		}, function(value, index, elem){
			layer.close(index);
			$.get(get_uid_by_name_url+"?name="+value,function(res){
				if(res.code==0){
					layer.msg("你现在可以给他发消息了");
					uid = res.data.uid;					
					set_user_name(uid);
					show_msg_page = 1;
					showMoreMsg(uid);
				}else{
					layer.alert('当前用户不存在!');
				}
			});
	});
}

//将对话内容的数组转成HTML字符串
function format_chatmsg_tohtml(array){
	if(typeof(array)=='string'){
		return array;
	}
		var str = '';
		var str_name = '';
		var str_del = '';
		var old_html = $(".pc_show_all_msg").html();
		array.forEach((rs)=>{
			if(old_html.indexOf( '<li data-id="'+rs.id )>-1){
				console.log('有重复的消息'+rs.id);
				return true;
			}
			str_name = (rs.qun_id && rs.uid!=my_uid)?`<div class="name" data-uid="${rs.uid}" onclick="$('#input_box').val('@${rs.from_username} ').focus()">@${rs.from_username}</div>`:'';
			str_del = (rs.uid==my_uid||rs.touid==my_uid) ? `<i data-id="${rs.id}" class="del glyphicon glyphicon-remove-circle"></i>` : '';
			str += `<li data-id="${rs.id}" class="` + ( rs.uid==my_uid ? 'me' : 'other' ) + `">
						<dd class="time" data-time="${rs.full_time}"><a>${rs.create_time}</a></dd>
						${str_name}
						<a href="/member.php/home/${rs.uid}.html" class="user_icon" target="_blank"><img src="${rs.from_icon}" onerror="this.src='/public/static/images/noface.png'" title="${rs.from_username}"></a><span class="content">${rs.content}</span>
						${str_del}		
						</li>`;
		});
		return str;
}


	//刷新最近的消息用户
	function check_list_new_msgnum(){
		$.get(ListMsgUserUrl+"1",function(res){
			if(res.code==0){
				var remind = true;
				$.each(res.ext.s_data,function(i,rs){
					//出现新的消息新用户，或者是原来新消息的用户又发来了新消息
					if(typeof(uid_array[rs.f_uid])=='undefined'||rs.id>uid_array[rs.f_uid]){
						console.log('有新的消息来了');
						$('.pc_msg_user_list').html(res.data);
						add_click_user();
						if(remind && window.Notification){	//消息提醒
							remind = false;
							if(Notification.permission=="granted"){
								pushNotice();
							}else{
								Notification.requestPermission(function(status) {                  
									if (status === "granted") {
										pushNotice();
									}
								});
							}
						}
					}
					//新消息已读
					if(rs.new_num<1){
						$('.pc_msg_user_list .list_'+rs.f_uid+' .shownum').removeClass('ck');
						$('.pc_msg_user_list .list_'+rs.f_uid+' .shownum').html(rs.num>999?'99+':rs.num);
					}
					//console.log(rs.f_uid+'='+rs.id+'='+uid_array[rs.f_uid]);
					uid_array[rs.f_uid] = rs.id;
				});
			}
		});
	}

	//右下角弹信息提示,有新消息来了
	function pushNotice(){
		var m = new Notification('新消息提醒', {body: '你收到一条新消息,请注意查收',});
			m.onclick = function () { window.focus();}
	}

//优先显示底部的内容
	function goto_bottom(vh){
		var iCount = setInterval(function() {
			var obj = $(".pc_show_all_msg");
			var h = obj.height();
			//console.log( '实际的高度='+h);
			if(h>vh){
				clearInterval(iCount);
				show_msg_top = h-453;
				obj.css({top:(-show_msg_top)+"px"});
				console.log('top='+show_msg_top)
			}
		}, 200);
	}


//建立WebSocket长连接
var chat_timer,clientId = '';
var pushIdArray = [];
function ws_connect(){
	if(typeof(w_s)=='object'){
		$("#remind_online").hide();
		w_s.close();
		console.log("#########中断了,重新连接!!!!!!!!!!"+Math.random());
		setTimeout(function(){	//避免还没有完全关闭连接
			ws_link();
		},3000);
	}else{
		ws_link();
	}
}

function ws_link(){
		ws_have_link = true;
		w_s = new WebSocket(ws_url);
		w_s.onmessage = function(e){
			var obj = {};
			try {
				obj = JSON.parse(e.data);
			}catch(err){
				console.log(err);
			}
			if(obj.type=='newmsg'){	//其它地方推送消息过来
				if( (obj.data[0].qun_id>0 && uid==-obj.data[0].qun_id) || (obj.data[0].uid==uid||obj.data[0].touid==uid) ){
					check_new_showmsg(obj);	//推数据
					$.get("/index.php/index/wxapp.msg/update_user.html?uid="+uid+"&id="+obj.data[0].id,function(res){//更新记录
						console.log(res.msg);
					});	
				}
			}else if(obj.type=='new_msg_id'){	//圈子直播文字最后得到的真实ID
				pushIdArray[obj.data.push_id] = obj.data.id; //删除内容的时候要用到
				$.get("/index.php/index/wxapp.msg/update_user.html?uid="+uid+"&id="+obj.data.id,function(res){//更新记录
					console.log(res.msg);
				});
			}else if(obj.type=='qun_sync_msg'){	//圈子直播文字  
				check_new_showmsg(obj);
			}else if(obj.type=='connect'){	//建立链接时得到客户的ID
				if(uid==0){
					return ;
				}
				clientId = obj.client_id;
				$.get("/index.php/index/wxapp.msg/bind_group.html?uid="+uid+"&client_id="+clientId,function(res){	//绑定用户
					if(res.code==0){
						//layer.msg('欢迎到来!',{time:500});
					}else{
						layer.msg(res.msg);
					}
				});
				var username = my_uid>0?userinfo.username:'';
				var icon = my_uid>0?userinfo.icon:'';
				var is_quner = my_uid==quninfo.uid ? 1 : 0;	//圈主
				w_s.send('{"type":"connect","url":"'+window.location.href+'","uid":"'+uid+'","my_uid":"'+my_uid+'","is_quner":"'+is_quner+'","userAgent":"'+navigator.userAgent+'","my_username":"'+username+'","my_icon":"'+icon+'"}');
			}else if(obj.type=='count'){  //用户连接成功后,算出当前在线数据统计
				 show_online(obj,'goin');
			}else if(obj.type=='leave'){	//某个用户离开了
				show_online(obj,'getout')
				//console.log(obj);
			}else if(obj.type=='msglist'){	//需要更新列表信息
				//console.log("消息列表,有新消息来了..........");
				//console.log(e.data);
				//obj.uid==uid即本圈子提交数据(或者自己正处于跟他人私聊),不用更新列表, obj.uid它人私信自己,就要更新,obj.uid是其它圈子也要更新
				if( (obj.uid<0 && obj.uid!=uid) || (obj.uid==my_uid && obj.from_uid!=uid ) ){
					check_list_new_msgnum();
				}
			}else if(obj.type=='give_vod_voice_state'){	//CMS音频 成功获取到直播信息,访客得到音频的播放状态
				voice_urls = obj.data.play_urls;	//首次点播要用
				vod_voice_play(obj.data,'ok');
			}else if(obj.type=='error#ask_vod_voice_state'){	//CMS音频 圈主首次进入或者圈主不在,按默认的播放
				vod_voice_play({play_urls:voice_urls},'err');
			}else if(obj.type=='ask_vod_voice_state'||obj.type=='ask_vod_voice_sync'){	//CMS音频 访客请求圈主的音频播放状态,圈主进行反馈,圈主如有多个窗口最后一次登录窗口才收到,第一次窗口不会收到.
				var arr ={};
				if(typeof(voice_iframeWin)=='object'){
					arr = {
						play_index:voice_iframeWin.get_now_state()[0],
						play_time:voice_iframeWin.get_now_state()[1],		
					};
				}
				arr.play_urls = voice_urls;
				var tag = obj.type=='ask_vod_voice_sync'?'give_vod_voice_sync':'give_vod_voice_state';
				w_s.send('{"type":"quner_to_user","tag":"'+tag+'","data":' + JSON.stringify( arr ) + ',"user_cid":"' + obj.user_cid + '"}');
			}else if(obj.type=='vod_voice_sync_play'){  //CMS音频 同步播放
				voice_iframeWin.control(obj.data);
			}else if(obj.type=='give_vod_voice_sync'){  //CMS音频 获取到同步信息
				voice_iframeWin.vod.sync_play({index:obj.data.play_index,time:obj.data.play_time});
			}else{
				console.log(e.data);
			}
		};
		
		w_s.onopen = function(e) {};
		w_s.onerror = function(e){
			console.log("#########连接异常中断了.........."+Math.random(),e);
			w_s.close();			
			ws_have_link = false;
		};
		w_s.onclose = function(e){
			w_s.close();
			console.log("########连接被关闭了.........."+Math.random());
			ws_have_link = false;
		};
		
		if(typeof(chat_timer)!='undefined')clearInterval(chat_timer);
		chat_timer = setInterval(function() {
			if(ws_have_link != true){
				w_s = new WebSocket(ws_url);
			}else{
				w_s.send('{"type":"refresh"}');
			}			
		}, 1000*50);	//50秒发送一次心跳

		var show_online = function(obj,type){
				 var total = obj.total; //在线窗口,同一个人可能有多个窗口				 
				 var data = obj.data;
				 var usernum = obj.data.length;  //在线会员人数,已注册的会员
				 if(total>1){
					 if(type=='goin'){
						 layer.msg("有新用户："+data[data.length-1].username+" 进来了");
					 }else if(type=='getout'){
						 layer.msg(obj.msg);
					 }
					 $("#remind_online").show();
					 if(uid>0){
						 $("#remind_online").html('对方在线,请不要离开!');
					 }else{
						 $("#remind_online").html('共有 '+total+' 个访客,会员有 '+usernum+' 人! 查看详情');
						 $("#remind_online").off('click');
						 $("#remind_online").click(function(){
							 view_online_user(data);
						 });
					 }
				 }else if( !$("#remind_online").is(':hidden') ){
					 if(uid>0){
						 layer.msg('对方已离开!');
					 }else{
						 layer.msg('人全走光了!'+obj.msg);
					 }
					 $("#remind_online").hide();
				 }
		}

		var view_online_user = function(data){
			var str = '';
			data.forEach((rs)=>{
						str += '<a href="/member.php/home/'+rs.uid+'.html" target="_blank">'+rs.username+'</a>、';
					});
			layer.open({
					type: 1,
					anim: 5,
					shade: 0,
					title: '仅列出已注册的在线会员数，不含游客',
					area: ['400px', '300px'],
					content: '<div style="padding:20px;line-height:180%;">'+str+'</div>',
			});
		}
}

//初次加载成功
function load_first_page(res){
	maxid = res.ext.maxid;

	quninfo = res.ext.qun_info;	//圈子信息

	ws_url = res.ext.ws_url;

	if(ws_url==''){	//没有设置WS的话,就用AJAX轮询
		check_new = setInterval(function(){
			if(maxid>=0)check_new_showmsg();
		},9000);	//没有发信息之前刷新时间不宜太快,9秒刷新一次

		setInterval(function() {
			//list_i++;
			//if(list_i%list_time==0)
			check_list_new_msgnum();	//每隔20秒获取一次列表数据
		}, 1000*20);
	}else{
		ws_connect();	//建立长链接
	}

	set_live_player(res);	//检查是否有视频直播
}

//加载更多的会话记录
function showMoreMsg(uid){
	if(show_msg_page==1){
		maxid = -1;
		get_qunuser_list(uid);	//获取圈内成员列表
		layer.msg("数据加载中,请稍候...");
	}
	msg_scroll = false;
	$.get(getShowMsgUrl+show_msg_page+"&uid="+uid,function(res){  
		//console.log(res);
		//console.log(res.data);
		if(res.code==0){
			if(show_msg_page==1){
				load_first_page(res);				
			}
			set_main_win_content(res);
		}else{
			layer.msg(res.msg,{time:2500});
		}
	});
}

//刷新会话用户中有没有新消息
var num = ck_num = 0;
function check_new_showmsg(obj){
    if(ws_url==''){	//没有设置WS的话,就用AJAX轮询
        if(ck_num>num){
            console.log("服务器还没反馈数据过来");
            //layer.msg("服务器反馈超时",{time:500});
            return ;
        }
    }
    
    if( typeof(obj)=='object' && typeof(obj.data)=='object' && obj.data.length>0 ){		//服务端推数据, 即被动获取数据
        var res = obj;
        
        var that = $('.pc_show_all_msg');
		
        var str = format_chatmsg_tohtml(res.data);
        if(str!=""){	//有新的聊天内容
            var vh = that.height();
            //console.log( '原来的高度='+vh);
            that.prepend(str);
            format_show_time(that)	//隐藏相邻的时间
            goto_bottom(vh);		//消息滚到最底部
            add_btn_delmsg();
            need_scroll = true;
            if(window.Notification){	//消息提醒
                if(Notification.permission=="granted"){
                    pushNotice();
                }else{
                    Notification.requestPermission(function(status) {
                        if (status === "granted") {
                            pushNotice();
                        }
                    });
                }
            }
        }
        set_live_player(res,'cknew');	//设置视频直播的播放器 
        //add_msg_data(res,'new');
        if(typeof(res.ext)!='undefined')maxid = res.ext.maxid;	//不主动获取数据的话,这个用不到
        
    }else{	//客户端拉数据, 主动获取数据
        
        $.get(getShowMsgUrl+"1&maxid="+maxid+"&uid="+uid+"&num="+num,function(res){
            if(res.code!=0){
                layer.alert('页面加载失败,请刷新当前网页');
                return ;
            }
            set_live_player(res,'cknew');	//检查是否有视频直播
            num++;
            ck_num = num;
            var that = $('.pc_show_all_msg');
            var str = format_chatmsg_tohtml(res.data);
            if(str!=""){	//有新的聊天内容
                var vh = that.height();
                //console.log( '原来的高度='+vh);
                that.prepend(str);
                format_show_time(that)	//隐藏相邻的时间
                goto_bottom(vh);
                add_btn_delmsg();
                need_scroll = true;
                if(window.Notification){	//消息提醒
                    if(Notification.permission=="granted"){
                        pushNotice();
                    }else{
                        Notification.requestPermission(function(status) {
                            if (status === "granted") {
                                pushNotice();
                            }
                        });
                    }
                }
            }
            //console.log( '='+res.ext.lasttime);
            maxid = res.ext.maxid;
            if(res.ext.lasttime<3){	//3秒内对方还在当前页面的话,就提示当前用户不要关闭当前窗口
                if(uid>0){
                    $("#remind_online").html("对方正在输入中，请稍候...");
                }else{
                    $("#remind_online").html("有用户在线");
                }
                $("#remind_online").show();
            }else{
                $("#remind_online").hide();
            }
        });
            ck_num++;
    }
}

//往主窗口里边加入显示的数据
function set_main_win_content(res){
	//layer.closeAll();
	var that = $('.pc_show_all_msg');
	res.data = format_chatmsg_tohtml(res.data);
	if(res.data==''){
		if(show_msg_page==1){
			that.html("");
			layer.msg("没有任何聊天记录！",{time:1000});
		}else{
			layer.msg("已经显示完了！",{time:500});
		}		
	}else{
		//console.log("ddddddddddddddddd-"+show_msg_page);
		//need_scroll$('.pc_show_all_msg').css('top',(453-that.height())+'px');
		if(show_msg_page==1){
			that.html(res.data);
			format_show_time(that);			
			setTimeout(function(){
				that.css('top',(453-that.height())+'px');
			},500);
		}else{
			var old_h = that.height();

			that.append(res.data);
			format_show_time(that);	
			
			setTimeout(function(){
				var new_h = $(".pc_show_all_msg").height();					
				$(".pc_show_all_msg").css('top',(old_h-new_h)+'px');
			},500);
		}		

		add_btn_delmsg();
		format_nickname();
		show_msg_page++;
		msg_scroll = true;
	}
}

//隐藏相邻的时间
function format_show_time(that){
	that.children('li').each(function(i){
		var this_time = $(this).find('.time').data('time');
		var next_time = $(this).next().find('.time').data('time');
		//console.log(i+" "+this_time+" "+next_time);
		if(next_time!=undefined && this_time-next_time<60){
			$(this).find('.time').hide();
		}
	})
}

//加载统计动态的详细内容数据
function get_tongji_msg(type){
	if(show_msg_page==1){
		$.get(tongjiCountUrl+"?set_read=1&type="+tj_type,function(res){});//把新数据标志为已读
		layer.msg("数据加载中,请稍候...");
	}
	msg_scroll = false;
	$.get(tongjiMsgUrl + show_msg_page + "&type="+type,function(res){
		if(res.code==0){
			set_main_win_content(res);
		}
	});
}

var uid = 0;	//当前聊天用户的UID

//URL中指定的用户,同步WAP模板
var str = window.location.href;
if (str.indexOf('uid=')>-1) {	//;
    str = str.substring(str.indexOf('uid=')+4).replace(/[^\d|^\-]/g,"");
	if (/^[-]?[0-9]+$/.test(str)) {
		uid = str;
		$(function(){
			showMoreMsg(uid);
			set_user_name(uid);
		});
	}
}


var chat_type = 'chat';   //主窗口当前应该加载哪类内容,执行哪个函数

var tj_type = '';  //当前选择了哪种统计数据

var uid_array = [];   //每个用户的最新消息ID

var quninfo = [];   //圈子信息
var ListMsgUserPage = 1;	//所有信息用户列表
var show_msg_page = 1;	//会话记录分页
var msg_scroll = true;  //做个标志,不要反反复复的加载会话内容
var user_scroll = true;  //做个标志,不要反反复复的加载用户列表
var user_div_top = 0;	//当前信息用户列表滚动条坐标top系数
var show_msg_top = 0;  //当前对话框滚动条坐标top系数
var maxid = -1;
var need_scroll = false;
var user_num = 0;		//圈内成员数
var user_list = {};	//圈内成员列表
var have_load_live_player=false;
var check_new;
var w_s,ws_url,ws_have_link;
//var list_i=0,list_time=30;	//每隔30秒获取一次列表数据

$(function(){
	
	var pc_show_all_msg_obj = $(".pc_show_all_msg");
	var pc_msg_user_list_obj = $(".pc_msg_user_list");

	$(document).on("mousewheel DOMMouseScroll", function (e) {
			var delta = (e.originalEvent.wheelDelta && (e.originalEvent.wheelDelta > 0 ? 1 : -1)) ||  // chrome & ie
					(e.originalEvent.detail && (e.originalEvent.detail > 0 ? -1 : 1));              // firefox
			if (delta > 0) {
				
				//监听会话内容的滚动条
				var msg_top = pc_show_all_msg_obj.css('top');		
				msg_top = Math.abs(msg_top.replace('px',''));	
				//console.log("向上滚"+msg_top);
				//console.log("高_"+$(".pc_show_all_msg").height()) 
				if( msg_top<100 && msg_scroll==true){
					if(show_msg_top>0||show_msg_page>1){
						if(chat_type=='tongji'){
							get_tongji_msg(tj_type);
						}else{
							showMoreMsg(uid);
						}				
					}			
				}
			} else if (delta < 0) {
				 
				//监听用户列表的滚动条
				var user_top = pc_msg_user_list_obj.css('top');
				user_top = Math.abs(user_top.replace('px',''));	
				//console.log("向下滚"+user_top);
				if(user_top-user_div_top>300 && user_scroll==true){
					//console.log(user_div_top);
					user_div_top = user_top;		
					showMore_User();
				}
			}
	});
	
	setInterval(function() {

		//监听会话内容的滚动条
		var msg_top = pc_show_all_msg_obj.css('top');		
		msg_top = Math.abs(msg_top.replace('px',''));	
		//console.log("高_"+$(".pc_show_all_msg").height()) 
		if( msg_top<100 && msg_scroll==true){
			if(show_msg_top>0||show_msg_page>1){
				if(chat_type=='tongji'){
					get_tongji_msg(tj_type);
				}else{
					showMoreMsg(uid);
				}				
			}			
		}
		
		//监听用户列表的滚动条
		var user_top = pc_msg_user_list_obj.css('top');
		user_top = Math.abs(user_top.replace('px',''));	
		if(user_top-user_div_top>300 && user_scroll==true){
			//console.log(user_div_top);
			user_div_top = user_top;		
			showMore_User();
		}

		//setInterval(function() {
		//	if(user_scroll==true)showMore_User();	//定时把他们全加载出来,方便做搜索使用.其实上面的滚动可删除了
		//}, 4000);

		//if(maxid>=0)check_new_showmsg();				

	}, 1000*10000);//永远不执行


	
	$(".friends_list li > p").click(function(){
		if($(this).find('i').is('.fa-chevron-up')){
			$(this).find('i').removeClass('fa-chevron-up');
			$(this).find('i').addClass('fa-chevron-down');
			$(this).parent().children('div').show();
		}else{
			$(this).find('i').removeClass('fa-chevron-down');
			$(this).find('i').addClass('fa-chevron-up');
			$(this).parent().children('div').hide()
		}
	});

	goto_bottom(500)

	//统计数据的类型选择
	var tongji_num = 0;//parseInt($("#tongji_num").html());
	$("#tongji li").each(function(i){
		var that = $(this);
		var type = that.data('type');
		that.click(function(){
			$("#send_user_name").html(that.find('span').html());
			$("#tongji li").removeClass('icon_active');
			that.addClass('icon_active');
			tj_type = type;
			show_msg_page = 1;
			tongji_num = tongji_num-parseInt(that.find('em').html());
			if(tongji_num<1){
				$("#tongji_num").hide();
			}else{
				$("#tongji_num").html(tongji_num);
			}
			that.find('em').hide();
			get_tongji_msg(type)
		});
		setTimeout(function(){
			//各种动态的新数据统计		
			$.get(tongjiCountUrl+'?type='+type,function(res){
				if(res.code==0 && res.data>0){
					that.find('em').html(res.data>999?'99+':res.data);
					that.find('em').addClass('ck');
					tongji_num = tongji_num+res.data;
					$("#tongji_num").html(tongji_num>999?'99+':tongji_num);
					$("#tongji_num").css('display','block');
				}else{
					that.find('em').hide();
				}
			});
		},2000*i+2000);
	})


	jQuery.getScript("/public/static/js/base64uppic.js").done(function() {
        exif_obj = true;
    }).fail(function() {
        layer.msg('/public/static/js/base64uppic.js加载失败',{time:800});
	});

	
	//上传图片
	$('#fileToUpload').change(function(){
		var pics = [];
        uploadBtnChange($(this).attr("id"),'compressValue',pics,function(url,pic_array){
			if(pic_array[0].indexOf('://')==-1 && pic_array[0].indexOf('/public/')==-1){
				pic_array[0] = '/public/'+pic_array[0];
			}
			$("#input_box").val("<img src='"+pic_array[0]+"' class='big' />"+$("#input_box").val());			
		 });
    });
	
	$('#give_hongbao').click(function(){
		if(uid>0){
			layer.alert('只有群聊才能发红包');
			return ;
		}
		layer.open({
				type: 2,
				shadeClose: true,
				shade: 0.3,
				area: ['800px', '650px'],
				content: '/member.php/member/plugin/execute/plugin_name/hongbao/plugin_controller/content/plugin_action/add/mid/1.html?fromtype=msg&ext_id='+(-uid),
			});
	});


	$("#input_box").focus(function(){
       $('.windows_input').css('background','#fff');
       $('#input_box').css('background','#fff');
	});

    $("#input_box").blur(function(){
       $('.windows_input').css('background','');
       $('#input_box').css('background','');
    });

	//发送消息
	var allowsend = true;
	function postmsg(){
		var content = $(".msgcontent").val();
		if(content==''){
			layer.alert('消息内容不能为空');
			return ;
		}else if(allowsend == false){
			layer.alert('请不要重复发送信息');
			return ;
		}
		$(".msgcontent").val('');
		allowsend = false;
		var push_id = 0;
		if(ws_url!=''){
			push_id = (Math.random()+'').substring(2);
			//if(ws_have_link!=true){	//如果中断了,就要重连
			//	ws_connect();
			//}
			var time = 0;
			if(ws_have_link!=true){	//上面链接需要时间
				layer.msg("请稍候,正在建立连接...");
				time = 3000;
			}
			setTimeout(function(){
				w_s.send('{"type":"qun_sync_msg","data":' + JSON.stringify( {'content':content,'push_id':push_id} ) + '}');
			},time);			
		}
		$.post(postMsgUrl,{'uid':uid,'content':content,'push_id':push_id},function(res){

			if(ws_url==''){	//没有设置WS的话,就用AJAX轮询
				//发布信息后,代表存在互动,缩短刷新时间
				//list_time = 5;
				clearInterval(check_new);
				check_new = setInterval(function(){
					//if(maxid>=0)
					check_new_showmsg();
				},1500);
			}

			allowsend = true;
			if(res.code==0){				
				//layer.msg('发送成功',{time:500});
				$("#hack_wrap").hide(100);
			}else{
				//$(".msgcontent").val(content);
				layer.alert('本条信息已发出,但并没有入库,原因:'+res.msg);
			}
		});
	}

	$("#send").click(function(){
		postmsg();
	});

	$("#input_box").unbind('keydown').bind('keydown', function(e){
		console.log(e.ctrlKey +'  '+e.keyCode);
		if(e.ctrlKey && e.keyCode==13){
			//layer.msg('正在发送消息');
			postmsg();
		}
	});

	$("#show_face").click(function(){
		if( $("#hack_wrap").is(':hidden') ){
			$("#hack_wrap").show(100);
			$("#hack_wrap em").off("click");
			$("#hack_wrap em").click(function(){
				$("#hack_wrap em").removeClass('ck');
				$(this).addClass('ck');
				$(".msgcontent").val( $(".msgcontent").val() + '[face' + $(this).data('id') + ']' )
			});			
		}else{
			 $("#hack_wrap").hide(500);
		}
	});
	

})

var severUrl = "/index.php/index/attachment/upload/dir/images/from/base64/module/bbs.html";

//添加删除信息的功能按钮
function add_btn_delmsg(){
	$(".office_text .del").off('click');
	$(".office_text .del").click(function(){
		var id = $(this).data("id");
		var that = $(this);
		if(pushIdArray[id]!=undefined){
			id = pushIdArray[id];
		}
		$.get("/member.php/member/wxapp.msg/delete.html?id="+id,function(res){
			if(res.code==0){
				layer.msg("删除成功");
				that.parent().hide();
			}else{
				layer.alert(res.msg);
			}
		});
	});
	$(".office_text .big").off('click');
	$(".office_text .big").click(function(){
		window.open($(this).attr('src'));
	});
	$(".office_text .hack-hongbao").each(function(){
		var id = $(this).data("id");
		var title = $(this).data("title");
		var str = `<a href="#" title="${title}" onclick="layer.open({type: 2,title: '${title}',shadeClose: true,shade: 0.3,area: ['600px', '600px'],content: '/index.php/p/hongbao-content-show/id/${id}.html'});"><img src="/public/static/plugins/voicehb/hongbao.png"></a>`;
		$(this).html(str);
	});
}

function pc_qun_hot(){	//异步加载执行的函数
	$("#hot_qunzi").append( $(".pc_qun_hot").html() );
	add_friend_click_fun();
}
function pc_qun_myjoin(){	//异步加载执行的函数
	$("#my_join_qunzi").append( $(".pc_qun_myjoin").html() );
	add_friend_click_fun();
}
function pc_qun_myvisit(){	//异步加载执行的函数
	$("#my_visit_qunzi").append( $(".pc_qun_myvisit").html() );
	add_friend_click_fun();
}

function pc_myfriend(){	//异步加载执行的函数
	$("#my_friend").find('.friends_box').remove();
	$("#my_friend").append( $("#friends_tag").html() );
	add_friend_click_fun();

	get_friend_data('my_idol');
	get_friend_data('my_fans');
	get_friend_data('my_blacklist');
}

//添加好友
function friend_act_add(uid){
	$.get(FriendActUrl+"?type=add&uid="+uid,function(res){
		if(res.code==0){
			layer.msg(res.msg);			
			get_friend_data('my_friend');
			get_friend_data('my_idol');
			get_friend_data('my_fans');
			get_friend_data('my_blacklist');
		}else{
			layer.alert(res.msg);
		}
	});
}

//删除好友
function friend_act_del(uid){
	$.get(FriendActUrl+"?type=del&uid="+uid,function(res){
		if(res.code==0){
			layer.msg(res.msg);
			get_friend_data('my_friend');
			get_friend_data('my_idol');
			get_friend_data('my_fans');
			get_friend_data('my_blacklist');
		}else{
			layer.alert(res.msg);
		}
	});
}

//加黑名单
function friend_act_bad(uid){
	$.get(FriendActUrl+"?type=bad&uid="+uid,function(res){
		if(res.code==0){
			layer.msg(res.msg);
			get_friend_data('my_friend');
			get_friend_data('my_idol');
			get_friend_data('my_fans');
			get_friend_data('my_blacklist');
		}else{
			layer.alert(res.msg);
		}
	});
}

//给好友列表添加点击事件
function add_friend_click_fun(){	
	$(".friends_list .friends_box").each(function(){
		var that = $(this);
		var btn = that.find(".friends_text");
		var btn_add = that.find(".add");
		var btn_del = that.find(".del");
		var btn_bad = that.find(".bad");
		
		//添加好友
		btn_add.off("click");
		btn_add.click(function(){
			friend_act_add( that.data("uid") );
		});
		
		//移除好友
		btn_del.off("click");
		btn_del.click(function(){
			friend_act_del( that.data("uid") );
		});
		
		//加黑名单
		btn_bad.off("click");
		btn_bad.click(function(){
			friend_act_bad( that.data("uid") );
		});


		btn.off("click");
		that.mouseout(function(){
			that.find("i").hide();
		});
		that.mouseover(function(){			
			that.find("i").show();
			$("#my_friend .friends_box i.add").hide();
			$("#my_blacklist .friends_box i.bad").hide();
			$("#my_idol .friends_box i.bad").hide();
			//$("#my_idol .friends_box i.add").hide();
		});
		btn.click(function(){
			$(".friends_list .friends_box").removeClass('user_active');
			//$(".friends_list .friends_box i").hide();
			that.addClass('user_active');
			//that.find("i").show();
			uid = that.data("uid");
			set_user_name(uid);
			show_msg_page = 1;
			showMoreMsg(uid);
		})
	});
}

//获取我的好友或粉丝列表
function get_friend_data(ty){
	var page = 1;
	var url = MyFriendUrl + page + "&type=";
	if(ty=='my_idol'){	//我的偶像
		url += "0&suid=&uid="+my_uid;
	}else if(ty=='my_fans'){	//我的粉丝
		url += "0&uid=&suid="+my_uid;
	}else if(ty=='my_blacklist'){	//黑名单
		url += "-1&uid=&suid="+my_uid;
	}else if(ty=='my_friend'){	//我的好友
		url += "1,2&uid=&suid="+my_uid;
	}
	$.get(url,function(res){
		if(res.code==0){
			if(page==1)$('#'+ty).find('.friends_box').remove();
			if(res.data!=''){				
				$('#'+ty).append(res.data);
				add_friend_click_fun();
			}
		}
	})
}


function format_nickname(){
	if(uid>0 || user_num<1){
		return ;
	}
	$('.pc_show_all_msg .name').each(function(){
		var _uid = $(this).data('uid');
		if(typeof(user_list[_uid]) == 'object' && typeof(user_list[_uid].nickname)!='undefined' && user_list[_uid].nickname!=''){
			$(this).html(user_list[_uid].nickname);
		}
	});
}

//获取圈子所有成员
function get_qunuser_list(uid){
	if(uid<0){
		var id = -uid;
		$.get("/index.php/qun/wxapp.member/get_member.html?id="+id+"&rows=1000&order=update_time&get_username=0",function(res){
			if(res.code==0){
				res.data.forEach((rs)=>{
					user_list[rs.uid] = rs;
				});
				user_num = res.data.length;
				format_nickname();				
			}
		});
	}		
}



//设置视频直播的播放器
function set_live_player(res,type){
	
		if(type=='cknew' && res.data.length>0){
			res.data.forEach((rs)=>{
				if(rs.content.indexOf('live_video_start')>0){
					console.log('中断过的 . 重新发起直播');
					if(have_load_live_player==true)have_load_live_player = false;	//中断过的 . 重新发起直播
				}
			});
		}

		if(have_load_live_player!=true && typeof(res.ext)!='undefined' && (typeof(res.ext.live_video)!='undefined'||typeof(res.ext.vod_voice)!='undefined') ){
			have_load_live_player = true;
			if(typeof(res.ext.vod_voice)!='undefined'){
				vod_voice_play(res.ext.vod_voice);	//设置点播转直播音频播放器
			}else{
				ck_play(res.ext.live_video.flv_url);	//设置直播视频播放器
			}			
		}else if(typeof(res.ext)!='undefined' && typeof(res.ext.live_video)=='undefined' && typeof(res.ext.vod_voice)=='undefined' ){
			have_load_live_player = false;
		}
}
var video_index = null;
function ck_play(url){
	if(video_index!=null){
		$("#video_2").remove();
		layer.close(video_index);
	}
    video_index = layer.open({
    type: 1,
    offset: ['10px', '10px'],
    anim: 5,
    fixed: false,
    shade: 0,
    title: '直播中',
    area: ['500px', '300px'],
    content: "<div id=\"video_2\" style=\"width:100%;height:100%;\"></div><script type=\"text/javascript\">var videoObject={container:'#video_2',variable:'player',flashplayer:false,live:true,loaded:'loadedHandler',video:'" + url + "'};var player=new ckplayer(videoObject);function loadedHandler(){player.addListener('loadedmetadata',loadedMetaDataHandler);}function loadedMetaDataHandler(){var metaData=player.getMetaDate();console.log(metaData);var index = parseInt(parent.layer.getFrameIndex(window.name))+1;console.log(index);var width=metaData['streamWidth'];var height=metaData['streamHeight'];if(width/height>=1){layer.style(index,{width:'500px',height:'325px'});return false}else{layer.style(index,{width:'250px',height:'500px'});return false}}<\/script>",
    btn: ['横屏播放', '竖屏播放'],
	yes: function(index, layero) {
		$(".layui-layer-btn0").css({"border-color": "#4898d5","background-color": "#2e8ded","color": "#fff"});
		$(".layui-layer-btn1").css({"border-color": "#f1f1f1","background-color": "#f1f1f1","color": "#333"});
		layer.style(index, {width: '500px',height: '300px'});
		return false
	},
	btn2: function(index) {
		$(".layui-layer-btn1").css({"border-color": "#4898d5","background-color": "#2e8ded","color": "#fff"});
		$(".layui-layer-btn0").css({"border-color": "#f1f1f1","background-color": "#f1f1f1","color": "#333"});
		layer.style(index, {width: '300px',height: '500px'});
		return false
	}
	});
}

var voice_iframeWin,voice_urls;

function vod_voice_play(obj,etype){
	var oo;
	if(etype=='ok'||etype=='err'){	//请求完成
		var url_array = obj.play_urls;
		if(etype=='ok'){
			oo = {index:obj.play_index,time:obj.play_time}
		}
	}else{	//请求直播信息
		voice_urls = obj.urls;
		var url_array = obj.urls;
		var timer = setInterval(function() {
			if(clientId!=''){
				clearInterval(timer);
				w_s.send('{"type":"user_ask_quner","tag":"ask_vod_voice_state","user_cid":"'+clientId+'"}');	//向圈主请求当前播放信息
			}	
		}, 1000);
		return ;
	}	
	
	layer.open({  
		  type: 2,    
		  title: '直播开始了...',  
		  fix: false,  
		  shadeClose: false,  
		  offset: ['10px', '10px'],
		  shade: 0,
		  maxmin: true,
		  scrollbar: false,
		  closeBtn:2,  
		  area: ['450px', my_uid==quninfo.uid?'390px':'345px'],  
		  content: "/public/static/libs/bui/pages/chat/vod_player.html?aid="+Math.abs(uid)+"&cid="+clientId,
		  success: function(layero, index){  
			var body = layer.getChildFrame('body', index);  //body.find('#dd').append('ff');    
			voice_iframeWin = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：iframeWin.method();  
			voice_iframeWin.voice_player(url_array,oo);
			//$('.layui-layer-min').trigger("click");
			setTimeout(function(){
				//$('.layui-layer-max').trigger("click");
				//body.find(".player-button").hide();
			},3000);	
			if(my_uid==quninfo.uid){
				setTimeout(function(){
					body.find('.syscn').show();
					body.find('.jp-previous').show();
					body.find('.jp-next').show();
					body.find('.jp-play').off('click');
				},2000);
			}
		  }
    });
}
//用户请求同步播放 给框架窗口使用
//function user_ask_sync_vod_voice(){
//	w_s.send('{"type":"user_ask_quner","tag":"ask_vod_voice_sync","user_cid":"'+clientId+'"}');
//}

$(function(){
	$("#vod_voice").click(function(){
		if(uid>=0){
			layer.alert('只有群聊才能直播!');
			return ;
		}
		layer.open({  
		  type: 2,    
		  title: '音频点播转直播',  
		  area: ['650px','600px'],  
		  content: "/member.php/member/vod/index.html?type=voice&aid="+Math.abs(uid),
		});
	});

	$("#live_video").click(function(){
		if(uid>=0){
			layer.alert('只有群聊才能直播!');
			return ;
		}
		$.get("/index.php/p/alilive-api-url.html?id="+Math.abs(uid),function(res){
			if(res.code==0){				
				layer.open({
                    type: 1,
					title:'直播推流与拉流地址',
                    shift: 1,
					area:['600px','400px'],
                    content: $("#live_video_warp").html(),
				});
				$(".live_video_warp").last().find(".codeimg img").attr('src',res.data.push_img);
				$(".live_video_warp").last().find(".push_url").val(res.data.push_url);
				$(".live_video_warp").last().find(".m3u8_url").val(res.data.m3u8_url);
				$(".live_video_warp").last().find(".rtmp_url").val(res.data.rtmp_url);
				$(".live_video_warp").last().find(".flv_url").val(res.data.flv_url);
			}else{
				layer.alert(res.msg);
			}
		});
	});
});























