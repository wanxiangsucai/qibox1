<?php
function_exists('urls') || die('ERR');
//list($province_id,$city_id,$zone_id,$street_id) = explode(',',$info[$name]);
//$url = purl('area/api/getlist',[],'index');
return <<<EOT
	<div class="ListArea">
						<select name='province_id' data-title="请选择省份" lay-ignore></select> 
						<select name='city_id' data-title="请选择城市" lay-ignore></select> 
						<select name='zone_id' data-title="请选择区域" lay-ignore></select> 
						<select name='street_id' data-title="请选择街道" lay-ignore></select>
	</div>
<SCRIPT LANGUAGE="JavaScript">
//地区选择事件
//var default_ckid = ["{$province_id}","{$city_id}","{$zone_id}","{$street_id}"];
//var get_area_url = "{$url}";
</SCRIPT>

EOT;
