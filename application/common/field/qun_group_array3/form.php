<?php
function_exists('urls') || die('ERR');

$showicon = $info[$name]?:'fa fa-fw fa-file-text';
$ext_id = intval(input('ext_id'));
$jscode = '';
if(fun('field@load_js',$field['type'])){
	$group_url = iurl('qun/wxapp.group/index');
	$jscode .= <<<EOT

<style type="text/css">
.input-group-addon {
    cursor: pointer;
}

</style>
<script type="text/javascript">
    // 打开图标选择器
$(function(){
	$(".choose_qun-{$name}").each(function(){
		var base = $(this);
		base.find("input").click(function(){
			var that = $(this);
			$.get("{$group_url}?id="+($("#atc_ext_id").length>0?$("#atc_ext_id").val():{$ext_id}),function(res){
					if(res.code==0){
						var str = '';
						res.data.forEach((rs)=>{
							var ck = that.val().indexOf(rs.gid)>-1?' checked ':' ';
							str+='<input type="radio" '+ck+' name="choose_qun" value="'+rs.gid+'">'+rs.name+'<br>';
						});
						layer.alert('<div class="list_qungroup">'+str+'</div>',{title:'请选择用户组'},function(i){
							var garray = [];
							$(".list_qungroup input").each(function(){
								if($(this).is(':checked')){
									garray.push($(this).val());
								}
							});
							that.val(garray.join(','));
							layer.close(i);
						});
					}else{
						layer.alert(res.msg);
					}
				});
		});

		base.find('.delete-icon').click(function(event){
			event.stopPropagation();
			if ($(this).prev().is(':disabled')) {
				return;
			}
			$(this).prev().val('');
			$(this).prev().prev().html('<i class="fa fa-fw fa-plus-circle"></i>');
		});

	});
});    
</script>

EOT;

}

return <<<EOT
$jscode
	<div class="input-group choose_qun-{$name}">
            <input style="width:300px;" readOnly type="text" id="atc_{$name}" name="{$name}" value="{$info[$name]}" placeholder="点击选择用户组" >
            <span class="input-group-addon delete-icon"><i class="fa fa-times"></i></span>
    </div>
EOT;
