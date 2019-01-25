DELETE FROM  `qb_config` WHERE  `c_key` =  'group_expire_data';
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 1, '用户组升级认证', 'up_group_use_rmb', '', 'radio', '0|使用积分\r\n1|使用金额', 1, '', '', 0, 0);
