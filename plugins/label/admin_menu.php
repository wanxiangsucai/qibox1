<?php

return array(
		'plugin'=>array(
				'title'=>'插件',
				'sons'=>array(
							array(
								'title'=>'标签管理',
								'sons'=>array(
								        array(
								                'title'=>'全站标签管理(修复)',
								                'link'=>'index/index',
								                'power'=>['edit','delete','set'=>'标签设置'],
								        ),
								        array(
								                'title'=>'站外APP标签管理',
								                'link'=>'applabel/index',
								                'power'=>['add','edit','delete','set'=>'标签设置'],
								        ),								        
								),
							),
				),
		),
);
