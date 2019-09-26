loader.define(function(require,exports,module) {
    
	var pageview = {},      // 页面的模块, 包含( init,bind )
		uiPullrefresh,      // 消息,电话公用的下拉刷新控件
		uiAccordionDevice,  // 我的设备折叠菜单
		uiAccordionFriend;  // 我的好友折叠菜单
    
	store.compile(".bui-bar");	//重新加载全局变量数据


    /**
     * [init 页面初始化]
     * @return {[type]} [description]
     */
    pageview.init = function () {
        
        // 页面动态加载,需要重新初始化
        bui.init({
            id: "#tab-qun"
        })
        var mainHeight = $(window).height() - $("#tab-contact-header").height()- $("#tabDynamicNav").height();



        // 初始化下拉刷新
        //uiPullrefresh = bui.pullrefresh({
        //    id        : "#contactScroll",
        //    height: mainHeight,
        //    onRefresh : getData
       // });

        // 初始化设备折叠菜单
       // uiAccordionDevice = bui.accordion({
       //     id:"#device"
       // });
        
		$("#myjoin_qunzi").html(qunObj.myjoin_val);
		$("#myjoin_qunzi").parent().prev().find(".time").html(qunObj.myjoin_num )
		$("#myvisit_qunzi").html(qunObj.myvisit_val);
		$("#myvisit_qunzi").parent().prev().find(".time").html(qunObj.myvisit_num);
		$("#hot_qunzi").html(qunObj.hotqun_val);
		$("#hot_qunzi").parent().prev().find(".time").html(qunObj.hotqun_num);


        // 初始化好友折叠菜单
        uiAccordionFriend = bui.accordion({
            id:"qun_zi"
        });

		//showFirst显示第一个
        uiAccordionFriend.showFirst();


		
        
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