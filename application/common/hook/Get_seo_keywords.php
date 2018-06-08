<?php
namespace app\common\hook;

class Get_seo_keywords{
    

    public $info = [
            //归属接口,必填
            'hook_key'=>'template_form_foot',
            //归属插件,可为空
            'plugin_key'=>'',
            //开发者
            'author'=>'齐博',
            //开发者网站
            'author_url'=>'http://www.php168.com',
            //功能描述
            'about'=>'自动获取热门关键字',
    ];
    
	//钩子行为
    public function run(&$user){
        $static_path = config('view_replace_str.__STATIC__');
print<<<EOT
<script src="{$static_path}/js/core/jquery.min.js"></script>
<script type="text/javascript">
function get_hot_keyword(word) { 
    var qsData = { 'wd': word, 'p': '3', 'cb': 'CallbackWord', 't': new Date().getMilliseconds().toString() };  
	$.ajax({ 
			async: false,  
			url: "http://suggestion.baidu.com/su",  
			type: "GET",  
			dataType: 'jsonp',
			//jsonp: 'jsoncallback', 
			//jsonpCallback:"Callback",
			data: qsData,  
			timeout: 500,  
			success: function (json) {  
        },  
			error: function (xhr) {
        }  
    });
}
function CallbackWord(res){
	data = res.s;
	if(data.length>0){
		var str = '';
		data.forEach(function(rs){
			str += rs+" ";
		});
		$("input[name='keywords']").val(str);
	}
}
$("input[name='keywords']").blur(function(){
  get_hot_keyword($(this).val());
});
</script>
EOT;
    }
	
	
	//卸载时运行 
	public function uninstall($id=0){		
	}
	
	//安装时运行
	public function install($id=0){		
	}
    
}