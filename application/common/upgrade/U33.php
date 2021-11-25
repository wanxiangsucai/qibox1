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
	function more_link_fun(res){
		if(res.code==0){
			$("#status-"+res.data.id).html(res.data.status_name);
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