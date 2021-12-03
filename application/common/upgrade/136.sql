INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 1, '用帐号密码登录是否启用滑动验证码', 'login_use_tncode', '0', 'radio', '0|禁用\r\n1|启用', 1, '', '', 0, 0);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 8, '是否启用滑动验证码', 'reg_use_tncode', '0', 'radio', '0|禁用\r\n1|启用', 1, '', '', 0, 0);
UPDATE `qb_config` SET `is_open`=1 WHERE `c_key` IN ('login_use_tncode','reg_use_tncode');
