DROP TABLE IF EXISTS `qb_msguser`;
CREATE TABLE IF NOT EXISTS `qb_msguser` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(8) NOT NULL COMMENT '当前用户UID',
  `aid` int(8) NOT NULL COMMENT '正数其它用户UID或负数圈子ID',
  `list` int(10) NOT NULL COMMENT '排序值,默认是时间,置顶的话,大于时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`list`),
  KEY `aid` (`aid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='类似微信首页的用户聊天记录用户列表' AUTO_INCREMENT=1 ;
