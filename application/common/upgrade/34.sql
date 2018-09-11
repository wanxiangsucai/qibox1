DROP TABLE IF EXISTS `qb_cms_mysort`;
CREATE TABLE IF NOT EXISTS `qb_cms_mysort` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户的UID',
  `name` varchar(80) NOT NULL COMMENT '分类名称',
  `list` int(10) NOT NULL,
  `logo` varchar(50) NOT NULL COMMENT '封面图',
  `ext_sys` smallint(4) NOT NULL COMMENT '扩展字段,关联的系统',
  `ext_id` mediumint(7) NOT NULL COMMENT '扩展字段,关联的系统ID',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `list` (`list`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户自定义分类' AUTO_INCREMENT=1 ;

INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'myfid', '我的分类', 'select', 'int(7) NOT NULL DEFAULT ''0''', '', 'cms_mysort@id,name@uid', '<script>if($("#atc_myfid").children().length<1)$("#form_group_myfid").hide();</script>', 1, 1, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '');
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'myfid', '我的分类', 'select', 'int(7) NOT NULL DEFAULT ''0''', '', 'cms_mysort@id,name@uid', '<script>if($("#atc_myfid").children().length<1)$("#form_group_myfid").hide();</script>', 1, 2, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '');

ALTER TABLE  `qb_cms_content1` ADD  `myfid` MEDIUMINT( 7 ) NOT NULL COMMENT  '我的分类';
ALTER TABLE  `qb_cms_content1` ADD INDEX (  `myfid` );

ALTER TABLE  `qb_cms_content2` ADD  `myfid` MEDIUMINT( 7 ) NOT NULL COMMENT  '我的分类';
ALTER TABLE  `qb_cms_content2` ADD INDEX (  `myfid` );