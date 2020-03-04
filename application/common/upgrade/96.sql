ALTER TABLE  `qb_webmenu` ADD  `fontcolor` VARCHAR( 10 ) NOT NULL COMMENT  '字体颜色',ADD  `bgcolor` VARCHAR( 10 ) NOT NULL COMMENT  '背景颜色';
ALTER TABLE  `qb_webmenu` ADD  `script` TEXT NOT NULL COMMENT  '脚本事件';

ALTER TABLE  `qb_admin_menu` COMMENT =  '管理员常用菜单也包含会员个性菜单';

ALTER TABLE  `qb_admin_menu` ADD  `fontcolor` VARCHAR( 10 ) NOT NULL COMMENT  '字体颜色',ADD  `bgcolor` VARCHAR( 10 ) NOT NULL COMMENT  '背景颜色';
ALTER TABLE  `qb_admin_menu` ADD  `script` TEXT NOT NULL COMMENT  '脚本事件';
ALTER TABLE  `qb_admin_menu` CHANGE  `type`  `type` TINYINT( 1 ) NOT NULL COMMENT  '0是后台常用菜单,1是会员中心个性菜单';

ALTER TABLE  `qb_admin_menu` ADD  `title` VARCHAR( 40 ) NOT NULL COMMENT  '原来的菜单名称' AFTER  `name`;
ALTER TABLE  `qb_admin_menu` CHANGE  `name`  `name` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '新的菜单名称';
ALTER TABLE  `qb_admin_menu` ADD  `allowgroup` VARCHAR( 150 ) NOT NULL COMMENT  '哪些用户组可用,只针对系统菜单';
ALTER TABLE  `qb_admin_menu` ADD  `is_use` TINYINT( 1 ) NOT NULL COMMENT  '会员中心是否启用默认菜单个性设置';

ALTER TABLE  `qb_admin_menu` ADD INDEX (  `groupid` );
ALTER TABLE  `qb_admin_menu` ADD INDEX (  `is_use` );
ALTER TABLE  `qb_admin_menu` ADD INDEX (  `type` );