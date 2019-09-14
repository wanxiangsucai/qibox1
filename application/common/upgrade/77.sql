DROP TABLE IF EXISTS `qb_friend`;
CREATE TABLE IF NOT EXISTS `qb_friend` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `suid` int(7) NOT NULL COMMENT '被关注者的uid，即自己的uid',
  `uid` int(7) NOT NULL COMMENT '关注者uid，即他人uid',
  `create_time` int(10) NOT NULL COMMENT '加入时间',
  `type` tinyint(1) NOT NULL COMMENT '0是粉丝,1是单向好友,-1黑名单,2是双向好友',
  `update_time` int(10) NOT NULL COMMENT '最后访问时间',
  `view` mediumint(7) NOT NULL DEFAULT '1' COMMENT '访问次数',
  PRIMARY KEY (`id`),
  KEY `view` (`view`),
  KEY `uid` (`uid`,`type`),
  KEY `update_time` (`update_time`),
  KEY `suid` (`suid`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='粉丝好友' AUTO_INCREMENT=1 ;

ALTER TABLE  `qb_memberdata` ADD  `map_x` DOUBLE NOT NULL COMMENT  '地图经度坐标',ADD  `map_y` DOUBLE NOT NULL COMMENT  '地图纬度坐标';
