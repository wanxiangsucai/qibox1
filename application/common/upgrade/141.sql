ALTER TABLE `qb_memberdata` ADD `qq_open_api` VARCHAR( 32 ) NOT NULL COMMENT '后期申请的APP专属QQ登录' AFTER `qq_api` ;
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 20, 'APP专属QQ登录appid', 'qqopen_appid', '', 'text', '', 1, '', '前期申请的与网页QQ登录相同，就留空（后期申请的，都不相同）', 0, -9);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 20, 'APP专属QQ登录接口密钥', 'qqopen_appsecret', '', 'text', '', 1, '', '前期申请的与网页QQ登录相同，就留空（后期申请的，都不相同）', 0, -9);

