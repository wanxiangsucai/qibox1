INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 1, '用户升级用户组是否自动审核', 'forbid_auto_upgroup', '0', 'radio', '0|自动审核\r\n1|人工审核', 1, '', '即使人工审核,也会先扣费的,所以推荐自动审核', -4, 0);
UPDATE`qb_config` SET `list`='-4',title='用户组升级方式'  WHERE `c_key`='up_group_use_rmb';
