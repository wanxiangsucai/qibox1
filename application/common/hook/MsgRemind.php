<?php
namespace app\common\hook;

class MsgRemind{
    

    public $info = [
            //归属接口,必填
            'hook_key'=>'layout_body_foot',
            //归属插件,可为空
            'plugin_key'=>'',
            //开发者
            'author'=>'齐博',
            //开发者网站
            'author_url'=>'http://www.php168.com',
            //功能描述
            'about'=>'有新的站内消息,就弹层提醒',
    ];
	
	//钩子行为
    public function run(&$user){
        $msgurl = murl('member/wxapp.msg/checknew');
        $readmsg = murl('member/msg/index');
print<<<EOT
<script type="text/javascript">
if($.cookie('msg_remind')!='no'&&"{$user['uid']}"!=''){
	$.get("{$msgurl}",function(res){
		if(res.code==0){
			layer.confirm('你有 '+res.data.num+' 条新的消息,是否现在查看？', {
				btn : [ '现在查看', '晚点再看' ]
			},function(index) {
				location.href="{$readmsg}";
			},function(index) {
				$.cookie('msg_remind', 'no', { expires: 10, path: '/' });   //10分钟提醒一次
			});
		}
	})
}
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