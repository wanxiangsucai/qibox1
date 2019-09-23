loader.define(function(require,exports,module) {
    
	var pageview = {},      // 页面的模块, 包含( init,bind )
			uiPullrefresh,      // 消息,电话公用的下拉刷新控件
			scroll_get=true,  // 我的设备折叠菜单
			showtype='qun',		//显示圈子还是用户
			mid=0,				//圈子模型筛选
			uiAccordionFriend;  // 我的好友折叠菜单
    
	store.compile(".bui-bar");	//重新加载全局变量数据


    /**
     * [init 页面初始化]
     * @return {[type]} [description]
     */
    pageview.init = function () {
        
        // 页面动态加载,需要重新初始化
        bui.init({
            id: "#tab-contact"
        })
        var mainHeight = $(window).height() - $("#tab-contact-header").height()- $("#tabDynamicNav").height();



        // 初始化好友折叠菜单
       // uiAccordionFriend = bui.accordion({
        //    id:"qun_zi"
        //});

		//showFirst显示第一个
        //uiAccordionFriend.showFirst();

		window.HOST_TYPE = "2";
		window.BMap_loadScriptTime = (new Date).getTime();
		loader.import(["https://api.map.baidu.com/getscript?v=2.0&ak=MGdbmO6pP5Eg1hiPhpYB0IVd&services=&t=20190622163250","/public/static/js/bdmap.js","/public/static/js/map-gps.js"],function(){
			reload_map();
		});        
    }

	
	var that = $("#contactScroll");
	that.parent().scroll(function () {
			var h = that.height()-that.parent().height()-that.parent().scrollTop();
			if( h<300 && scroll_get==true){
				scroll_get = false;
				console.log(h);
				layer.msg('内容加截中,请稍候',{time:1500});
				showmorelist();
			}
	});

	function showmorelist(){
		showMapPosition(map_x,map_y)
	}

	var page = 1;
	var j = 0;

	//根据当前坐标位置去数据库按位置远近排序读取
	function showMapPosition(longitude,latitude){
		var url;
		if(showtype == 'user'){
			url = "/index.php/index/wxapp.member/get_near.html?rows=15";
		}else{
			url = "/index.php/qun/wxapp.near/index.html?rows=15&mid="+mid;
		}
		$.get(url+"&point=" + longitude + ',' + latitude + "&page=" + page + '&' + Math.random(),function(res){
			var d ='';
		   if(res.code==0){
			   if(res.data.length==0){			   
				   layer.msg("已经显示完了！",{time:500});
			   }else{
					page++;
				    res.data.forEach(function(rs){
						j++;
						var toid;
						if(showtype == 'user'){
							toid = rs.uid;
						}else{
							toid = -rs.id;
						}
						d += `
							<li class="bui-btn bui-box">
                                    <a href="${rs.url}" class="iframe" target="_blank"><img class="ring ring-pc" src="${rs.picurl}" onerror="this.src='/public/static/images/nopic.png'"/></a>
                                <div class="span1 a" href="/public/static/libs/bui/pages/chat/chat.html?uid=${toid}">
                                    <h3 class="item-title">
                                        ${rs.title}
                                    </h3>
                                    <p class="item-text bui-text-hide" data-map="${rs.map_x},${rs.map_y}">${rs.content}</p>
                                </div>
                            </li>	
						`;
				   });
				   $("#near_qunzi").append(d);
				   $("#list_content .show_address .time").html(res.paginate.total);
				   
				   show_distance(longitude,latitude);
				   layer.closeAll();
				   scroll_get = true;
			   }			  
		   }
		});
		
		//显示当前位置的街道名
		var gpsPoint = new BMap.Point(longitude, latitude);
		BMap.Convertor.translate(gpsPoint, 0, function(point){
		//alert('x:'+point.lng+' y:'+point.lat);
			var geoc = new BMap.Geocoder();
			geoc.getLocation(point, function(rs){
				var addComp = rs.addressComponents;
				$(".show_address .span1").html(' 当前位置：'+addComp.district + addComp.street + addComp.streetNumber);
			});
		});
	}


	var map_x = 0;
	var map_y = 0;
	//获取当前坐标位置
	function reload_map(){
		page = 1;
		$("#near_qunzi").html('');
		var geolocation = new BMap.Geolocation();
		geolocation.getCurrentPosition(function(result){
			if(this.getStatus() == window.BMAP_STATUS_SUCCESS){
			  map_x = result.point.lng;
			  map_y = result.point.lat;
			  $.get("/member.php/member/wxapp.user/edit_map.html?type=1&point="+map_x+","+map_y);
			  showMapPosition(map_x,map_y);
				//var geoc = new BMap.Geocoder();
				//geoc.getLocation(result.point, function(rs){
				//	var addComp = rs.addressComponents;
				//	alert(addComp.district + addComp.street + addComp.streetNumber);
				//});

				//gg = GPS.bd_decrypt(result.point.lat, result.point.lng);	//百度转谷歌
				//wgs = GPS.gcj_decrypt(gg.lat, gg.lon); //谷歌转GPS
				//showMapPosition(wgs.lon,wgs.lat);
			} else {
				alert('failed:'+this.getStatus());
			}        
		},{enableHighAccuracy: true})
	}
	//reload_map();

	//计算距离当前位置的公里数
	function show_distance(map_lat,map_lon){
		$('#near_qunzi').children('li').each(function(){
			var this_map=$(this).find('.item-text').data('map');
			var thismap=this_map.split(",");
			var this_lon = thismap[0];
			var this_lat = thismap[1];
			var show_word='距离';
			if(this_lon!=0 &&this_lat!=0){
				var show_map_str = GPS.distance(map_lat,map_lon,this_lon,this_lat);
				console.log(map_lat+"="+map_lon+"="+this_lon+"="+this_lat);
				var kilometres = Math.floor(show_map_str/1000);  
				var metres=Math.floor(show_map_str%1000);				
				if(kilometres>0){
					show_word+='<font style="color:red;">'+kilometres+'</font>公里';
				}
				show_word += isNaN(metres)?'未知':'<font style="color:red;">'+metres+'</font>米';
			}else{
				show_word = '未标定位';
			}
			
			$(this).find('.item-text').html(show_word);
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

	var getParams = bui.getPageParams();
    getParams.done(function(result){
		layer.msg('数据加载中,请稍候...',{time:5000});
		console.log(result);
		if(result.type!=undefined && result.type=='user'){
			showtype = 'user';
		}else if(result.mid!=undefined){
			showtype = 'qun';
			mid = result.mid;
		}
    })

    // 控件初始化
    pageview.init();
    
    // 输出模块
    module.exports = pageview;
})