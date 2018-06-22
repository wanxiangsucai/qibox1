<?php

return array(
		'plugin'=>array(
				'title'=>'插件',
				'sons'=>array(
							array(
								'title'=>'营销功能',
								'sons'=>array(
// 								        array(
// 								                'title'=>'营销基础设置',
// 								                'link'=>'operation/set',
// 								        ),
								        array(
								                'title'=>'会员提现申请',
								                'link'=>'rmb_getout/index',
								                'power'=>['delete','pay'=>'给用户付款','log'=>'查看用户收入明细'],
								        ),
								        array(
								                'title'=>'人民币充值管理',
								                'link'=>'rmb_infull/index',
								                'power'=>['delete'],
								        ),
								        array(
								                'title'=>'人民币消费/充值记录',
								                'link'=>'rmb_consume/index',
								                'power'=>['delete'],
								        ),
								        array(
								                'title'=>'积分消费/赚取记录',
								                'link'=>'moneylog/index',
								                'power'=>['delete'],
								        ),
								),
							),
				),
		),
);
