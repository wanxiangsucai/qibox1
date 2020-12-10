<?php

return [
    'title'=>'',
	'form'=>[
		//['color','bgcolor','背景颜色','','#62B3E7'],
		//['color','fontcolor','字体颜色','','#ffffff'],
		//['images2','picurls','幻灯图片组图','图片尺寸1200*460'],
		//['text','url1','商品图一链接地址',''],
		['image','logo','LOGO图片',''],
		//['text','url2','商品图二链接地址',''],
		//['image','img2','幻灯图右边商品图二',''],
		
		//['select','type3','下拉框','',['111','222'],0],
		//['text','btitle','分类标题','',''],
		//['image','picurl','默认图片',''],
		//['text','title','默认标题',''],
		//['text','url','默认地址',''],
		//['textarea','test4','多行文本','','默认内容'],
		['checkbox','hidelogin','隐藏登录方式','',['qq'=>'QQ','wx'=>'微信','phone'=>'手机短信']],
		//['radio','type1','单选项1','',['aa','bb'],0],
		//['text','test1','这是自定义的1','','xxx'],
	],
	//'help_msg'=>'帮助信息',
	//联动显示
	'trigger'=>[
		//['type1','0','test1'],	//第二第三项,多个用逗号隔开
		//['type3','0','test2,test4'],
	],
	'type1'=>'', //wap 或 pc
	'type2'=>'', //www 或 hy 
	'type3'=>'', //这一项是为PC考虑的,WAP不需要考虑,可设置big small 或留空. big代表很宽的 small代表窄边
	//'quote'=>false, //设置为true发布信息时允许站内引用使用此风格,不允许使用就删除或设置为false,若要指定频道使用的话,就设置频道的目录名,如果即要限频道又要限模型的话,就用类似这样的格式化 cms|3
];