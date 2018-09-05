ALTER TABLE  `qb_label` CHANGE  `name`  `name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '标签区分符';

ALTER TABLE  `qb_labelhy` CHANGE  `name`  `name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '标签区分符';

DROP TABLE IF EXISTS `qb_qun_menu`;
CREATE TABLE IF NOT EXISTS `qb_qun_menu` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `pid` mediumint(5) NOT NULL DEFAULT '0' COMMENT '父ID',
  `uid` mediumint(7) NOT NULL COMMENT '用户id',
  `aid` int(7) NOT NULL COMMENT '圈子黄页ID',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1是底部菜单,2是头部菜单',
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '链接名称',
  `url` varchar(150) NOT NULL DEFAULT '' COMMENT '链接地址',
  `target` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0本窗口打开,1新窗口打开',
  `ifshow` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1显示,0隐藏',
  `list` smallint(4) NOT NULL DEFAULT '0' COMMENT '排序值',
  `style` varchar(30) NOT NULL DEFAULT '' COMMENT 'CSS类名',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户定义的导航菜单' AUTO_INCREMENT=2 ;

ALTER TABLE  `qb_qun_content1` ADD  `ext_config` TEXT NOT NULL COMMENT  '扩展配置';