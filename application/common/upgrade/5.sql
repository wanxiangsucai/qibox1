ALTER TABLE  `qb_memberdata` ADD  `password` VARCHAR( 32 ) NOT NULL COMMENT  '密码' AFTER  `uid` ,
ADD  `password_rand` VARCHAR( 10 ) NOT NULL COMMENT  '密码混淆加密字串' AFTER  `password`;

UPDATE `qb_memberdata` SET password = (SELECT password FROM `qb_members` WHERE qb_members.uid = qb_memberdata.uid);

ALTER TABLE  `qb_memberdata` CHANGE  `uid`  `uid` MEDIUMINT( 7 ) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT  '用户ID';

INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES('', 1, '是否隐藏前台网址中的index.php文件名', 'hiden_index_php', '0', 'radio', '0|显示\r\n1|隐藏', 1, '', '如果空间不支持,就选择显示,不然前台页面会无法打开', 0, 0);