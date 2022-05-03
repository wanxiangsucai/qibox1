function check_post(form_obj){
	var ck = true;
	$(".must-choose").each(function(){
		let title = $(this).data('title');
		if($(this).find('select').length>0 && !$(this).find('select').val()){
			layer.alert(title+" 必选项,不能为空!");
			ck = false;
			return false;
		}else if($(this).find("input[type='radio']").length>0 && $(this).find("input[type='radio']:checked").length==0){
			layer.alert(title+" 必选项,不能为空!");
			ck = false;
			return false;
		}else{
			var obj = $(this).find('input');
			if(obj.length<0){
				//obj = $(this).find('select');
			}
			if(obj.length==1 && obj.val()==''){
				obj.focus();
				layer.alert(title+" 必填项,不能为空!");
				ck = false;
				return false;
			}
		}	
	});
	var array = [];
	$(".customize").each(function(){
		var va = '';
		var tp = $(this).data('type');
		if(tp=="select"){
			if( $(this).find('select').length>0 ){
				va = $(this).find('select').val();
			}else{
				va = $(this).find('input[name='+$(this).data('name')+']').val();
			}
			
		}else if(tp=="radio"){
			if( $(this).find('input[type="radio"]').length>0  ){
				va = $(this).find('input:checked').val();
			}else{
				va = $(this).find('input[name='+$(this).data('name')+']').val();
			}			
		}else if(tp=="checkbox"){
			if( $(this).find('input[type="checkbox"]').length>0  ){
				$(this).find('input:checked').each(function(){
					va += "、" + $(this).val();
				});
				va = va.substring(1);
			}else{
				va = $(this).find('input[name='+$(this).data('name')+']').val();
			}			
		}else if( $(this).data('name') && $(this).find('input[name='+$(this).data('name')+']').length>0 ){
			va = $(this).find('input[name='+$(this).data('name')+']').val();
		}else{
			va = $(this).find('input').length>0 ? $(this).find('input').val() : $(this).find('textarea').val();
		}
		if(va!=''){
			array.push( {
			title:$(this).data('title'),
			type:tp,
			value:va
			} );
		}		
	});
	if(array.length>0){
		$("#order_field").val( JSON.stringify(array)  );
	}

	if( ck && form_obj && form_obj.hasClass('ajax_post') ){
		var formData = form_obj.serialize();
		var url = form_obj.attr('action');
		$.post(url,formData,function(res){
			if(res.code==0){
				layer.msg("你的表单已成功提交!");
				if(typeof(post_order)=='function'){
					post_order('ok',res);
				}
			}else{
				layer.alert(res.msg);
				if(typeof(post_order)=='function'){
					post_order('err',res);
				}
			}
		});
		return false;
	}else{
		return ck;
	}
}

$(function(){
	$('form input[type="text"]').each(function(){
		if($(this).prev().hasClass("title")){
			$(this).prev().hide();	//文本框就不需要显示标题提示描述
		}
	});
})