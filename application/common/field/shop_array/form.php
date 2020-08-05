<?php
function_exists('urls') || die('ERR');


$jscode = '';
if(fun('field@load_js',$field['type'])){
	$jscode = <<<EOT
<script type="text/javascript">
jQuery(document).ready(function() {
	$('.list_shoparray').each(function () {
		var base = $(this);
		var basehtml = base.find('div.input-group:first').prop("outerHTML");
		//base.append(basehtml);
		
		//统计数据
		var count_value = function(){
			var vals = [];
			base.find('input.shop_title').each(function(){
				if($(this).val()!='')vals.push($(this).val()+'|'+$(this).next().val()+'|'+$(this).next().next().val());
			});
			//vals.join(',')
			base.find('textarea').val( JSON.stringify(vals)  );
		}
		
		//输入框鼠标离开事件
		var get_act = function(){
			base.find('input.wri').on('blur',function(){
					count_value();
				});

			base.find('input.wri').bind('keyup',function(e){
				if (event.keyCode == "13") {
					layer.alert('请点击底部的提交按钮来提交表单!');
				}
			});
		}
		get_act();


		$(document).on("keypress", "input", function(event) { 
			return event.keyCode != 13;	//回车不能提交表单,请点击提交按钮!
		});
		
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
			base.find('span.del').on('click',function(){
				$(this).parent().remove();
				count_value();
			});
		}
		del_act();
	});
});
</script>

EOT;

}

$groups = '<style type="text/css">
.input-group .shop_title{width:200px;}
.input-group .shop_price{width:80px;}
.input-group .shop_num{width:60px;}
@media (max-width:600px) {
	.input-group .shop_title{width:140px;}
	.input-group .shop_price{width:50px;}
	.input-group .shop_num{width:40px;}
}
</style>';
$array = json_decode($info[$name],true);
if($array){
	foreach($array AS $key=>$vo){
		list($title,$price,$num) = explode('|',$vo);
		$groups .= "<div class='input-group'>
			<span class='input-group-addon add'><i class='fa fa-plus-square'></i></span>
			<input class='wri shop_title' type='text' value='{$title}' placeholder='种类或日期'>
			<input class='wri shop_price' type='number' step='0.01' value='{$price}' placeholder='价格'>
			<input class='wri shop_num' type='number' step='1' min='0' value='{$num}' placeholder='库存'>
			<span class='input-group-addon del'><i class='fa fa-fw fa-close'></i></span>
        </div>";
	}
}else{
	$groups .= "<div class='input-group'>
			<span class='input-group-addon add'><i class='fa fa-plus-square'></i></span>
			<input class='wri shop_title' type='text' value='' placeholder='种类或日期'>
			<input class='wri shop_price' type='number' step='0.01' min='0' value='' placeholder='价格'>
			<input class='wri shop_num' type='number' step='1' min='0' value='' placeholder='库存'>
			<span class='input-group-addon del'><i class='fa fa-fw fa-close'></i></span>
        </div>";
}


return <<<EOT


<div class="list_shoparray">
$groups
<textarea style="display:none;" id="{$name}" name="{$name}" >{$info[$name]}</textarea>
</div>
$jscode

EOT;
;