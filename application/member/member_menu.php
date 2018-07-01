<?php

return array(
        'base'=>array(
                'title'=>'基础设置',
				'icon'=>'fa fa-windows',
                'sons'=>array(
                        array(
                                'title'=>'会员基础设置',
                                'sons'=>array(
                                        array(
                                                'title'=>'修改个人资料',
                                                'link'=>'user/edit',
                                                //'type'=>'pc',
                                        ),
                                        array(
                                                'title'=>'站内短消息',
                                                'link'=>'msg/index',
                                        ),
                                        array(
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
                                                'title'=>'绑定第三方登录',
                                                'link'=>'bindlogin/weixin',
                                        ),
										array(
                                                'title'=>'升级会员等级',
                                                'link'=>'group/index',
                                        ),
                                        array(
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

