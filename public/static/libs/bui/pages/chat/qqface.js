// 默认已经定义了main模块
loader.define(function() {

    var pageview = {};

    // 主要业务初始化
    pageview.init = function() {
        // 这里写main模块的业务
        //bui.hint("loadPart_part.js was loaded");
		var str = "";
		for(var i=1;i<23;i++){
			str += `<em data-id="${i}"><img src="/public/static/images/qqface/${i}.gif"></em>`;
		}
		//console.log(str);
		$(".list_qqface").html(str);
		$(".list_qqface em").click(function(){
			$(".list_qqface em").removeClass('ck');
			$(this).addClass('ck');
			$("#chatInput").val( $("#chatInput").val() + '[face' + $(this).data('id') + ']' );
			router.$("#btnSend").removeClass("disabled");
			router.$("#btnSend").addClass("primary");
		});
    }

    // 事件绑定
    pageview.bind = function() {
    }



    // 初始化
    pageview.init();
    // 绑定事件
    pageview.bind();
    
    return pageview;
})