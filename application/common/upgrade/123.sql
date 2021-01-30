ALTER TABLE  `qb_admin_menu` ADD  `role` VARCHAR( 80 ) NOT NULL COMMENT  '可以使用的角色,多个用逗号隔开,留空不限';
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 1, '用户角色名称', 'role_name', '个人\r\n政企', 'textarea', '', 1, '', '每个名称换一行', -10, 0);
