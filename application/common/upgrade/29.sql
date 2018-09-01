DROP TABLE IF EXISTS `qb_shop_mysort`;
CREATE TABLE IF NOT EXISTS `qb_shop_mysort` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户的UID',
  `name` varchar(80) NOT NULL COMMENT '分类名称',
  `list` int(10) NOT NULL,
  `logo` varchar(50) NOT NULL COMMENT '封面图',
  `ext_sys` smallint(4) NOT NULL COMMENT '扩展字段,关联的系统',
  `ext_id` mediumint(7) NOT NULL COMMENT '扩展字段,关联的系统ID',
  PRIMARY KEY (`id`),
  KEY `mid` (`uid`),
  KEY `pid` (`pid`),
  KEY `list` (`list`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户自定义商品分类' AUTO_INCREMENT=1;

INSERT INTO `qb_shop_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`) VALUES(0, 'myfid', '我的分类', 'select', 'int(7) NOT NULL DEFAULT ''0''', '', 'shop_mysort@id,name@uid', '', 1, 1, '', '', '', '', '', 2, '', '', '', 1, 0, 0, 0);

ALTER TABLE  `qb_shop_content1` ADD  `myfid` MEDIUMINT( 7 ) NOT NULL COMMENT  '我的分类';
ALTER TABLE  `qb_shop_content1` ADD INDEX (  `myfid` );

INSERT INTO `qb_qun_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'style', '个性风格', 'select', 'varchar(30) NOT NULL', '', 'app\\common\\util\\Style@get_style@["qun"]', '', 1, 1, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '');

ALTER TABLE  `qb_qun_content1` ADD  `style` VARCHAR( 30 ) NOT NULL COMMENT  '个性风格';