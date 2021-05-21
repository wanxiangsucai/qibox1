ALTER TABLE  `qb_shorturl` ADD  `wxapp_id` VARCHAR( 20 ) NOT NULL COMMENT  '第三方小程序ID';
ALTER TABLE  `qb_shorturl` ADD  `expire_time` INT( 10 ) NOT NULL COMMENT  '失效日期，默认为空';
ALTER TABLE  `qb_shorturl` DROP INDEX  `type` ,ADD INDEX  `type` (  `type` ,  `url` ,  `wxapp_id` ) COMMENT  '';
ALTER TABLE  `qb_shorturl` ADD INDEX (  `expire_time` ) COMMENT  '';

INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 4, 'PC微信扫码登录方式', 'scan_login_type', 'mp', 'radio', 'mp|公众号H5码\r\nwxapp|小程序码', 1, '', '如果配置了小程序的话，就推荐用小程序码', 0, -2);
