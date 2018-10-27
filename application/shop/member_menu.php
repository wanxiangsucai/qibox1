<?php
//菜单权限教程 https://www.kancloud.cn/php168/x1_of_qibo/816623

return array(
		'cms'=>array(
				'title'=>'cms',
				'sons'=>array(
							array(
								'title'=>'CMS功能',
								'sons'=>array(
    									array(
    										    'title'=>'商品管理',
    										    'link'=>'content/index',
    									        'power'=>'can_post_group',
    									),
    									array(
    										    'title'=>'发布商品',
    										    'link'=>'content/postnew',
    									        'power'=>'can_post_group',
    									),
								        array(
								                'title'=>'我订购的商品',
								                'link'=>'order/index',
								        ),
								        array(
								                'title'=>'客户的订单',
								                'link'=>'kehu_order/index',
								                'power'=>'can_post_group',
								        ),
								        array(
								                'title'=>'收货地址管理',
								                'link'=>'address/index',
								        ),
								        array(
								                'title'=>'分类管理',
								                'link'=>'mysort/index',
								                'power'=>'can_post_group',
								        ),
								),
							),
				),
		),
);
 