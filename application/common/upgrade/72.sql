DROP TABLE IF EXISTS `qb_cms_buyer`;
CREATE TABLE IF NOT EXISTS `qb_cms_buyer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(8) NOT NULL COMMENT '内容ID',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '购买时间',
  `uid` int(7) NOT NULL COMMENT '购买者UID',
  `author` int(7) NOT NULL COMMENT '创建者UID',
  `money` decimal(10,2) NOT NULL COMMENT '金额',
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`),
  KEY `uid` (`uid`),
  KEY `author` (`author`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='内容付费购买者' AUTO_INCREMENT=5 ;


ALTER TABLE  `qb_cms_content1` ADD  `price` DECIMAL( 10, 2 ) NOT NULL COMMENT  '收费阅读';
ALTER TABLE  `qb_cms_content2` ADD  `price` DECIMAL( 10, 2 ) NOT NULL COMMENT  '收费阅读';
ALTER TABLE  `qb_cms_content3` ADD  `price` DECIMAL( 10, 2 ) NOT NULL COMMENT  '收费阅读';

ALTER TABLE  `qb_cms_content1` ADD  `description` VARCHAR( 255 ) NOT NULL COMMENT  '简介(SEO描述)';
ALTER TABLE  `qb_cms_content2` ADD  `description` VARCHAR( 255 ) NOT NULL COMMENT  '简介(SEO描述)';
ALTER TABLE  `qb_cms_content3` ADD  `description` VARCHAR( 255 ) NOT NULL COMMENT  '简介(SEO描述)';
