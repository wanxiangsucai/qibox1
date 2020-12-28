<?php

return [
    'title'=>'底部菜单相关功能设置',
	'form'=>[
		['radio','hidelay','点击小圆点显示大窗','',['启用','不启用']],
		['color','pointbgcolor','小圆点背景色','','#1bb0d2'],
		['text','title1','第一行标题','','随时随地交流，掌控身边动态！'],
		['text','title2','第二行标题','','创建属于自己的圈子'],
		['text','title3','第三行标题','','发现生活乐圈，绽放精彩人生'],
		['text','title4','第四行标题','','点击下载APP，可享受更多权益'],
		['text','link1','链接一','',murl('qun/content/add',['mid'=>1])],		
		['radio','hidedownapp','是否提示APP下载','',['显示','隐藏']],
		['file','appurl','app下载地址',''],
		['radio','hide2icon','是否显示二级菜单图标','',['显示','隐藏']],
	],
	//'help_msg'=>'帮助信息',
	//联动显示
	'trigger'=>[
		['hidelay','0','pointbgcolor,title1,title2,title3,title4,link1,appurl,hidedownapp'],	//第二第三项,多个用逗号隔开
		['hidelay','1','hide2icon'],
	],
	'type1'=>'', //wap 或 pc
	'type2'=>'', //www 或 hy 
	'type3'=>'', //对PC而言可设置big small  其中 big代表很宽的 small代表窄边,加了斜杠的话,就根据网址判断
	//'quote'=>false, //设置为true发布信息时允许站内引用使用此风格,不允许使用就删除或设置为false,若要指定频道使用的话,就设置频道的目录名,如果即要限频道又要限模型的话,就用类似这样的格式化 cms|3
];