<?php
namespace app\common\fun;

/**
 * 链接用到的相关函数
 */
class Link{
    
    /**
     * 带下拉菜单的链接，比如
     * $code = fun('link@more',"<i class='fa fa-gears'>相关操作</i>",[
		                    '通过审核'=>urls('pass',['id'=>$value,'status'=>1]),
		                    '拒绝通过'=>urls('pass',['id'=>$value,'status'=>-1]),
		                ]);
     * @param string $title
     * @param array $link_array
     * @return string
     */
    public static function more($title='',$link_array=[]){
        $str = '';
        foreach ($link_array AS $name=>$link){
            if (is_array($link)) {
                if($link['target']=='ajax'){
                    $str .= "<a class='more_links {$link['icon']}' href=\"javascript:more_links_ajax('{$link['url']}','{$link['alert']}')\">{$name}</a>";
                }else{
                    $str .= "<a class='more_links {$link['icon']}' target=\"{$link['target']}\" href=\"{$link['url']}\">{$name}</a>";
                }                
            }else{
                $str .= "<a class='more_links' href=\"javascript:layer.confirm('你确定要{$name}？', { btn: ['确定', '取消'] },function(){ window.location.href='{$link}' });\">{$name}</a>";
            }            
        }
        $area_width = empty(in_wap())&&count($link_array)>=6?'400px':'200px';
        $show_time = empty(in_wap())&&count($link_array)>=6?'10000':'5000';
        $code = "
<script type='text/javascript'>
function more_links_ajax(url,alert_msg){
	if(alert_msg){
		layer.confirm(alert_msg,{title:false,},function(index){
			post(url);
		});
	}else if(typeof(pre_link_fun)=='function'){
		pre_link_fun(url);
	}else{
		post(url);
	}
	function post(url){
        var Tip = layer.load(1, {
    	    shadeClose: true,
    	    icon:1,
    	    shade: [0.8,'#666']
    	});
		$.get(url,function(res){
            layer.close(Tip); 
			if(res.code==0){
                layer.msg(res.msg?res.msg:'操作成功');
			}else{
				layer.alert(res.msg?res.msg:'操作失败');
			}
            if(typeof(more_link_fun)=='function')more_link_fun(res);
		});
	}
}
$(function(){
    $('body').click(()=>{
        if(window.layer_more_links){
            layer.close(window.layer_more_links);
        }
        window.layer_more_links = null;

    });
})
function show_layer_more_links(obj){
    var index = layer.tips(obj.next().html(), obj, {tips: [3, '#0FA6D8'],tipsMore: false,time:{$show_time},area:'{$area_width}' });
    setTimeout(()=>{
        window.layer_more_links = index;
    },500);
}
</script>
<style type='text/css'>
.more_links{
	font-size:16px;
	background:#fff;
	color:rgb(15, 166, 216) !important;
	padding:3px 8px 3px 8px;
	border-radius:3px;
	margin:5px 0;
	display:block;
    text-align:center;
}
.more-links-warp{
    width: 100%;
    display:flex;
    flex-wrap: wrap;
    justify-content: space-between;
}
.more-links-warp .more_links{
    width:160px;
}
.more-links-warp .more_links:hover{
    background:#f3f3f3;
}
</style>
<a href='javascript:' title='请点击选择相应选项！' onclick=\"show_layer_more_links($(this));event.stopPropagation();\">{$title}</a>
<div style='display:none;'><div class='more-links-warp'>{$str}</div>
</div>";
        return $code;
    }
}
