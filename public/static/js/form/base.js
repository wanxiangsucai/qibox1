

jQuery(document).ready(function() {
	
	if(typeof(nopower)=='string' && nopower!=''){
		layer.alert(nopower,{title:'提示'},function(index){
			layer.close(index);
			if(nopower.indexOf('手机')>-1){
				layer.open({
						type: 2,
						title:'绑定手机',
						area: in_wap ? ['95%', '80%'] : ['800px', '600px'],
						shade: 0.4,
						content:bind_phone_url,
				});
			}else if( nopower.indexOf('公众号')>-1 ){
				layer.alert("<img width='300' src='"+mp_img_url+"'><br>请用微信扫码关注上面的公众号",{title:'请扫码关注下面的公众号',});
			}else if(nopower.indexOf('用户组')>-1){
				layer.open({
						type: 2,
						title:'升级用户组',
						area: ['95%', '80%'],
						shade: 0.4,
						content:upgroup_url,
				});
			}
		});
	}

	//ajax无刷新提交表单
	$("form.ajax_post").each(function(){
		var form = $(this);
		form.submit(function(){
			if(typeof(needTncode)!='undefined' && needTncode==true){
				open_tncode(function(){
					needTncode = false;
					post_form(form);
				});
			}else{
				post_form(form);
			}
			return false;
		});
	});

	function post_form(form){
			var havepost = true;
			var form_data = form.serialize();
			var url = form.attr('action');

			if(havepost==false){
				layer.alert('请不要重复提交');
				return false;
			}
			var index = layer.load(1);
			havepost = false;
			$.post(url, form_data).success(function (res) {
				layer.close(index);
				if(res.code==0){
					layer.msg(res.msg);
					if( typeof(post_ok)=='function' ){	//接口回调
						post_ok(res);
					}else{
						setTimeout(function(){
							try{
								parent.layer.close(parent.layer.getFrameIndex(window.name));
							}catch(err){
								console.log(err);
							}														
							window.location.href = res.url;
						},500);
					}				
				}else{
					havepost = true;
					if( typeof(post_err)=='function' ){	//接口回调
						post_err(res);
					}else{
						//layer.open({title: '提交失败',content:res.msg});
						layer.alert(res.msg,{title:'提交失败'},function(index){
							layer.close(index);
							if(res.msg.indexOf('手机')>-1){
								layer.open({
									type: 2,
									title:'绑定手机',
									area: in_wap ? ['95%', '80%'] : ['800px', '600px'],
									shade: 0.4,
									content:bind_phone_url,
								});
							}else if( res.msg.indexOf('公众号')>-1 ){
								layer.alert("<img width='300' src='"+mp_img_url+"'><br>请用微信扫码关注上面的公众号",{title:'请扫码关注下面的公众号',});
							}else if(res.data.paymoney){
								pay_money(res.data.paymoney)
							}
						});
					}					
				}
			}).fail(function (res) {
				havepost = true;
				layer.close(index);
				layer.open({title: '服务器发生错误',area:['90%','90%'],content: res.responseText});
			});
	}


	function pay_money(money){
		jQuery.getScript("/public/static/js/pay.js").done(function() {			
		}).fail(function() {
			layer.msg('public/static/js/pay.js加载失败',{time:800});
		});
		layer.confirm("是否立即充值 "+money+" 元",{btn:['充值','取消']},function(i){
			layer.close(i);
			if( navigator.userAgent.match(/(iPhone|iPod|Android|ios|iPad)/i) ){
				Pay.mobpay( money,'充值',function(type,index){
					layer.close(index);
					if(type=='ok'){
						layer.msg('充值成功');
						if( typeof(endpay_ok)=='function' ){	//接口回调
							endpay_ok();
						}
					}else{
						layer.alert('充值失败');
					}
				});
			}else{
				Pay.pcpay( money,'充值',function(type,index){
					layer.close(index);
					if(type=='ok'){
						layer.msg('充值成功');
						if( typeof(endpay_ok)=='function' ){	//接口回调
							endpay_ok();
						}
					}else{
						layer.alert('充值失败');
					}
				});
			}
		});
	}

	//联动触显 不能跟layer表单事件一起用
    if (typeof(trigger_config)=='object' && typeof(trigger_config.triggers) != 'undefined') {
        // 先隐藏依赖项
        var field_hide_array   = trigger_config.field_hide.split(',') || [];
        for (var index in field_hide_array){
            $('#form_group_'+field_hide_array[index]).hide();
        }
        var trigger_form = $('.form-trigger');
        $.each(trigger_config.triggers, function (trigger, content) {
            trigger_form.delegate('[name='+ trigger +']', 'change', function (event, init) {
                var trigger_item = $(this);
                var trigger_value   = trigger_item.val();	//当前选中的值
				
				var cks = '';	//获取当前项的所有表单
				$(content).each(function () {
					var that = $(this);
					cks += that[1]+',';
				});				

				for (var index in field_hide_array) {
					if(field_hide_array[index]!='' && $.inArray( field_hide_array[index], cks.split(',') )>-1 ){	//只隐藏当前项,非当前项就不能隐藏
						$('#form_group_'+field_hide_array[index]).hide();
					}					
				}

                $(content).each(function () {
                    var that = $(this);
                    var sel_values  = that[0];	//供可选的值 可能是一个 xx 也可能是多个xx,ss,cc
                    var targets_array = that[1].split(',');
                    if ( (sel_values.indexOf(',')==-1&&trigger_value==sel_values)  || (sel_values.indexOf(',')>-1&&$.inArray( trigger_value, sel_values.split(',') )>-1) ) { //选中其中的值
                        for (var index in targets_array) {
							$('#form_group_'+targets_array[index]).show();
                        }
                    }
                });
            });
            // 有默认值时
            var trigger_value = '';
			if (trigger_form.find('[name='+ trigger +']').attr('type') == 'radio') {
                trigger_value = trigger_form.find('[name='+ trigger +']:checked').val() || '';
                if (trigger_value != '') {
                    var $radio_id = $('.form-trigger [name='+ trigger +']:checked').attr('id');
                    $('.form-trigger #'+$radio_id).trigger("change");
                }
            } else {
                trigger_value = trigger_form.find('[name='+ trigger +']').val() || '';
                if (trigger_value != '') {
                    $('.form-trigger [name='+ trigger +']').trigger("change");
                }
            }
        });
    }

});


