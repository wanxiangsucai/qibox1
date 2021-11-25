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
										    'title'=>'我发布的内容',
										    'link'=>'content/index',
									        'power'=>'can_post_group',
									),
									array(
										'title'=>'发布内容',
										'link'=>'content/postnew',
									     'power'=>'can_post_group',
									),
								        array(
								                'title'=>'采集公众号文章',
								                'link'=>'content/copynews',
								                'power'=>'can_post_group',
								        ),
										array(
								                'title'=>'分类管理',
								                'link'=>'mysort/index',
										        'power'=>'can_post_group',
								        ),


										array(
											'title'=>'主题审核管理',
											'link'=>'content/manage',
											'power'=>function(){
												$dirname =  basename(__DIR__);
												if(fun('admin@sort',0,$dirname)===true || fun('admin@status_power',$dirname)){
													return true;
												}
												return false;
											},
										),



								),
							),
				),
		),
);
 