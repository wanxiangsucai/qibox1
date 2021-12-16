<?php
namespace app\common\upgrade;


class U33{
	public static function up(){
	   $m = modules_config();
	   $string = '{extend name="$member_style_layout" /}

{block name="title"} {$tab_ext.page_title} {/block}

{block name="content"}
	{include file="member@common/menu" /}
	{include file="member@common/table01" /}
	{$pages}
<script type="text/javascript">
function pre_link_fun(url){
	if(url.indexOf(\'/status/-9\')>0 || url.indexOf(\'/status/-1\')>0){
		layer.confirm(\'<textarea class="doReason" style="width:100%;height:100px;"></textarea>\',{title:\'操作理由（可不填）\'},function(){
			post(url,{reason:$(".doReason").last().val()});
		});
	}else{
		post(url,{});
	}
	function post(url,data){
		$.post(url,data,function(res){            
			if(res.code==0){
                layer.msg(res.msg?res.msg:\'操作成功\');
				$("#status-"+res.data.id).html(res.data.status_name);
			}else{
				layer.alert(res.msg?res.msg:\'操作失败\');
			}
		});
	}
}
</script>
{/block}';
	   foreach ($m AS $rs){
	       if($rs['keywords']=='cms'||$rs['keywords']=='qun'){
	           continue;
	       }
	       if(!is_file(TEMPLATE_PATH.'member_style/default/'.$rs['keywords'].'/content/index.htm')){
	           continue;
	       }
	       $path = TEMPLATE_PATH.'member_style/default/'.$rs['keywords'].'/content/manage.htm';
	       file_put_contents($path, $string);
	   }
	}
}