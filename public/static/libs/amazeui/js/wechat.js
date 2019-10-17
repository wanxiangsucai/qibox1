

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

//input box focus
$(document).ready(function(){
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
		$.post(postMsgUrl,{'uid':uid,'content':content,},function(res){
			allowsend = true;
			if(res.code==0){				
				layer.msg('发送成功');
				$("#hack_wrap").hide(300);
			}else{
				$(".msgcontent").val(content);
				layer.alert('发送失败:'+res.msg);
			}
		});
	}
	$("#send").click(function(){
		postmsg();
	});

	$("#input_box").unbind('keydown').bind('keydown', function(e){
		console.log(e.ctrlKey +'  '+e.keyCode);
		if(e.ctrlKey && e.keyCode==13){
			layer.msg('正在发送消息');
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
		$(".pc_msg_user_list li").removeClass('user_active');
		$(this).addClass('user_active');
		uid = $(this).data('uid');
		console.log(uid);
		show_msg_page = 1; //重新恢复第一页
		msg_scroll = true; //恢复可以使用滚动条
		showMoreMsg(uid);	//加载相应用户的聊天记录
		set_user_name(uid); //设置当前会话的用户名
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

function format_chat_msg(array){
	if(typeof(array)=='string'){
		return array;
	}
		var str = '';
		var str_name = '';
		var str_del = '';
		array.forEach((rs)=>{
			str_name = (rs.qun_id && rs.uid!=my_uid)?`<div class="name" onclick="$('#input_box').val('@${rs.from_username} ').focus()">@${rs.from_username}</div>`:'';
			str_del = (rs.uid==my_uid||rs.touid==my_uid) ? `<i data-id="${rs.id}" class="del glyphicon glyphicon-remove-circle"></i>` : '';
			str += `<li class="` + ( rs.uid==my_uid ? 'me' : 'other' ) + `">
						<dd class="time" data-time="${rs.full_time}"><a>${rs.create_time}</a></dd>
						${str_name}
						<a href="/member.php/home/${rs.uid}.html" class="user_icon" target="_blank"><img src="${rs.from_icon}" onerror="this.src='/public/static/images/noface.png'" title="${rs.from_username}"></a><span class="content">${rs.content}</span>
						${str_del}		
						</li>`;
		});
		return str;
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
		//console.log(res.data);
		if(res.code==0){
			if(show_msg_page==1){
				maxid = res.ext.maxid;
			}
			set_main_win_content(res);
		}else{
			layer.msg(res.msg,{time:2500});
		}
	});
}

//往主窗口里边加入显示的数据
function set_main_win_content(res){
	layer.closeAll();
	var that = $('.pc_show_all_msg');
	res.data = format_chat_msg(res.data);
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

var chat_type = 'chat';   //主窗口当前应该加载哪类内容,执行哪个函数

var tj_type = '';  //当前选择了哪种统计数据

var uid_array = [];   //每个用户的最新消息ID
var uid = 0;	//当前聊天用户的UID

var ListMsgUserPage = 1;	//所有信息用户列表
var show_msg_page = 1;	//会话记录分页
var msg_scroll = true;  //做个标志,不要反反复复的加载会话内容
var user_scroll = true;  //做个标志,不要反反复复的加载用户列表
var user_div_top = 0;	//当前信息用户列表滚动条坐标top系数
var show_msg_top = 0;  //当前对话框滚动条坐标top系数
var maxid = -1;
var need_scroll = false;

$(function(){
	
	var num = ck_num = 0;

	setInterval(function() {

		//监听会话内容的滚动条
		var msg_top = $(".pc_show_all_msg").css('top');
		
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
		var user_top = $(".pc_msg_user_list").css('top');
		user_top = Math.abs(user_top.replace('px',''));	
		if(user_top-user_div_top>300 && user_scroll==true){
			//console.log(user_div_top);
			user_div_top = user_top;		
			showMore_User();
		}

		setInterval(function() {
			if(user_scroll==true)showMore_User();	//定时把他们全加载出来,方便做搜索使用.其实上面的滚动可删除了
		}, 4000);

		if(maxid>=0)check_new_showmsg();

		if(num%3==0)check_list_new_msgnum();
		

	}, 1000);


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


	//刷新最近的消息用户
	function check_list_new_msgnum(){
		$.get(ListMsgUserUrl+"1",function(res){
			if(res.code==0){			
				$.each(res.ext.s_data,function(i,rs){
					//出现新的消息新用户，或者是原来新消息的用户又发来了新消息
					if(typeof(uid_array[rs.f_uid])=='undefined'||rs.id>uid_array[rs.f_uid]){ console.log('有新的消息来了');
						$('.pc_msg_user_list').html(res.data);
						add_click_user();
						if(num>10 && window.Notification){	//消息提醒
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


	//刷新会话用户中有没有新消息
	function check_new_showmsg(){
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
			var that = $('.pc_show_all_msg');
			res.data = format_chat_msg(res.data);
			if(res.data!=""){	//有新的聊天内容
				var vh = that.height();
				//console.log( '原来的高度='+vh);
				that.prepend(res.data);
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
				$("#remind_online").show();
			}else{
				$("#remind_online").hide();
			}
		});
		ck_num++;
	}

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
	goto_bottom(500)

	//统计数据的类型选择
	var tongji_num = 0;//parseInt($("#tongji_num").html());
	$("#tongji li").each(function(){
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
				content: '/member.php/member/plugin/execute/plugin_name/hongbao/plugin_controller/content/plugin_action/add/mid/1.html?ext_id='+(-uid),
			});
	});
	

})

var severUrl = "/index.php/index/attachment/upload/dir/images/from/base64/module/bbs.html";

//添加删除信息的功能按钮
function add_btn_delmsg(){
	$(".office_text .del").click(function(){
		var id = $(this).data("id");
		var that = $(this);
		$.get("/member.php/member/wxapp.msg/delete.html?id="+id,function(res){
			if(res.code==0){
				layer.msg("删除成功");
				that.parent().hide();
			}else{
				layer.alert(res.msg);
			}
		});
	});
	$(".office_text .big").click(function(){
		window.open($(this).attr('src'));
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





































