ALTER TABLE  `qb_appstore_field` CHANGE  `title`  `title` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '字段标题';

ALTER TABLE  `qb_form_field` CHANGE  `title`  `title` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '字段标题';
ALTER TABLE  `qb_bbs_field` CHANGE  `title`  `title` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '字段标题';

ALTER TABLE  `qb_cms_field` CHANGE  `title`  `title` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '字段标题';
ALTER TABLE  `qb_shop_field` CHANGE  `title`  `title` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '字段标题';

ALTER TABLE  `qb_module` CHANGE  `version`  `version` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '版本信息';
ALTER TABLE  `qb_plugin` CHANGE  `version`  `version` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '版本信息';
ALTER TABLE  `qb_module` ADD  `version_id` MEDIUMINT( 7 ) NOT NULL COMMENT  '对应官方的APP应用ID,升级用来核对';
ALTER TABLE  `qb_plugin` ADD  `version_id` MEDIUMINT( 7 ) NOT NULL COMMENT  '对应官方的APP应用ID,升级用来核对';