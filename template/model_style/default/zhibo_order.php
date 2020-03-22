<?php

return [
	'title'=>'直播预约',
	'form'=>[
		['number','order_time','提前多少分钟通知','单位分钟,即在开播之前,提前通知',10],
		['color','bgcolor','背景颜色'],
		['color','bordercolor','边框颜色'],
		['color','timecolor','时间颜色'],
	],
	'form_title'=>'相关设置',
	'forbid_field'=>'rows,fidtype,fids,cleng,ispic,order,by,status',	//禁用系统的字段
	'hide_model'=>true,	//禁止显示模型选择
	'type1'=>'',		//wap 或 pc
	'type2'=>'',		//www 或 hy 
	'type3'=>'big',		//这一项是为PC考虑的,WAP不需要考虑,可设置big small 或留空. big代表很宽的 small代表窄边
	'quote'=>'cms|3',    //设置为true发布信息时允许站内引用使用此风格,不允许使用就删除或设置为false,若要指定频道使用的话,就设置频道的目录名,如果即要限频道又要限模型的话,就用类似这样的格式化 cms|3 如果有多个模型或多个频道的话,就用逗号隔开,比如说  cms,bbs,shop|3,2,4
];