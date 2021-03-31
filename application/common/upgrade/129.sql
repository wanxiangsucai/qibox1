ALTER TABLE  `qb_rmb_infull` ADD  `wxapp_appid` VARCHAR( 25 ) NOT NULL COMMENT  '小程序API';
ALTER TABLE  `qb_memberdata` ADD  `subscribe_qun_wxapp` TINYINT( 1 ) NOT NULL COMMENT  '是否订阅圈子小程序消息';

