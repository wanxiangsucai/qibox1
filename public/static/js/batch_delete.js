$(function(){
	$("a").each(function(){
		if($(this).attr("href").indexOf('/delete/')>-1&&$(this).html().indexOf('批量删除')>-1){
			$(this).click(()=>{
				layer.confirm("你确认要批量删除以下所有数据吗？",{title:'提示'},()=>{
					var ids = [];
					$("input[name='ids[]']").each(function(){
						if($("input[name='ids[]']:checked").length==0 || $(this).is(':checked')==true){
							ids.push('ids[]='+$(this).val());
						}
					});
					window.location.href = $(this).attr("href")+'?'+ids.join('&');
				});
				return false;
			})
		}
	});
});