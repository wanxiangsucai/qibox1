<?php
//菜单权限教程 https://www.kancloud.cn/php168/x1_of_qibo/816623

if(config('webdb.typeid')&&is_file(__DIR__.'/'.config('webdb.typeid').'member_menu.php')){
    return include_once __DIR__.'/'.config('webdb.typeid').'member_menu.php';
}

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
								        'title'=>'创作收益',
								        'link'=>'sell_log/index',
								        'power'=>'can_post_group',
								    ),
								    array(
								        'title'=>'我要分享',
								        'link'=>'content/fx',
								        'power'=>'allow_fx_group',
								    ),
								    array(
								        'title'=>'我创建的优惠券',
								        'link'=>'youhui/index',
								        'power'=>'allow_fx_group',
								    ),
								    array(
								        'title'=>'我领取的优惠券',
								        'link'=>'yhlog/index',
								         
								    ),
								    array(
								            'title'=>'我的分享记录',
								            'link'=>'fxuser/index',
								            'power'=>'allow_fx_group',
								    ),
								    array(
								                'title'=>'我的分享收益',
								                'link'=>'fxlog/index',
								                'power'=>'allow_fx_group',
								     ),
								    array(
								        'title'=>'主题审核管理',
								        'link'=>'content/manage',
								        'power'=>function(){
								            $dirname =  basename(__DIR__);
								            if(fun('admin@sort',0,$dirname)===true || fun('admin@status_power',$dirname) || fun('sort@admin',0,$dirname)){
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
 