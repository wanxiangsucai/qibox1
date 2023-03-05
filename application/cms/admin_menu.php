<?php

if(config('webdb.typeid')&&is_file(__DIR__.'/'.config('webdb.typeid').'admin_menu.php')){
    return include_once __DIR__.'/'.config('webdb.typeid').'admin_menu.php';
}

return array(
		'cms'=>array(
				'title'=>'cms',
				'sons'=>array(
							array(
								'title'=>'功能设置',
								'sons'=>array(
										array(
								                'title'=>'参数设置',
								                'link'=>'setting/index',
										        'power'=>[],
								            ),
								        array(
								                'title'=>'发布内容',
								                'link'=>'content/postnew',
    								            'power'=>[
    								                'pages/index'=>'分页管理',
    								                'pages/add'=>'添加分页',
    								                'pages/edit'=>'修改分页',
    								                'pages/delete'=>'删除分页',
    								            ],
								            ),
								        array(
								                'title'=>'内容管理',
								                'link'=>'content/index',
    								            'power'=>[
    								                'add'=>'发布内容',
    								                'edit'=>'修改内容',
    								                'delete'=>'删除内容',
    								                'index_fid'=>'更改栏目',
    								                'index_view'=>'更改浏览量',
    								                'index_list'=>'更改排序值',
    								                'index_status'=>'更改状态',
    								            ],
								              ),
    									array(
    										'title'=>'栏目管理',
    										'link'=>'sort/index',
    									 ),
    								    array(
    								            'title'=>'模型管理',
    								            'link'=>'module/index',
    								            'power'=>[
    								                    'add'=>'创建模型',
    								                    'edit'=>'修改模型',
    								                    'delete'=>'删除模型',
    								                    'field/index'=>'字段管理',
    								                    'field/add'=>'添加字段',
    								                    'field/edit'=>'修改字段',
    								                    'field/delete'=>'删除字段',
    								            ],
    								        ),
										array(
    										'title'=>'辅栏目管理',
    										'link'=>'category/index',
    									 ),
								        array(
								                'title'=>'辅栏目内容管理',
								                'link'=>'info/index',
								        ),
								        array(
								                'title'=>'栏目字段管理',
								                'link'=>['sort_field/index',['mid'=>-2]],
								                //'power'=>['edit','delete'],
								        ),
								        array(
								                'title'=>'辅栏目字段管理',
								                'link'=>['sort_field/index',['mid'=>-3]],
								                //'power'=>['edit','delete'],
								        ) ,
//     								    array(
//     								        'title'=>'创作收益日志',
//     								        'link'=>'sell_log/index',
// 											'power'=>['delete'],
//     								    ),
// 								        array(
// 								                'title'=>'用户分享记录',
// 								                'link'=>['fxuser/index'],
// 								                'power'=>['delete'],
// 								        ),
// 								        array(
// 								                'title'=>'用户分享收益',
// 								                'link'=>['fxlog/index'],
// 								                'power'=>['delete'],
// 								        ),
								),
							),
				),
		),
);
