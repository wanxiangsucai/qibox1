<?php
function_exists('urls') || die('ERR');

$showicon = $info[$name]?:'fa fa-fw fa-file-text';
$ext_id = intval(input('ext_id'));
$jscode = '';
if(fun('field@load_js',$field['type'])){
	$group_url = iurl('qun/wxapp.group/index');
	$jscode .= <<<EOT

<style type="text/css">
.choose_qun_group .input-group-addon {
    cursor: pointer;
}
.list_qungroup ul{
	display:flex;
}
.list_qungroup ul li{
	width:50%;
}
.list_qungroup ul li input{
	width:80px;
}
</style>
<script type="text/javascript">
$(function(){
	$(".choose_qun_group").each(function(){
		var base = $(this);
		base.find("input").click(function(){
			var that = $(this);
			$.get("{$group_url}?id="+($("#atc_ext_id").length>0?$("#atc_ext_id").val():{$ext_id}),function(res){
					if(res.code==0){
						var vobj = {};
						if(that.val()!=''){
							vobj = JSON.parse( that.val() );
						}
						var str = '';
						res.data.forEach((rs)=>{
							str+="<ul><li>"+rs.name+'</li><li><input type="text" data-gid="'+rs.gid+'" value="'+(vobj[rs.gid]||'')+'"></li></ul>';
						});
						layer.alert('<div class="list_qungroup">'+str+'</div>',{title:'请设置用户组'},function(i){
							var garray = {};
							$(".list_qungroup input").each(function(){
								garray[$(this).data('gid')] = $(this).val();
							});
							that.val( JSON.stringify(garray) );
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
	<div class="input-group choose_qun_group">
            <input style="width:300px;" readOnly type="text" id="atc_{$name}" name="{$name}" value='{$info[$name]}' placeholder="点击选择用户组" >
            <span class="input-group-addon delete-icon"><i class="fa fa-times"></i></span>
    </div>
EOT;
