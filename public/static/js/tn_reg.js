if(typeof(post_reg_form)=='undefined'){

	function post_reg_form(form){
		var form_data = form.serialize();
		var url = form.attr('action');
		$.post(url, form_data, function (res) {
			if(res.code==0){
				layer.msg(res.msg,{icon:6,time:1500, shift: 1});
				if(typeof(reg_fun)=='function'){
					reg_fun(res);
				}else{
					setTimeout(function(){													
						window.location.href = res.url;
					},1500);
				}				
			}else{
				layer.alert(res.msg);
			}
		});
	}

	$(function(){
		$(".tncodeUserReg").each(function(){
			var login_need_tncode = regNeedTncode;
			var form = $(this);
			form.submit(function(){
				if(login_need_tncode){
					open_tncode(function(){
						login_need_tncode = false; //避免用户重复操作滑动码
						post_reg_form(form); 
					});
				}else{
					post_reg_form(form); 
				}
				return false;
			})
		});
	});

}
