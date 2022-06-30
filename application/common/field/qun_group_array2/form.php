<?php
function_exists('urls') || die('ERR');

$ext_id = intval(input('ext_id'));
$jscode = '';
if(fun('field@load_js',$field['type'])){
	$group_url = iurl('qun/wxapp.group/index');
	$jscode = <<<EOT
<script type="text/javascript">
jQuery(document).ready(function() {
	$('.list_shoparray').each(function () {
		var base = $(this);
		var basehtml = base.find('div.input-group:first').prop("outerHTML");
		//base.append(basehtml);
		
		$(document).on("keypress", "input", function(event) { 
			return event.keyCode != 13;	//回车不能提交表单,请点击提交按钮!
		});

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
		var blur_act = function(){
			base.find('input.wri').on('blur',function(){
					count_value();
				});

			base.find('input.wri').bind('keyup',function(e){
				if (event.keyCode == "13") {
					layer.alert('请点击底部的提交按钮来提交表单!');
				}
			});
		}

		//下移
		function down_act(){
			base.find("span.down").click(function(){
				var that = $(this).parent();
				if(that.next().hasClass('input-group')){
					that.next().after(that.clone());
					that.remove();
					init_act();
				}else{
					layer.alert('到尽头了');
				}								
			});
		}		

		//上移
		function up_act(){
			base.find("span.up").click(function(){
				var that = $(this).parent();
				if(that.prev().hasClass('input-group')){
					that.prev().before(that.clone());
					that.remove();
					init_act();
				}else{
					layer.alert('到尽头了');
				}								
			});
		}
		
		//添加按钮事件
		var add_act = function(){
			base.find('span.add').on('click',
				function(){
					$(this).parent().after(basehtml);
					$(this).parent().next().find("input").val('');
					init_act();
				}
			);
		}

		//移除按钮事件
		var del_act = function(){
			base.find('span.del').on('click',function(){
				$(this).parent().remove();
				count_value();
			});
		}


		//选择用户组事件
		var choosegroup_act = function(){
			base.find('.choose_group').on('click',function(){
				var that = $(this);
				$.get("{$group_url}?id="+($("#atc_ext_id").length>0?$("#atc_ext_id").val():{$ext_id}),function(res){
					if(res.code==0){
						var str = '';
						res.data.forEach((rs)=>{
							var ck = that.val().indexOf(rs.gid)>-1?' checked ':' ';
							str+='<input type="checkbox" '+ck+' value="'+rs.gid+'">'+rs.name+'<br>';
						});
						layer.alert('<div class="list_qungroup">'+str+'</div>',{title:'请选择用户组'},function(i){
							var garray = [];
							$(".list_qungroup input").each(function(){
								if($(this).is(':checked')){
									garray.push($(this).val());
								}
							});
							that.val(garray.join(','));
							count_value();
							layer.close(i);
						});
					}else{
						layer.alert(res.msg);
					}
				});
			});
		}

		var init_act = function(){
			base.find('span').off('click');
			base.find('input').off('blur');
			add_act();
			del_act();
			blur_act();
			down_act();
			up_act();
			count_value();
			choosegroup_act();
		}
		init_act();

	});
});
</script>

EOT;

}

$groups = '<style type="text/css">
.input-group .shop_title{width:200px;}
.input-group .shop_groups{width:150px;}
.input-group .shop_noticegroups{width:150px;}
@media (max-width:600px) {
	.input-group .shop_title{width:140px;}
	.input-group .shop_groups{width:80px;}
	.input-group .shop_noticegroups{width:80px;}
}
</style>';
$array = json_decode($info[$name],true);
if($array){
	foreach($array AS $key=>$vo){
		list($title,$gids,$noticegids) = explode('|',$vo);
		$groups .= "<div class='input-group'>
			<span class='input-group-addon add'><i class='fa fa-plus-square'></i></span>
			<input class='wri shop_title' type='text' value='{$title}' placeholder='状态名称、分类'>
			<input class='wri shop_groups choose_group' type='text' readOnly value='{$gids}' placeholder='有权限的用户组'>
			<input class='wri shop_noticegroups choose_group' type='text' readOnly value='{$noticegids}' placeholder='通知用户组'>
			<span class='input-group-addon del'><i class='fa fa-fw fa-close'></i></span>
        </div>";
	}
}else{
	$groups .= "<div class='input-group'>
			<span class='input-group-addon add'><i class='fa fa-plus-square'></i></span>
			<input class='wri shop_title' type='text' value='' placeholder='状态名称、分类'>
			<input class='wri shop_groups choose_group' type='text' readOnly value='' placeholder='有权限的用户组'>
			<input class='wri shop_noticegroups choose_group' type='text' readOnly value='' placeholder='通知用户组'>
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