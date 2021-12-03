

if(typeof(forbidLoginJump)=='undefined'){

	var forbidLoginJump = false;

	function post_login_form(form){
		var form_data = form.serialize();
		var url = form.attr('action');
		$.post(url, form_data, function (res) {
			if(res.code==0){
				forbidLoginJump = true;
				layer.msg(res.msg,{icon:6,time:1500, shift: 1});
				if(typeof(login_fun)=='function'){
					login_fun(res);
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
		$(".tncodeUserLogin").each(function(){
			var login_need_tncode = loginNeedTncode;
			var form = $(this);
			form.submit(function(){
				if(login_need_tncode){
					open_tncode(function(){
						post_login_form(form); 
					});
				}else{
					post_login_form(form); 
				}
				return false;
			})
		});
	});

}
