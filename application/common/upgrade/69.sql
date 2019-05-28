DROP TABLE IF EXISTS `qb_timed_log`;
CREATE TABLE IF NOT EXISTS `qb_timed_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `taskid` smallint(6) NOT NULL COMMENT '对应的任务ID值',
  `create_time` int(10) NOT NULL COMMENT '执行时间',
  `times` double(7,4) NOT NULL COMMENT '执行消耗时间多少秒',
  PRIMARY KEY (`id`),
  KEY `taskid` (`taskid`),
  KEY `create_time` (`create_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='定时任务执行日志' AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `qb_timed_task`;
CREATE TABLE IF NOT EXISTS `qb_timed_task` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(15) NOT NULL COMMENT 't_once仅一次，t_month每月一次，t_week每周一次，t_day每天一次，t_days每几天一次，t_hours每几小时一次，t_minutes每几分钟一次',
  `title` varchar(255) NOT NULL COMMENT '任务描述',
  `class_file` varchar(255) NOT NULL COMMENT '运行脚本',
  `class_method` varchar(15) NOT NULL COMMENT '指定类的方法名',
  `ext` text NOT NULL COMMENT '相关扩展参数',
  `create_time` int(10) NOT NULL COMMENT '创建日期',
  `day` tinyint(2) NOT NULL COMMENT '每月的哪天执行',
  `week` tinyint(1) NOT NULL COMMENT '每周的星期几执行',
  `ymd` varchar(10) NOT NULL COMMENT '年月日,比如2018-12-12',
  `days` tinyint(3) NOT NULL COMMENT '每隔几天就执行一次',
  `hours` tinyint(2) NOT NULL COMMENT '每隔几小时就执行一次',
  `minutes` tinyint(2) NOT NULL COMMENT '每隔几分钟就执行一次',
  `his` varchar(8) NOT NULL COMMENT '具体几时几分执行，比如13:25:00（每隔几小时或几分的这里为空）',
  `num` int(7) NOT NULL COMMENT '总共被执行过多少次',
  `list` int(10) NOT NULL COMMENT '排序值',
  `ifopen` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `last_time` int(10) NOT NULL COMMENT '上次执行时间',
  `use_time` double(7,4) NOT NULL COMMENT '上次执行消耗时间',
  `author` varchar(50) NOT NULL COMMENT '开发者',
  `version_id` int(7) NOT NULL COMMENT '应用市场ID',
  `version` varchar(60) NOT NULL COMMENT '版本号',
  PRIMARY KEY (`id`),
  KEY `list` (`list`),
  KEY `type` (`type`,`num`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='定时任务计划表' AUTO_INCREMENT=1 ;


INSERT INTO `qb_hook_plugin` (`id`, `hook_key`, `plugin_key`, `hook_class`, `about`, `ifopen`, `list`, `author`, `author_url`, `version`, `version_id`) VALUES(0, 'layout_body_foot', '', 'app\\index\\controller\\Task', '定时任务前台唤醒', 1, 0, '', '', '', 0);