//地区选择
$(document).ready(function (){
	if($(".ListArea select").length>0){
		choose_where(0,0,default_ckid[0],true);
	}	
	$(".ListArea select").each(function(i){
		$(this).hide();	//把所有都隐藏起来.有数据加载成功才显示
		$(this).change(function () {
		   var val = $(this).val();	//$(this).children('option:selected').val();
		   choose_where(i,val);  
	   });
	});
});
//下拉框选择事件
function choose_where(num,pid,ckid,ifload){	//第几个选项,父ID,默认初始化选中ID,是否页面默认初始化
	if(ifload!==true && num==0 && pid==0){	//省份即第一项,不是默认加载的时候,选择0时,就不要给下级加数据,而是把下级全部清空
		delete_sons(num+1);	//所有子级的数据全清空
		return ;
	}
	var iftop = num===0 ? 1 : 0;
	$.get(get_area_url+"?iftop="+iftop+"&pid="+pid+"&ckid="+ckid,function(res){
		if(res.code==0){	//有数据
			if(ifload===true){	//页面初始化时加载的默认数据
				set_area_value(num,res.data,ckid);		//当前选项赋值
				choose_where(num+1,ckid,default_ckid[num+1],ifload);	//加载数据成功,才返回当前选中的ID给下级当作父ID继续取值
			}else{	//用户重新自由选择
				set_area_value(num+1,res.data);	//下级数据填充				
				delete_sons(num+2);	//下下级以后的数据全清空
			}
		}else{	//无数据
			delete_sons(num+1);	//下级数据不存在的话,就把他们全清空
		}
	});
}

//清空下级后面所有的数据
function delete_sons(min){
	leng = $(".ListArea select").length;
	for(i=min;i<leng;i++){
		set_area_value(i,[]);
	}
}

//下级数据赋值
function set_area_value(num,data,ckid){
	that = $(".ListArea select").eq(num);
	if(data.length==0){
		that.hide();
	}else{
		that.show();
	}
	that.empty();	//首先清空select现在有的内容
    that.append("<option selected='selected' value='0'>"+that.data("title")+"</option>");
	data.forEach(function(item){
		var ck = ckid==item.id ? ' selected ' : '';
		that.append("<option  value='" + item.id + "' " + ck + ">" + item.name + "</option>");
	})
}