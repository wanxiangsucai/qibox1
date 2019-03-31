DROP TABLE IF EXISTS `qb_moneytype`;
CREATE TABLE IF NOT EXISTS `qb_moneytype` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '虚拟币名称',
  `icon` varchar(50) NOT NULL COMMENT '图标',
  `dw` varchar(10) NOT NULL DEFAULT '个' COMMENT '单位',
  `group_ratio` varchar(255) NOT NULL COMMENT '每个用户组的兑换指数',
  `more_ratio` text NOT NULL COMMENT '其它兑换指数比例',
  `list` int(10) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='虚拟币种类' AUTO_INCREMENT=5 ;

ALTER TABLE  `qb_moneytype` ADD  `auto_change` TINYINT( 1 ) NOT NULL COMMENT  '是否自动兑换',ADD  `change_cfg` TEXT NOT NULL COMMENT  '用户设置的是否自动兑换开关';


INSERT INTO `qb_moneytype` (`id`, `name`, `icon`, `dw`, `group_ratio`, `more_ratio`, `list`) VALUES(1, '回香豆', 'glyphicon glyphicon-tint', '个', '{"2":"","3":"40","8":"10","11":"10","12":"10","13":"10"}', '{"user":{"weixin_api":"5","wx_attention":"10","email_yz":"1","mob_yz":"20","idcard_yz":"1","regdate":"3|1\\r\\n6|2\\r\\n12|5\\r\\n","addrmb":"500|5\\r\\n1000|10\\r\\n2000|15\\r\\n3000|20","addtopic":"100|5\\r\\n200|10","addreply":"100|5\\r\\n200|10\\r\\n400|15\\r\\n800|20"},"types":{"2":"100|1\\r\\n200|2\\r\\n300|3\\r\\n400|4\\r\\n500|5","3":"10|1\\r\\n20|2\\r\\n30|3\\r\\n40|4\\r\\n50|5\\r\\n60|6\\r\\n70|7\\r\\n80|8\\r\\n90|9\\r\\n100|10","4":""}}', 0);
INSERT INTO `qb_moneytype` (`id`, `name`, `icon`, `dw`, `group_ratio`, `more_ratio`, `list`) VALUES(2, '点赞币', 'fa fa-fw fa-thumbs-o-up', '个', '', '', 0);
INSERT INTO `qb_moneytype` (`id`, `name`, `icon`, `dw`, `group_ratio`, `more_ratio`, `list`) VALUES(3, '签到币', 'fa fa-fw fa-fire', '个', '', '', 0);
INSERT INTO `qb_moneytype` (`id`, `name`, `icon`, `dw`, `group_ratio`, `more_ratio`, `list`) VALUES(4, '活跃度', 'fa fa-fw fa-database', '个', '', '', 0);


DROP TABLE IF EXISTS `qb_money`;
CREATE TABLE IF NOT EXISTS `qb_money` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `uid` int(7) NOT NULL COMMENT '用户UID',
  `money` int(7) NOT NULL COMMENT '某种类型的虚拟币总值',
  `type` smallint(4) NOT NULL COMMENT '虚拟币类型',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `uid` (`uid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户自定义虚拟币' AUTO_INCREMENT=1 ;

ALTER TABLE  `qb_moneylog` ADD  `type` smallint( 4 ) NOT NULL COMMENT  '虚拟币类型,比如威望、金豆,0是系统积分';
ALTER TABLE  `qb_moneylog` ADD INDEX (  `type` );


UPDATE `qb_config` SET `c_value`='积分'  WHERE `c_key`='MoneyName';
