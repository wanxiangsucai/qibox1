<?php

return [
    'title'=>'商城商品',
	'form'=>[		
		['select','type3','下拉框','',['111','222'],0],
		['number','test2','数字项','',74],		
		['textarea','test4','多行文本','','默认内容'],
		['checkbox','type2','多选项','',['w1'=>'其一','w2'=>'其二'],'w1'],
		['radio','type1','单选项1','',['aa','bb'],0],
		['text','test1','这是自定义的1','','xxx'],
	],
	'help_msg'=>'商城设置帮助信息',
	//联动显示
	'trigger'=>[
		['type1','0','test1'],	//第二第三项,多个用逗号隔开
		['type3','0','test2,test4'],
	],
	'type1'=>'wap', //wap 或 pc
	'type2'=>'', //www 或 hy 
	'type3'=>'', //这一项是为PC考虑的,WAP不需要考虑,可设置big small 或留空. big代表很宽的 small代表窄边
];