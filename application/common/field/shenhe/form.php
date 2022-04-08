<?php
function_exists('urls') || die('ERR');


$jscode = '';
$getuser_url = iurl('index/wxapp.member/getbyids');
if(fun('field@load_js',$field['type'])){
	$jscode = <<<EOT
<script type="text/javascript">
jQuery(document).ready(function() {
	$('.list_array').each(function () {
		var base = $(this);
		var basehtml = base.find('div.input-group:first').prop("outerHTML");
		//base.append(basehtml);
		
		$(document).on("keypress", "input", function(event) { 
			return event.keyCode != 13;	//回车不能提交表单,请点击提交按钮!
		});

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
		var blur_act = function(){
			base.find('input.wri').on('blur',function(){
					count_value();
					$.get('{$getuser_url}',{uids:$(this).val()},res=>{
						let arr = [];
						if(res.code==0){
							res.data.forEach(rs=>{
								arr.push((rs.nickname||rs.username)+'('+rs.uid+')')
							})
						}
						$(this).parent().find('.listuser').html(arr.join('、'));
					})
				}
			);
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
			base.find('.add').unbind('click').on('click',
				function(){
					base.find('div.input-group:last').after(basehtml);
					base.find('div.input-group:last').find("input").val('');
					base.find('div.input-group:last').find(".listuser").html('');
					init_act();
				}
			);
		}

		//移除按钮事件
		var del_act = function(){
			base.find('span.del').on('click',
				function(){
					$(this).parent().remove();
					count_value();
					count_step();
				}
			);
		}

		var count_step = function(){
			let j = 0;
			base.find('.step').each(function(){
				j++;
				let name = j+'审';
				if(j==1){
					name = '编辑';
				}else if(j==2){
					name = '责辑';
				}else if(j==3){
					name = '编审';
				}else if(j==4){
					name = '监制';
				}
				$(this).html(name);
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
			count_step();
		}
		init_act();
	});
});
</script>

EOT;

}

$groups = '';
$array = json_decode($info[$name],true);
if($array){
	foreach($array AS $key=>$vo){
		$j = $key+1;
		$listuser=[];
		foreach(explode(',',$vo) AS $uid){
			if($uid>0){
				$user = get_user($uid);
				if($user)$listuser[] = ($user['nickname']?:$user['username']).'('.$uid.')';
			}
		}
		$listuser = implode('、',$listuser);
		$groups .= "<div class='input-group'>
			<span class='input-group-addon step'>{$j}审</span>
			<input class='wri' type='text' value='{$vo}' placeholder='请输入用户UID' >			
			<span class='input-group-addon del'><i class='fa fa-fw fa-close'></i></span>
			<span class='listuser'>{$listuser}</span>
        </div>";
	}
}else{
	$groups="<div class='input-group'>
			<span class='input-group-addon step'>1审</span>
			<input class='wri' type='text' value='' placeholder='请输入用户UID' >			
			<span class='input-group-addon del'><i class='fa fa-fw fa-close'></i></span>
        </div>";
}


return <<<EOT


<div class="list_array">
$groups
<div style="margin:8px 20px;">
<button class="layui-btn layui-btn-sm layui-btn-normal add" style="color:#fff;" type="button">增加一级</button>
</div>
<textarea style="display:none;" id="{$name}" name="{$name}" >{$info[$name]}</textarea>
</div>
$jscode

EOT;
;