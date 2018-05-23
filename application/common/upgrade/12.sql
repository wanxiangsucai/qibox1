
CREATE TABLE IF NOT EXISTS `qb_market` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL COMMENT '类型:比如admin_style index_style member_style等等',
  `keywords` varchar(50) NOT NULL COMMENT '关键字',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '安装日期',
  `author` varchar(80) NOT NULL COMMENT '开发者',
  `author_url` varchar(120) NOT NULL COMMENT '开发者网站',
  `version` varchar(60) NOT NULL COMMENT '版本信息',
  `version_id` mediumint(7) NOT NULL DEFAULT '0' COMMENT '云端对应的ID',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `keywords` (`keywords`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='云端市场购买的应用，主要是做升级核对，但频道、插件、钩子不在这个表，目前主要是风格，后续可以拓展更多的' AUTO_INCREMENT=1 ;