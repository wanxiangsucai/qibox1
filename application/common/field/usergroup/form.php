<?php
function_exists('urls') || die('ERR');


$jscode = '';
if(fun('field@load_js',$field['type'])){
	$jscode = <<<EOT
<script type="text/javascript">
$('.list_usergroup').each(function () {
		var base = $(this);

		//统计数据
		var count_value = function(){
			var vals = {};
			base.find('input.wri').each(function(){
				var gid = parseInt($(this).data("id"));
				vals[gid] = $(this).val();
				//if($(this).val()!='')vals.push({"gid":$(this).val()});
			});
			//vals.join(',')
			base.find('textarea').val( JSON.stringify(vals)  );
		}
		
		//输入框鼠标离开事件
		base.find('input.wri').on('blur',
				function(){
					count_value();
				}
		);
		
		//初始化
		obj = JSON.parse( base.find('textarea').val() );
		for(var item in obj){
			var jValue=obj[item];//key所对应的value
			//alert(item+jValue);
			base.find('[data-id="'+item+'"]').val(jValue);
        }
		
	});
</script>

EOT;

}

$groups = '';
foreach(getGroupByid(0) AS $key=>$vo){
	$groups .= "<div class='input-group'><span class='gtitle'>{$vo}</span><span style='padding-left:15px;' class='_input'><input type='text'class='wri' data-id='{$key}' value='' placeholder='请输入数值'></span></div>";
}

return <<<EOT


<div class="list_usergroup">
$groups
<textarea style="display:none;" id="atc_{$name}" name="{$name}" >{$info[$name]}</textarea>
</div>
$jscode

EOT;
;