ALTER TABLE `qb_webmenu` ADD `sysname` VARCHAR( 30 ) NOT NULL COMMENT '归属频道,留空则是系统专用';
ALTER TABLE `qb_webmenu` ADD INDEX ( `sysname` , `type` );
ALTER TABLE `qb_webmenu` CHANGE `sysname` `sysname` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '_sys_' COMMENT '归属频道,留空则是系统专用';
UPDATE `qb_webmenu` SET `sysname`='_sys_';
