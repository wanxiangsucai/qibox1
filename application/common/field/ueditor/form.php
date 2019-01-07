<?php
function_exists('urls') || die('ERR');



$jscode_pc = $jscode_wap = '';
if(fun('field@load_js',$field['type'])){
	$serverurl = urls("index/attachment/upload","dir=images&from=ueditor&module=".request()->dispatch()['module'][0]);
	$editor_tpl = iurl('index/editor/index');
	$jscode_pc = <<<EOT
<script type="text/javascript">
var editor_a = [];
var editor_i = 0;
jQuery(document).ready(function() {
	$('.js-ueditor').each(function(){
		//$('.ueditor').width($('.ListType .Right').width());	//重新定义编辑器的宽度＝表单提交容器标签的宽度
		var edit = UE.getEditor($(this).attr('name'), {
            initialFrameHeight:350,  //初始化编辑器高度,默认320
            autoHeightEnabled:false,  //是否自动长高
            maximumWords: 50000, //允许的最大字符数
            serverUrl: '{$serverurl}',
			//toolbars: [ ['fullscreen', 'source', 'undo', 'redo', 'bold','italic','fontsize','forecolor']]
        });
		editor_a.push(edit);
	});
});
</script>
<script src="__STATIC__/libs/ueditor/ueditor.config.js"></script>
<script src="__STATIC__/libs/ueditor/ueditor.all.min.js"></script>

<!--布局模板开始-->
<script type="text/javascript"> 
jQuery(document).ready(function() {
	$('.slectEditMode').each(function(i){
		$(this).click(function(){
			editor_i = i;
			showEditMode();
		});		
	});
})
function insertHtml(nums) {
	var strs=$('.stylemode'+nums).html();
	editor_a[editor_i].execCommand('insertHtml',strs);
	hide_nav($('#editmodes'),$('#fullbg1'));
}
function showEditMode(){
	$.get("{$editor_tpl}",function(d){
		$('#editmodes').html(d);
	});
	show_nav($('#editmodes'),$('#fullbg1'));
	$('#fullbg1').height($(window).height());
	$('#editmodes').height($(window).height());
}
function show_nav(node,fullbg){
	fullbg.css({'display':'block'}).stop().animate({'opacity':.6},500,function(){
		node.stop().animate({'width':'300px','padding':'0px 10px 0 10px'},300);
	});
}
function hide_nav(node,fullbg){
	fullbg.animate({'opacity':0},300,function(){
		$(this).css({'display':'none'});
	});
	setTimeout(function(){
		node.html('');
		node.stop().animate({'width':'0px','padding':'0px 0px 0 0px'},300);
	}, 500)
}
</script>

<style type="text/css">
.slectEditMode{
	padding:5px 0 5px 0;
}
.slectEditMode a{
	display:inline-blodk;
	padding:5px 10px;
	background:green;
	border-radius:5px;
	color:#FFF;
}
.fullbg { 
	background-color:#000; 
	opacity:0; 
	top:0; 
	left:0; 
	width:100%; 
	height:100%; 
	z-index:1001; 
	position:fixed;
	display:none;
}
#editmodes{ 
	position:fixed; 
	top:0;  
	right:0; 
	z-index:1002; 
	height:100%;
	width:0px;
	overflow:auto;
	overflow-x:hidden;
	scrollbar-face-color: #FFFFFF;
	scrollbar-shadow-color: #eee;
	scrollbar-highlight-color: #eee;
	scrollbar-3dlight-color: #FFFFFF;
	scrollbar-darkshadow-color: #FFFFFF;
	scrollbar-track-color: #FFFFFF;
	scrollbar-arrow-color: #D2E5F4; 
	background:#FFF;
}
</style>
<div id="editmodes"></div>
<div class="fullbg" id="fullbg1" onclick="hide_nav($('#editmodes'),$('#fullbg1'))"></div>
<!--布局模板结束-->

EOT;

$jscode_wap = <<<EOT

				<link rel="stylesheet" href="__STATIC__/libs/summernote/bootstrap.min.css" />
				<script type="text/javascript" src="__STATIC__/libs/summernote/bootstrap.min.js"></script>
				<link rel="stylesheet" href="__STATIC__/libs/summernote/summernote.css">
				<script type="text/javascript" src="__STATIC__/libs/summernote/summernote.js"></script>
				<script type="text/javascript">
					$(document).ready(function(){
					  $('.summernote').each(function(){							
						var v_summernote = $(this).summernote({
							height: 200,
							callbacks: {
								onImageUpload: function (files) {
									sendFile(v_summernote, files[0]);
								}
							},
							toolbar: [
										['codeview',['fullscreen','undo','redo', 'clear','codeview']], //查看html代码
										//['fontname', []], //字体系列                                 
										['style', ['bold', 'italic', 'underline','strikethrough','hr','link','picture']], // 字体粗体、字体斜体、字体下划线、字体格式清除       
										//['font', ['strikethrough', 'superscript', 'subscript']], //字体划线、字体上标、字体下标   
										//['fontsize', ['fontsize','color']], //字体大小                                
									   // ['color', []], //字体颜色                             
										//['style', ['style']],//样式
										//['para', [ 'paragraph']], //无序列表、有序列表、段落对齐方式'ul', 'ol',
										//['height', ['height']], //行高                  
										//['table',['table']], //插入表格    
										//['hr',[]],//插入水平线                
										//['link',['hr','link','picture']], //插入链接                
										//['picture',[]], //插入图片                
										//['video',['video']], //插入视频
										 
										//['fullscreen',[]], //全屏
										
										//['undo',[]], //撤销
										//['redo',[]], //取消撤销
									   // ['help',['help']], //帮助
									 ],

						  });
						  var sendFile = function(o,files){
							  var formData = new FormData();
								formData.append("file", files);
								$.ajax({
								url: "{$serverurl}?action=uploadimage",
									data: formData,
									cache: false,
									contentType: false,
									processData: false,
									type: 'POST',
									success: function (data) {
										o.summernote('insertImage', data.url);
									}
								});
							  
						  }
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

<div class="slectEditMode"><a href="javascript:;">内容布局模板</a></div>

EOT;
;

}

