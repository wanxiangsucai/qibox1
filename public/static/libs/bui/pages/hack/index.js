loader.define(function(require,exports,module) {


	var pageview = {};      // 页面的模块, 包含( init,bind )
	var type = 'bbs';
	var uid = 0;
	
    /**
     * [init 页面初始化]
     * @return {[type]} [description]
     */
    pageview.init = function () {
		get_list_data();
    }


	//获取相应的频道数据列表
	function get_list_data(){
		var url = "/index.php/"+type+"/wxapp.index/listbyuid.html?rows=30";
		$.get(url,function(res){
			if(res.code==0){
				if(res.data.length>0){				
					$('.list-hack').append( pageview.format_friend_data(res.data) );
					if(res.paginate.total>0)$('.list-hack').prev().find("em").html(res.paginate.total); //有几位好友
					add_action();
				}
			}
		});		
	};
	
	//绑定按钮事件
	function add_action(){

		router.$("#add_model").click(function(){
			bui.load({ 
				url: "/public/static/libs/bui/pages/frame/show.html",
				param:{
					url:"/member.php/"+type+"/content/postnew.html?job=bui",
				}
			});
		});

		router.$('.list-hack .add').click(function(){
			var id = $(this).data("id");
			var m_title = $(this).data("title");
			var m_content = $(this).data("content");
			var m_picurl = $(this).data("picurl");
			if(m_picurl==null) m_picurl = '';
			var content = `<ul class="model-list model-${type}" data-id="${id}" data-type="${type}" data-imgurl="${m_picurl}"><li class="model-title">${m_title}</li><li class="model-more"><div class="model-content">${m_content}</div><div class="model-picurl"><img src="${m_picurl}" onerror="$(this).parent().hide()"/></div></li></ul>`;
			$.post("/member.php/member/wxapp.msg/add.html",{
				'uid':uid,
				'content':content,
				'ext_id':id,
				'ext_sys':type,
				},function(res){		
				if(res.code==0){
					layer.msg('添加成功');
					bui.back();
				}else{
					layer.alert('添加失败:'+res.msg);
				}
			});
		});

		//添加侧滑菜单
		bui.listview({
				id   : '.list-hack',
				data : [{text : "删除",classname : "danger"},{text : "修改",classname : "warning"}],
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

	pageview.format_friend_data = function(array){
		var str = "";
		array.forEach((rs)=>{
			var content = rs.content.substring(0,20);
			var title = rs.title.substring(0,15);
			str +=`
			<li class="list-item" data-uid="${rs.id}">
				<div class="bui-btn bui-box">
					<a href="/index.php/${type}/content/show/id/${rs.id}.html" class="iframe"><img class="ring ring-group" src="${rs.picurl}" onerror="this.src='/public/static/images/nopic.png'"/></a>                                
					<div class="span1">
						<h3 class="item-title">
							${title}
						</h3>
						<p class="item-text">${content}</p>
					</div>
					<i class="icon- primary add" data-id="${rs.id}" data-title="${rs.title}" data-picurl="${rs.picurl}" data-content="${rs.content}"><i class="fa fa-plus-circle"></i></i>
				</div>
			</li>
			`;
		});
		return str;
	}

	var getParams = bui.getPageParams();
		getParams.done(function(result){
			if(result.uid==undefined){
				layer.alert('uid参数不存在');
			}else if(result.type==undefined){
				layer.alert('type参数不存在');
			}
			uid = result.uid;
			type = result.type;
		})


    // 控件初始化
    pageview.init();

    // 输出模块
    module.exports = pageview;
})
