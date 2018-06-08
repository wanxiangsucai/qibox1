<?php
function_exists('urls') || die('ERR');


$jscode = '';
if(fun('field@load_js',$field['type'])){
	$jscode = <<<EOT
<script type="text/javascript">
$('.list_array').each(function () {
		var base = $(this);
		var basehtml = base.find('div.input-group:first').prop("outerHTML");
		//base.append(basehtml);
		
		//统计数据
		var count_value = function(){
			var vals = [];
			base.find('input.wri').each(function(){
				if($(this).val()!='')vals.push($(this).val());
			});
			//vals.join(',')
			base.find('textarea').val( JSON.stringify(vals)  );
		}
		
		//输入框鼠标离开事件
		var get_act = function(){
			base.find('input.wri').on('blur',
				function(){
					count_value();
				}
			);
		}
		get_act();
		
		//添加按钮事件
		var add_act = function(){
			base.find('span.add').on('click',
				function(){
					$(this).parent().after(basehtml);
					$(this).parent().next().find("input").val('');
					base.find('span.add').off('click');
					base.find('span.del').off('click');
					base.find('input').off('blur');
					add_act();
					del_act();
					get_act();
				}
			);
		}
		add_act();

		//移除按钮事件
		var del_act = function(){
			base.find('span.del').on('click',
				function(){
					$(this).parent().remove();
					count_value();
				}
			);
		}
		del_act();
});
</script>

EOT;

}

$groups = '';
$array = json_decode($info[$name],true);
if($array){
	foreach($array AS $key=>$vo){
		$groups .= "<div class='input-group'>
			<span class='input-group-addon add'><i class='fa fa-plus-square'></i></span>
			<input class='wri' type='text' value='{$vo}' placeholder='请输入' >			
			<span class='input-group-addon del'><i class='fa fa-fw fa-close'></i></span>
        </div>";
	}
}else{
	$groups="<div class='input-group'>
			<span class='input-group-addon add'><i class='fa fa-plus-square'></i></span>
			<input class='wri' type='text' value='' placeholder='请输入' >			
			<span class='input-group-addon del'><i class='fa fa-fw fa-close'></i></span>
        </div>";
}


return <<<EOT


<div class="list_array">
$groups
<textarea style="display:none;" id="{$name}" name="{$name}" >{$info[$name]}</textarea>
</div>
$jscode

EOT;
;