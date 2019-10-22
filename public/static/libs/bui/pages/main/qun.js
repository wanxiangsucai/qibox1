loader.define(function(require,exports,module) {
    
	var pageview = {},      // 页面的模块, 包含( init,bind )
		pages = {},
		uiPullrefresh,      // 消息,电话公用的下拉刷新控件
		uiAccordionDevice,  // 我的设备折叠菜单
		uiAccordionFriend;  // 我的好友折叠菜单
	var urls = {
			lively:"/index.php/qun/wxapp.qun/index.html?rows=20&type=list&page=",
			myjoin:"/index.php/qun/wxapp.qun/myjoin.html?rows=20&page=",
			myvisit:"/index.php/qun/wxapp.qun/myvisit.html?rows=20&page=",
	};
    
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
        pageview.get_qunlist('lively');
		pageview.get_qunlist('myjoin');
		pageview.get_qunlist('myvisit');


        // 初始化好友折叠菜单
        uiAccordionFriend = bui.accordion({
            id:"qun_zi"
        });

		//showFirst显示第一个
        uiAccordionFriend.showFirst();


		
        
    }

	pageview.get_qunlist = function(type){
		var page = typeof(pages[type])=='undefined' ? 1 : pages[type];
		$.get(urls[type]+page,function(res){
			if(res.code==0){
				if(res.data.length<1){
					if(page>1){
						layer.msg('加载完了');
					}
				}else{
					var obj = router.$("#qunzi_"+type);
					obj.append( pageview.format_qun_data(res.data) );
					obj.parent().prev().find(".time").html(res.paginate.total);
				}
			}
		});
	}

	pageview.format_qun_data = function(array){
		var str = "";
		array.forEach((rs)=>{
			rs.content = rs.content.substring(0,20);
			str +=`
				<li class="bui-btn bui-box">
					<a href="${rs.url}"  class="iframe" title="${rs.title}"><img class="ring ring-pc" src="${rs.picurl}" onerror="this.src='/public/static/images/nopic.png'"/></a>
					<div class="span1 a" href="/public/static/libs/bui/pages/chat/chat.html?uid=-${rs.id}">
						<h3 class="item-title">${rs.title}</h3>
						<p class="item-text bui-text-hide">${rs.content}</p>
					</div>
					<!--<span class="bui-badges">0</span>-->
				</li>
			`;
		});
		return str;
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