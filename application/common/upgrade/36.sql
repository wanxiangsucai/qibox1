ALTER TABLE  `qb_shop_field` ADD  `script` TEXT NOT NULL COMMENT  'JS脚本',ADD  `trigger` VARCHAR( 255 ) NOT NULL COMMENT  '选择某一项后,联动触发显示其它字段';
ALTER TABLE  `qb_cms_field` ADD  `script` TEXT NOT NULL COMMENT  'JS脚本',ADD  `trigger` VARCHAR( 255 ) NOT NULL COMMENT  '选择某一项后,联动触发显示其它字段';




DROP TABLE IF EXISTS `qb_signin_cfg`;
CREATE TABLE IF NOT EXISTS `qb_signin_cfg` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `uid` int(7) NOT NULL COMMENT '群主UID',
  `ext_sys` mediumint(5) NOT NULL COMMENT '系统ID',
  `ext_id` int(7) NOT NULL COMMENT '群ID',
  `day_money` varchar(100) COLLATE utf8_bin NOT NULL COMMENT '每天签到奖励多少积分,若设置为3,5,8则代表连续第二天为5,第三天为8',
  `limit_num` varchar(100) COLLATE utf8_bin NOT NULL COMMENT '限制每天前多少名签到才有奖,若设置9:00=10,14:30=10指定时刻后的前几名才有奖',
  `min_money` smallint(4) NOT NULL COMMENT '晚到的给予多少鼓励积分奖励',
  `url` varchar(150) COLLATE utf8_bin NOT NULL COMMENT '签到成功后跳转网址',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `ext_id` (`ext_id`,`ext_sys`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='圈主设定的签到规则' AUTO_INCREMENT=1 ;

ALTER TABLE  `qb_signin_member` ADD INDEX (  `ip` );


ALTER TABLE  `qb_bbs_field` ADD  `script` TEXT NOT NULL COMMENT  'JS脚本',ADD  `trigger` VARCHAR( 255 ) NOT NULL COMMENT  '选择某一项后,联动触发显示其它字段';
ALTER TABLE  `qb_qun_field` ADD  `script` TEXT NOT NULL COMMENT  'JS脚本',ADD  `trigger` VARCHAR( 255 ) NOT NULL COMMENT  '选择某一项后,联动触发显示其它字段';
ALTER TABLE  `qb_exam_field` ADD  `script` TEXT NOT NULL COMMENT  'JS脚本',ADD  `trigger` VARCHAR( 255 ) NOT NULL COMMENT  '选择某一项后,联动触发显示其它字段';
ALTER TABLE  `qb_fenlei_field` ADD  `script` TEXT NOT NULL COMMENT  'JS脚本',ADD  `trigger` VARCHAR( 255 ) NOT NULL COMMENT  '选择某一项后,联动触发显示其它字段';
ALTER TABLE  `qb_hy_field` ADD  `script` TEXT NOT NULL COMMENT  'JS脚本',ADD  `trigger` VARCHAR( 255 ) NOT NULL COMMENT  '选择某一项后,联动触发显示其它字段';
ALTER TABLE  `qb_party_field` ADD  `script` TEXT NOT NULL COMMENT  'JS脚本',ADD  `trigger` VARCHAR( 255 ) NOT NULL COMMENT  '选择某一项后,联动触发显示其它字段';
ALTER TABLE  `qb_miaosha_field` ADD  `script` TEXT NOT NULL COMMENT  'JS脚本',ADD  `trigger` VARCHAR( 255 ) NOT NULL COMMENT  '选择某一项后,联动触发显示其它字段';
ALTER TABLE  `qb_giftshop_field` ADD  `script` TEXT NOT NULL COMMENT  'JS脚本',ADD  `trigger` VARCHAR( 255 ) NOT NULL COMMENT  '选择某一项后,联动触发显示其它字段';
ALTER TABLE  `qb_booking_field` ADD  `script` TEXT NOT NULL COMMENT  'JS脚本',ADD  `trigger` VARCHAR( 255 ) NOT NULL COMMENT  '选择某一项后,联动触发显示其它字段';