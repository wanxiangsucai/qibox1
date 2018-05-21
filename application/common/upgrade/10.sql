ALTER TABLE `qb_msg` DROP INDEX  `touid` ,ADD INDEX  `touid` (  `touid` ,  `ifread` );

ALTER TABLE  `qb_hook_plugin` ADD  `version` VARCHAR( 60 ) NOT NULL COMMENT  '版本信息',ADD  `version_id` MEDIUMINT( 7 ) NOT NULL COMMENT  '云端对应的ID';