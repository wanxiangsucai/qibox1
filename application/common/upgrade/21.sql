INSERT INTO `qb_hook` (`id`, `name`, `about`, `ifopen`, `list`) VALUES(0, 'admin_begin', '后台程序开始的接口', 1, 0);
INSERT INTO `qb_hook` (`id`, `name`, `about`, `ifopen`, `list`) VALUES(0, 'index_begin', '前台程序开始的接口', 1, 0);
INSERT INTO `qb_hook` (`id`, `name`, `about`, `ifopen`, `list`) VALUES(0, 'member_begin', '会员中心程序开始的接口', 1, 0);
INSERT INTO `qb_hook` (`id`, `name`, `about`, `ifopen`, `list`) VALUES(0, 'upload_driver', '上传驱动', 1, 0);
UPDATE  `qb_hook` SET  `name` =  'upload_attachment_begin' WHERE `name` ='upfile_begin';
UPDATE  `qb_hook` SET  `name` =  'upload_attachment_end' WHERE `name` ='upfile_end';