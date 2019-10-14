DROP TABLE IF EXISTS `qb_shorturl`;
CREATE TABLE IF NOT EXISTS `qb_shorturl` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` tinyint(2) NOT NULL COMMENT '0是浏览器跳转使用，1是公众号关注码，2是小程序码',
  `url` varchar(255) NOT NULL COMMENT '原网址',
  `uid` int(7) NOT NULL COMMENT '操作的用户ID',
  PRIMARY KEY (`id`),
  KEY `type` (`type`,`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='短网址' AUTO_INCREMENT=1 ;
