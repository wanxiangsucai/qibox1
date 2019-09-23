loader.define(function(require,exports,module) {


	var pageview = {},      // 页面的模块, 包含( init,bind )
		uiPullrefresh,      // 消息,电话公用的下拉刷新控件
		mainHeight,
		uiMask,             // 公共遮罩
		uiListviewMessage,  // 消息侧滑菜单
		uiDropdownMore,     // 下拉菜单更多
		user_scroll=true,	//滚动条
		ListMsgUserPage=1,	//用户列表分页
		uiSlideTabMessage;  // 顶部tab

    /**
     * [init 页面初始化]
     * @return {[type]} [description]
     */
    pageview.init = function () {

        mainHeight = $(window).height() - $("#tab-home-header").height()- $("#tabDynamicNav").height();
        var slideHeight = parseInt(mainHeight) - $(".bui-searchbar").height();
	

		var load_friend = true;
        //初始化顶部TAB
        uiSlideTabMessage = bui.tab({
            id       : "#tabMessage",
            menu     : "#tabMessageNav",
            height   : slideHeight,
			scroll   : true,
            swipe    : true,   //不允许通过滑动触发
            animate  : true,    //点击跳转时不要动画
			onBeforeTo: function(e) {//   目标索引  e.currentIndex e.prevIndex
               if(load_friend){	//console.log('好友加载成功')
				   load_friend = false;
					$("#my_friend").html(myFriendList);
					$("#my_friend").prev().find("em").html(myFriendNum);     
					get_friend_data('my_friend');	//这里其实是重复加载了,主要是为了加上侧滑菜单
					get_friend_data('my_idol');
					get_friend_data('my_fans');
					get_friend_data('my_blacklist');
			   }
           }
        });
        //初始化消息的侧滑菜单

        uiListviewMessage = bui.listview({
                id: "#listview",
                data: [{ "text": "置顶", "classname":"primary"},{ "text": "删除", "classname":"danger"}],
                callback: function (e) {
                    // this 为滑动出来的操作按钮
                    var $this = $(e.target);

                    var text = $this.text();
                        if( text == '删除' ){
                            bui.confirm("确定要删除吗",function (e) {
                                //this 是指点击的按钮
                                var text2 = $(e.target).text();
                                if( text2 == "确定"){
                                    // 执行删除整行操作
                                    $this.parents(".list-item").fadeOut(300,function (e) {
                                        $(this).remove();
                                    });
                                }
                            })
                        }
                    // 不管做什么操作,先关闭按钮,不然会导致第一次点击无效.
                    this.close();
                }
            });

        

        // 初始化下拉刷新
        //uiPullrefresh = bui.pullrefresh({
         //   id        : "#messageScroll",
         //   height: mainHeight,
         //   onRefresh : getData
        //});

/*------------消息 电话 end --------------*/

/*------------右上角更多菜单 start --------------*/

        // 初始化下拉更多操作
        uiDropdownMore = bui.dropdown({
          id: "#more",
          showArrow: true,
          width: 160
        });
        // 下拉菜单有遮罩的情况
        uiMask = bui.mask({
          appendTo:"#main",
          opacity: 0.03,
          zIndex:9,
          callback: function (argument) {
            // 隐藏下拉菜单
            uiDropdownMore.hide();
          }
        });
        // 通过监听事件绑定
        uiDropdownMore.on("show",function () {
          uiMask.show();
        })
        uiDropdownMore.on("hide",function () {
          uiMask.hide();
        });

/*------------右上角更多菜单 end --------------*/
		
		//初次这里有可能会加载晚一步
		if(MsgUserList!=''){
			$("#listview").html(MsgUserList);
		}
		
		var btn_chat = $("#tabDynamicNav .bui-box-vertical").eq(0);
		setInterval(function() {
			var url = window.location.href;
			if(url.indexOf('#/')==-1 && btn_chat.hasClass('active')==true ){	//跳转到了其它页面,就不要再执行
				check_list_new_msgnum(); //刷新有没有新用户发消息 过来
			}			
		}, 3000);
		
		var that = $("#listview");
		that.parent().scroll(function () {
			var h = that.height()-that.parent().height()-that.parent().scrollTop();			
			if(h<300 && user_scroll==true){
				console.log(h);
				layer.msg('数据加载中,请稍候...',{time:3000});
				showMore_User();	//显示更多用户列表
			}
		});

    }

    pageview.bind = function (argument) {

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
				//if(page==1)$('#'+ty).find('.friends_box').remove();
				if(res.data!=''){				
					$('#'+ty).append(res.data);
					if(res.paginate.total>0)$('#'+ty).prev().find("em").html(res.paginate.total); //有几位好友
					//添加侧滑菜单
					bui.listview({
							id   : '#'+ty,
							data : [{text : "删除",classname : "danger"},{text : "拉黑",classname : "warning"}],
							callback : function(e,ui) {
								var text = $(e.target).text().trim();
								if( text == '删除' ){
									$(e.target).parents(".list-item").fadeOut(300,function () {
										$(this).remove();
									});
								}
								// 关闭侧滑
								ui.close();
							}
						});
				}
			}
		})
	}

	//显示更多用户列表
	function showMore_User(){
		ListMsgUserPage++;
		user_scroll = false;
		$.get(ListMsgUserUrl+ListMsgUserPage,function(res){  
			//console.log(res);
			if(res.code==0){
				if(res.data==''){
					layer.msg("已经显示完了！",{time:500});
				}else{
					$('#listview').append(res.data);
					$.each(res.ext.s_data,function(i,rs){
						uid_array[rs.f_uid] = rs.id;
					});
					user_scroll = true;
					$("#listview").parent().scrollTop($("#listview").parent().scrollTop()-200);
				}
			}else{
				layer.msg(res.msg,{time:2500});
			}
		});
	}
	
	var uid_array = [];   //每个用户的最新消息ID
	//刷新有没有新用户发消息 过来
	function check_list_new_msgnum(){
		$.get(ListMsgUserUrl+"1",function(res){
			if(res.code==0){			
				$.each(res.ext.s_data,function(i,rs){
					//出现新的消息新用户，或者是原来新消息的用户又发来了新消息
					if(typeof(uid_array[rs.f_uid])=='undefined'||rs.id>uid_array[rs.f_uid]){
						$('#listview').html(res.data);
						//add_click_user();
					}
					//新消息已读
					if(rs.new_num<1){
						$('#listview  .list_'+rs.f_uid+' .bui-badges').removeClass('badges-ck');
						$('#listview  .list_'+rs.f_uid+' .bui-badges').html(rs.num>999?'99+':rs.num);
					}
					//console.log(rs.f_uid+'='+rs.id+'='+uid_array[rs.f_uid]);
					uid_array[rs.f_uid] = rs.id;
				});
			}
		});
	}


    // 下拉刷新以后执行数据请求
    function getData () {

        bui.ajax({
            url : "/public/static/libs/bui/userlist.json",
            data: {
                pageindex:1,
                pagesize:4
            }
        }).done(function(res) {

            //还原刷新前状态
            uiPullrefresh.reverse();

        }).fail(function (res) {
            //请求失败变成点击刷新
            uiPullrefresh.fail();
        })
    }

    // 控件初始化
    pageview.init();

    // 输出模块
    module.exports = pageview;
})
