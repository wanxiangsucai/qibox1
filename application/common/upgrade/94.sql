DROP TABLE IF EXISTS `qb_radis_index`;
CREATE TABLE IF NOT EXISTS `qb_radis_index` (
  `k` varchar(256) NOT NULL COMMENT 'key值',
  `v` text NOT NULL COMMENT 'value值',
  `t` int(10) NOT NULL COMMENT '有效期',
  UNIQUE KEY `k` (`k`),
  KEY `time` (`t`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='解决没有安装radis的兼容处理,当缓存用';


DROP TABLE IF EXISTS `qb_radis_list`;
CREATE TABLE IF NOT EXISTS `qb_radis_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `k` varchar(256) NOT NULL COMMENT 'key值',
  `v` text NOT NULL COMMENT 'value值',
  PRIMARY KEY (`id`),
  KEY `k` (`k`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='解决没有安装radis的兼容处理,相当于radis的列表' AUTO_INCREMENT=7 ;


INSERT INTO `qb_chatmod` (`id`, `uid`, `aid`, `type`, `name`, `about`, `icon`, `pcwap`, `keywords`, `init_jsfile`, `init_iframe`, `init_jscode`, `status`, `list`, `allowgroup`) VALUES(0, 0, 0, 1, '菜单模块', '', '', 0, 'menu', '/public/static/libs/bui/pages/menu/init.js', '', '', 1, 0, '');
