CREATE TABLE `qb_qun_buystyle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(8) NOT NULL DEFAULT '0' COMMENT '购买用户UID',
  `pageid` int(8) NOT NULL DEFAULT '0' COMMENT '圈子ID',
  `stylename` varchar(50) NOT NULL DEFAULT '' COMMENT '风格目录关键字',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '购买时间',
  `endtime` int(10) NOT NULL DEFAULT '0' COMMENT '风格使用截止时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='风格购买记录表'