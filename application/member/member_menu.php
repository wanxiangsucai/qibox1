<?php

return array(
        'often'=>array(
                'title'=>'常用菜单',
                'icon'=>'fa fa-fw fa-star',
                'sons'=>array(
                ),
        ),
        'base'=>array(
                'title'=>'基础设置',
				'icon'=>'fa fa-windows',
                'sons'=>array(
                        array(
                                'icon'=>'fa fa-windows',
                                'title'=>'会员基础设置',
                                'sons'=>array(
                                        array(
                                            'icon'=>'fa fa-user-circle',
                                                'title'=>'修改个人资料',
                                                'link'=>'user/edit',
                                                //'type'=>'pc',
                                        ),
                                        array(
                                            'icon'=>'fa fa-envelope-o',
                                                'title'=>'站内短消息',
                                                'link'=>'msg/index',
                                        ),
                                        array(
                                            'icon'=>'fa fa-volume-up',
                                                'title'=>'消息提醒设置',
                                                'link'=>'remind/set',
                                        ),
//                                         array(
//                                                 'title'=>'积分充值',
//                                                 'link'=>'jifen/add',
//                                         ),
//                                         array(
//                                                 'title'=>'积分消费记录',
//                                                 'link'=>'jifen/index',
//                                         ),
										array(
										    'icon'=>'fa fa-wechat',
                                                'title'=>'绑定第三方登录',
                                                'link'=>'bindlogin/weixin',
                                        ),
										array(
										    'icon'=>'glyphicon glyphicon-circle-arrow-up',
                                                'title'=>'认证身份/升级等级',
                                                'link'=>'group/index',
                                        ),
                                        array(
                                            'icon'=>'fa fa-drivers-license',
                                                'title'=>'身份验证',
                                                'link'=>'yz/index',
                                        ),
                                ),
                        ), 
                )
        ),
		'plugin'=>array(
                'title'=>'功能插件',
				'icon'=>'fa fa-fw fa-puzzle-piece',
                'sons'=>array(
                ),
        ),
		'module'=>array(
                'title'=>'频道模块',
				'icon'=>'fa fa-fw fa-cubes',
                'sons'=>array(
                ),
        ),        
);

