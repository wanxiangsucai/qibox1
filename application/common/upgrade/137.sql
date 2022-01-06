DROP TABLE IF EXISTS `qb_weixinnotice`;
CREATE TABLE `qb_weixinnotice` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(50) NOT NULL COMMENT '程序调用关键字',
  `type` tinyint(1) NOT NULL COMMENT '0是小程序订阅消息，1是公众号订阅消息，2是公众号模板消息',
  `template_id` varchar(50) NOT NULL COMMENT '消息ID',
  `title` varchar(255) NOT NULL COMMENT '标注说明',
  `data_field` text NOT NULL COMMENT '内容字段',
  `mp` smallint(5) NOT NULL COMMENT '归属插件id或频道id，插件用负数',
  `status` tinyint(1) NOT NULL COMMENT '0禁用，1启用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyword` (`keyword`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


INSERT INTO `qb_weixinnotice`  VALUES ('1','test1','2','HDSNuQ8KtsvcoUOxsDrcgUm4Zu22SWFhKppa7CagMIc','群发商品促销通知','[{\"title1\":\"活动标题\",\"title2\":\"title\",\"title3\":\"first\",\"title4\":\"\"},{\"title1\":\"活动内容\",\"title2\":\"content\",\"title3\":\"remark\",\"title4\":\"\"},{\"title1\":\"活动日期\",\"title2\":\"time\",\"title3\":\"keyword1\",\"title4\":\"\"}]','0','1');
INSERT INTO `qb_weixinnotice`  VALUES ('2','reply','2','Mww4pFW5fZmxluKVgGMCc9l_9XWA4iRGxupzHr2wAtM','贴子回复(意见反馈通知)','[{\"title1\":\"标题\",\"title2\":\"title\",\"title3\":\"first\",\"title4\":\"\"},{\"title1\":\"用户名\",\"title2\":\"username\",\"title3\":\"keyword1\",\"title4\":\"\"},{\"title1\":\"时间\",\"title2\":\"time\",\"title3\":\"keyword2\",\"title4\":\"\"},{\"title1\":\"附注\",\"title2\":\"about\",\"title3\":\"remark\",\"title4\":\"\"},{\"title1\":\"内容\",\"title2\":\"content\",\"title3\":\"\",\"title4\":\"\"},{\"title1\":\"频道\",\"title2\":\"modname\",\"title3\":\"\",\"title4\":\"\"}]','0','1');
INSERT INTO `qb_weixinnotice`  VALUES ('3','comment','2','Mww4pFW5fZmxluKVgGMCc9l_9XWA4iRGxupzHr2wAtM','评论回复(意见反馈通知)','[{\"title1\":\"标题\",\"title2\":\"title\",\"title3\":\"first\",\"title4\":\"\"},{\"title1\":\"用户名\",\"title2\":\"username\",\"title3\":\"keyword1\",\"title4\":\"\"},{\"title1\":\"时间\",\"title2\":\"time\",\"title3\":\"keyword2\",\"title4\":\"\"},{\"title1\":\"附注\",\"title2\":\"about\",\"title3\":\"remark\",\"title4\":\"\"},{\"title1\":\"内容\",\"title2\":\"content\",\"title3\":\"\",\"title4\":\"\"},{\"title1\":\"频道\",\"title2\":\"modname\",\"title3\":\"\",\"title4\":\"\"}]','0','1');
