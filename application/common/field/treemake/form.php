<?php
function_exists('urls') || die('ERR');


$get_time_url = iurl('wxapp.api/get_time_byday');
$jscode = fun('field@load_js','layui')?"<script type='text/javascript'>if(typeof(layui)=='undefined'){document.write(\"<script LANGUAGE='JavaScript' src='__STATIC__/layui/layui.js'><\\/script>\");}</script><link rel='stylesheet' href='__STATIC__/layui/css/layui.css' media='all'>":'';
if(fun('field@load_js',$field['type'])){
	$jscode .= <<<EOT
<script type="text/javascript">
jQuery(document).ready(function() {
	var Tree;
	var tree_o = {};
	layui.use('tree', function(){
		Tree = layui.tree;
	});
	function get_tree_value(obj){
		var ar = [];
		obj.children(".layui-tree-set").each(function(){
			var id = $(this).attr("data-id");
			if(id=='undefined' || id<10000){
				id = (new Date()).getTime().toString().substring(8,13);
				if(id<10000){
					id = parseInt(id) + 10000;
				}
				$(this).attr("data-id",id);
			}
			var o ={id:id,title:$(this).find(".layui-tree-entry .layui-tree-main .layui-tree-txt").html()};
			if($(this).children(".layui-tree-pack").length>0){
				o.children = get_tree_value( $(this).children(".layui-tree-pack").eq(0) );
			}
			ar.push(o);	
		});
		return ar;
	}

	function create_tree(tag,tree_data){
		Tree.render({
			elem: '#elem_'+tag  //绑定元素 '#test1'
			,id: 'Id_'+tag //定义索引 'demoId'
			,showCheckbox:false
			,edit:['add','update', 'del' ]
			,accordion:false  //手风琴效果
			,data: tree_data  //数据
			,operate: function(obj){
				setTimeout(function(){
					tree_o[tag] = get_tree_value($('#elem_'+tag+" .layui-tree"));
					$("#atc_"+tag).val( JSON.stringify(tree_o[tag]) );  //监听更新数据
					//console.log( JSON.stringify( get_tree_value($('#elem_'+tag+" .layui-tree")) , null, 2) );
				},200);
				var type = obj.type; //得到操作类型：add、edit、del
				var data = obj.data; //得到当前节点的数据
				var elem = obj.elem; //得到当前节点元素			
				var id = data.id; //得到节点索引			
				if(type === 'add'){ //增加节点
					elem.children(".layui-tree-pack").show(200);
				} else if(type === 'update'){ //修改节点
					//if(elem.attr('data-id')=='undefined' || elem.attr('data-id')<10000){elem.attr('data-id',create_num());}
				} else if(type === 'del'){ //删除节点
				}
			}
		});
		//var checkData = tree.getChecked('demoId');
	}
	$('.make_tree_list').each(function(){
		var tag = $(this).data('tag');
		var value = $(this).find("textarea").val();		
		tree_o[tag] = value!='' ? JSON.parse( value ) : [];

		setTimeout(function(){
			create_tree(tag,tree_o[tag]);
		},typeof(Tree)=='undefined'?500:0);

		$(this).find(".addsort").click(function () {
			layer.prompt({
				title: '请输入分类名称',
				formType: 0
			}, function(value,i){
				if(value==''){
					return ;
				}
				layer.close(i);
				tree_o[tag].push({title:value});
				Tree.reload('Id_'+tag, {data: tree_o[tag]});
				tree_o[tag] = get_tree_value($('#elem_'+tag+" .layui-tree"));
				$("#atc_"+tag).val( JSON.stringify(tree_o[tag]) );
			});
		});
	});
});

</script>

EOT;

}


return <<<EOT

<div class="make_tree_list" data-tag="{$name}">
<textarea style="display:none;" id="atc_{$name}" name="{$name}">{$info[$name]}</textarea>
<span class="layui-btn layui-btn-normal layui-btn-sm addsort" data-tag="{$name}">添加一级分类</span>
</div>
<div id="elem_{$name}"></div>
$jscode


EOT;
