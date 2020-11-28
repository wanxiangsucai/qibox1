DELETE FROM qb_config  WHERE c_key IN ('reg_group00','RegHongBao');
INSERT INTO `qb_config_group` (`id`, `title`, `list`, `sys_id`, `ifshow`, `ifsys`) VALUES(6, '参数设置', 0, -7, 0, 1);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 6, '默认省份', 'province_id', '440000', 'number', '', 1, '', '', 0, -7);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 6, '默认城市', 'city_id', '440100', 'number', '', 1, '', '', 0, -7);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, 6, '默认区域(县城)', 'zone_id', '0', 'number', '', 1, '', '', 0, -7);
