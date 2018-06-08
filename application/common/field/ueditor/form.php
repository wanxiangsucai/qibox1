<?php
function_exists('urls') || die('ERR');



$jscode = '';
if(fun('field@load_js',$field['type'])){
	$serverurl = urls("index/attachment/upload","dir=images&from=ueditor&module=".request()->dispatch()['module'][0]);
	$jscode = <<<EOT
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

}

return <<<EOT

<div style="max-width:80%;">
<script id="{$name}" class="js-ueditor" name="{$name}" type="text/plain">{$info[$name]}</script>
$jscode
</div>

EOT;
;