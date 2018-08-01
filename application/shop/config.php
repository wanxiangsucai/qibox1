<?php

return [
	//自动表单 前台列表页母模板
	'automodel_listpage'=>APP_PATH.'shop/view/index/default/content/list.htm',
	//自动表单 前台详情展示页母模板
    'automodel_showpage'=>APP_PATH.'shop/view/index/default/content/show.htm',
	
	//自动表单 前台辅栏目列表页母模板
	'automodel_category_listpage'=>APP_PATH.'common/builder/listpage/category_list.htm',
	
	//发布信息选择模型页模板
    'post_choose_model'=>APP_PATH.'common/builder/sort/model_list.htm',
	//发布信息选择栏目页模板
    'post_choose_sort'=>APP_PATH.'common/builder/sort/layout.htm',
	
    'car_one'=>false,   //设置为true的时候,购物车只保留最后那个商品,也即取消购物车功能
    //发布内容必须要选择栏目
    'post_need_sort'=>true,
    //模块关键字，目录名，也是数据表区分符    
     'system_dirname'=>basename(__DIR__),
];