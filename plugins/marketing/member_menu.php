<?php

if(config('webdb.typeid')&&is_file(__DIR__.'/'.config('webdb.typeid').'member_menu.php')){
    return include_once __DIR__.'/'.config('webdb.typeid').'member_menu.php';
}

return array(
		'plugin'=>array(
				'title'=>'插件',
				'sons'=>array(
							array(
								'title'=>'财务与积分功能',
								'sons'=>array(
								        array(
								                'title'=>'积分日志',
								                'link'=>'jifen/index',
								        ),
								        array(
								                'title'=>'积分充值',
								                'link'=>'jifen/add',
								        ),
								        array(
								                'title'=>'我的财务信息',
								                'link'=>'rmb/index',
								        ),
								        array(
								                'title'=>'收款帐号设置',
								                'link'=>'rmb/edit',
								        ),
								        array(
								                'title'=>'虚拟币兑换积分',
								                'link'=>'money/index',
								        ),
								),
							),
				),
		),
);
