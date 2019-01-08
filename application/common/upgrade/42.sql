DROP TABLE IF EXISTS `qb_groupcfg`;
CREATE TABLE IF NOT EXISTS `qb_groupcfg` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` mediumint(6) NOT NULL COMMENT '用户组ID',
  `title` varchar(32) NOT NULL COMMENT '字段名称标题',
  `c_key` varchar(50) NOT NULL DEFAULT '' COMMENT '字段名',
  `c_value` text NOT NULL COMMENT '字段值',
  `form_type` varchar(16) NOT NULL COMMENT '字段表单类型',
  `options` varchar(256) NOT NULL COMMENT '字段参数 比如多选或单选要用到',
  `htmlcode` text NOT NULL COMMENT 'html额外代码',
  `c_descrip` varchar(256) NOT NULL COMMENT '选项详细介绍描述',
  `list` int(10) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `list` (`list`),
  KEY `c_key` (`c_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='系统参数配置' AUTO_INCREMENT=1 ;

ALTER TABLE  `qb_memberdata` ADD  `ext_field` TEXT NOT NULL COMMENT  '自定义字段参数';


ALTER TABLE  `qb_group` ADD  `logo` VARCHAR( 150 ) NOT NULL COMMENT  '图标';
ALTER TABLE  `qb_group` ADD  `wap_page` VARCHAR( 150 ) NOT NULL COMMENT  'wap个人主页模板',ADD  `wap_member` VARCHAR( 150 ) NOT NULL COMMENT  'wap会员中心模板',ADD  `pc_page` VARCHAR( 150 ) NOT NULL COMMENT  'pc个人主页模板',ADD  `pc_member` VARCHAR( 150 ) NOT NULL COMMENT  'pc会员中心模板';