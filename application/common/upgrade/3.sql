
CREATE TABLE IF NOT EXISTS `qb_bbs_infomsg` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `ext_id` int(7) NOT NULL COMMENT '扩展ID,比如可以给圈子统计',
  `ext_sys` smallint(4) NOT NULL COMMENT '扩展字段,关联的系统',
  `posttime` int(10) NOT NULL COMMENT '发贴更新时间',
  `today_post` mediumint(5) NOT NULL COMMENT '今日发贴量',
  `yesterday_post` mediumint(5) NOT NULL COMMENT '昨晚发贴量',
  `total_post` mediumint(7) NOT NULL COMMENT '总发贴量',
  `total_topic` mediumint(6) NOT NULL COMMENT '主题总数',
  `day_top_post` int(11) NOT NULL COMMENT '最高日发贴量',
  PRIMARY KEY (`id`),
  KEY `ext_id` (`ext_id`,`ext_sys`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='论坛的一些辅助信息,比如今日多少贴' AUTO_INCREMENT=1 ;