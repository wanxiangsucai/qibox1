<?php
function_exists('urls') || die('ERR');



$jscode_pc = $jscode_wap = '';
if(fun('field@load_js',$field['type'])){
	$serverurl = urls("index/attachment/upload","dir=images&from=ueditor&module=".request()->dispatch()['module'][0]);
	$jscode_pc = <<<EOT
<script type="text/javascript"> 
jQuery(document).ready(function() {
	$('.js-ueditor').each(function(){
		//$('.ueditor').width($('.ListType .Right').width());	//重新定义编辑器的宽度＝表单提交容器标签的宽度
		UE.getEditor($(this).attr('name'), {
            initialFrameHeight:350,  //初始化编辑器高度,默认320
            autoHeightEnabled:false,  //是否自动长高
            maximumWords: 50000, //允许的最大字符数
            serverUrl: '{$serverurl}',
			//toolbars: [ ['fullscreen', 'source', 'undo', 'redo', 'bold','italic','fontsize','forecolor']]
        });
	});
});
</script>
<script src="__STATIC__/libs/ueditor/ueditor.config.js"></script>
<script src="__STATIC__/libs/ueditor/ueditor.all.min.js"></script>

EOT;

$jscode_wap = <<<EOT

				<link rel="stylesheet" href="__STATIC__/libs/summernote/bootstrap.min.css" />
				<script type="text/javascript" src="__STATIC__/libs/summernote/bootstrap.min.js"></script>
				<link rel="stylesheet" href="__STATIC__/libs/summernote/summernote.css">
				<script type="text/javascript" src="__STATIC__/libs/summernote/summernote.js"></script>
				<script type="text/javascript">
					$(document).ready(function(){
					  $('.summernote').each(function(){							
						$(this).summernote({
							height: 200,
							toolbar: false
						  });
					  });
					});
				</script>
EOT;

}

$field['input_width'] && $field['input_width']="width:{$field['input_width']};";
$field['input_width'] || $field['input_width']='max-width:80%;';
$field['input_height'] && $field['input_height']="width:{$field['input_height']};";

if(IN_WAP===true){

	return <<<EOT

<textarea id="{$name}" name="{$name}" class="summernote" placeholder="请输入内容">{$info[$name]}</textarea>
$jscode_wap

EOT;
;

}else{

	return <<<EOT

<div style="{$field['input_width']}{$field['input_height']}" class="layui-textarea c_{$name}  {$field['css']}">
<script id="{$name}" class="js-ueditor" name="{$name}" type="text/plain">{$info[$name]}</script>
$jscode_pc
</div>

EOT;
;

}

