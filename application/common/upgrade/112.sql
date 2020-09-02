INSERT INTO `qb_config_group` (`id`, `title`, `list`, `sys_id`, `ifshow`, `ifsys`) VALUES(5, '会员基础菜单', 900, 0, 1, 1);

INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 5, '显示站内短消息的用户组', 'menu_usemsg_group', '', 'usergroup2', '', 1, '', '不在会员中心显示,但有权限', 0, 0);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 5, '显示消息提醒设置的用户组', 'menu_msg_remind_group', '', 'usergroup2', '', 1, '', '不在会员中心显示,但有权限', 0, 0);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 5, '显示绑定微信QQ的用户组', 'menu_bindlogin_group', '', 'usergroup2', '', 1, '', '不在会员中心显示,但有权限', 0, 0);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 5, '显示升级会员等级的用户组', 'menu_upgroup_group', '', 'usergroup2', '', 1, '', '不在会员中心显示,但有权限', 0, 0);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 5, '显示手机身份验证的用户组', 'menu_yzmob_group', '', 'usergroup2', '', 1, '', '不在会员中心显示,但有权限', 0, 0);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 5, '显示好友粉丝管理的用户组', 'menu_friend_group', '', 'usergroup2', '', 1, '', '不在会员中心显示,但有权限', 0, 0);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 5, '显示应用市场的用户组', 'menu_market_group', '', 'usergroup2', '', 1, '', '不在会员中心显示,但有权限', 0, 0);

DROP TABLE IF EXISTS `qb_module_buyer`;
CREATE TABLE IF NOT EXISTS `qb_module_buyer` (
  `id` mediumint(7) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(7) NOT NULL COMMENT '购买者UID',
  `mid` mediumint(5) NOT NULL COMMENT '模块ID是正数,插件ID是负数',
  `create_time` int(10) NOT NULL COMMENT '购买日期',
  `endtime` int(10) NOT NULL COMMENT '失效日期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='模块购买者记录' AUTO_INCREMENT=1 ;


ALTER TABLE  `qb_module` ADD  `testday` SMALLINT( 5 ) NOT NULL COMMENT  '允许体验几天',ADD  `money` VARCHAR( 256 ) NOT NULL COMMENT  '售价,比如“30|5|一个月”第一项是天数，第二项是售价，第三项是名称，多个的话。换行';
ALTER TABLE  `qb_module` CHANGE  `admingroup`  `admingroup` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '允许免费使用的用户组';

ALTER TABLE  `qb_plugin` ADD  `testday` SMALLINT( 5 ) NOT NULL COMMENT  '允许体验几天',ADD  `money` VARCHAR( 256 ) NOT NULL COMMENT  '售价,比如“30|5|一个月”第一项是天数，第二项是售价，第三项是名称，多个的话。换行';

ALTER TABLE  `qb_plugin` ADD  `admingroup` VARCHAR( 150 ) NOT NULL COMMENT  '允许免费使用的用户组';

ALTER TABLE  `qb_plugin` ADD  `picurl` VARCHAR( 256 ) NOT NULL COMMENT  '封面图';
ALTER TABLE  `qb_module` ADD  `picurl` VARCHAR( 256 ) NOT NULL COMMENT  '封面图';

ALTER TABLE  `qb_plugin` ADD  `is_sell` TINYINT( 1 ) NOT NULL COMMENT  '是否上架应用市场';
ALTER TABLE  `qb_module` ADD  `is_sell` TINYINT( 1 ) NOT NULL COMMENT  '是否上架应用市场';