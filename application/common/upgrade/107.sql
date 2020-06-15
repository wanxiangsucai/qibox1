ALTER TABLE  `qb_group` CHANGE  `level`  `level` VARCHAR( 256 ) NOT NULL DEFAULT  '0' COMMENT  '会员组升级所需积分或RMB,如果设置多种格式的话,可以这样“1=2,30=5”即1天只须2元,30天须5元,多个情况就用英文半角逗号隔开';
ALTER TABLE  `qb_group` ADD  `about` TEXT NOT NULL COMMENT  '权限相关介绍';
ALTER TABLE  `qb_grouplog` ADD  `daytime` MEDIUMINT( 7 ) NOT NULL COMMENT  '升级天数';
