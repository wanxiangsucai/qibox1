ALTER TABLE  `qb_groupcfg` ADD  `allowview` VARCHAR( 255 ) NOT NULL COMMENT  '允许浏览此字段的用户组';
ALTER TABLE  `qb_groupcfg` ADD  `ifmust` TINYINT( 1 ) NOT NULL COMMENT  '升级此用户组需要的必填字段';
ALTER TABLE  `qb_groupcfg` ADD  `forbid_edit` TINYINT( 1 ) NOT NULL COMMENT  '是否禁止修改';
ALTER TABLE  `qb_groupcfg` ADD  `nav` VARCHAR( 30 ) NOT NULL COMMENT  '表单分组名',ADD  `input_width` VARCHAR( 7 ) NOT NULL COMMENT  '表单宽度',ADD  `input_height` VARCHAR( 7 ) NOT NULL COMMENT  '表单高度',ADD  `match` VARCHAR( 255 ) NOT NULL COMMENT  '用户输入时的正则匹配',ADD  `css` VARCHAR( 30 ) NOT NULL COMMENT  '自定义css的类名';
ALTER TABLE  `qb_memberdata` CHANGE  `bday`  `bday` INT( 10 ) NOT NULL COMMENT  '出生日期';
ALTER TABLE  `qb_group` ADD  `daytime` SMALLINT( 5 ) NOT NULL COMMENT  '用户组有效期';

DROP TABLE IF EXISTS `qb_grouplog`;
CREATE TABLE IF NOT EXISTS `qb_grouplog` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `uid` int(7) NOT NULL COMMENT '用户UID',
  `gid` mediumint(5) NOT NULL COMMENT '用户申请提升的用户组',
  `status` tinyint(1) NOT NULL COMMENT '审核状态',
  `create_time` int(10) NOT NULL COMMENT '申请时间',
  `check_time` int(10) NOT NULL COMMENT '审核时间',
  `refuse_reason` varchar(255)  NOT NULL COMMENT '被拒绝审核的原因',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='申请升级用户组的申请表' AUTO_INCREMENT=1 ;