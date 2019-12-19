DROP TABLE IF EXISTS `qb_chatmod`;
CREATE TABLE IF NOT EXISTS `qb_chatmod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(7) NOT NULL COMMENT '用户UID',
  `aid` int(7) NOT NULL COMMENT '圈子id',
  `type` tinyint(1) NOT NULL COMMENT '0都可以使用,1只能群聊使用,2只能私聊使用',
  `name` varchar(50) NOT NULL COMMENT '功能名称',
  `about` varchar(256) NOT NULL COMMENT '描述',
  `icon` varchar(80) NOT NULL COMMENT '图标,若为空,就不显示按钮',
  `pcwap` tinyint(1) NOT NULL COMMENT '0都可以使用,1只能WAP使用,2只能PC使用,3只能APP使用',
  `keywords` varchar(50) NOT NULL COMMENT '关键字,标志符',
  `init_jsfile` varchar(150) NOT NULL COMMENT '初始化加载脚本路径',
  `init_iframe` varchar(150) NOT NULL COMMENT '初始化加载框架网页路径',
  `init_jscode` text NOT NULL COMMENT '可执行脚本',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1启用,0禁用',
  `list` int(10) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='群聊功能模块' AUTO_INCREMENT=1 ;


INSERT INTO `qb_chatmod` (`id`, `uid`, `aid`, `type`, `name`, `about`, `icon`, `pcwap`, `keywords`, `init_jsfile`, `init_iframe`, `init_jscode`, `status`, `list`) VALUES(0, 0, 0, 0, '表情', '', 'fa fa-smile-o', 2, 'qqface', '/public/static/libs/bui/pages/hack/qqface.js', '', '', 1, 0);
INSERT INTO `qb_chatmod` (`id`, `uid`, `aid`, `type`, `name`, `about`, `icon`, `pcwap`, `keywords`, `init_jsfile`, `init_iframe`, `init_jscode`, `status`, `list`) VALUES(0, 0, 0, 1, '上传图片', '', 'fa fa-photo', 2, 'uploadpic', '/public/static/libs/bui/pages/uploadpic/pc_init.js', '', '', 1, 0);
INSERT INTO `qb_chatmod` (`id`, `uid`, `aid`, `type`, `name`, `about`, `icon`, `pcwap`, `keywords`, `init_jsfile`, `init_iframe`, `init_jscode`, `status`, `list`) VALUES(0, 0, 0, 1, '上传图片', '', 'fa fa-photo', 1, 'uploadpic', '/public/static/libs/bui/pages/uploadpic/wap_init.js', '', '', 1, 0);
INSERT INTO `qb_chatmod` (`id`, `uid`, `aid`, `type`, `name`, `about`, `icon`, `pcwap`, `keywords`, `init_jsfile`, `init_iframe`, `init_jscode`, `status`, `list`) VALUES(0, 0, 0, 0, '指定用户会话', '', 'fa fa-user-plus', 2, 'send_member', '/public/static/libs/bui/pages/hack/send_member.js', '', '', 1, 0);
INSERT INTO `qb_chatmod` (`id`, `uid`, `aid`, `type`, `name`, `about`, `icon`, `pcwap`, `keywords`, `init_jsfile`, `init_iframe`, `init_jscode`, `status`, `list`) VALUES(0, 0, 0, 1, '引用主题', '', 'fa fa-list-alt', 1, 'topic', '/public/static/libs/bui/pages/topic/init.js', '', '', 1, 0);
INSERT INTO `qb_chatmod` (`id`, `uid`, `aid`, `type`, `name`, `about`, `icon`, `pcwap`, `keywords`, `init_jsfile`, `init_iframe`, `init_jscode`, `status`, `list`) VALUES(0, 0, 0, 1, '视频直播', '', 'fa fa-video-camera', 0, 'zhibo', '/public/static/libs/bui/pages/zhibo/init.js', '', '', 1, 0);
INSERT INTO `qb_chatmod` (`id`, `uid`, `aid`, `type`, `name`, `about`, `icon`, `pcwap`, `keywords`, `init_jsfile`, `init_iframe`, `init_jscode`, `status`, `list`) VALUES(0, 0, 0, 1, '音频直播', '', 'glyphicon glyphicon-headphones', 0, 'vod_voice', '/public/static/libs/bui/pages/vod_voice/init.js', '', '', 1, 0);
INSERT INTO `qb_chatmod` (`id`, `uid`, `aid`, `type`, `name`, `about`, `icon`, `pcwap`, `keywords`, `init_jsfile`, `init_iframe`, `init_jscode`, `status`, `list`) VALUES(0, 0, 0, 1, '语音聊天', '', 'fa fa-volume-up', 1, 'sound', '/public/static/libs/bui/pages/sound/init.js', '', '', 1, 0);
INSERT INTO `qb_chatmod` (`id`, `uid`, `aid`, `type`, `name`, `about`, `icon`, `pcwap`, `keywords`, `init_jsfile`, `init_iframe`, `init_jscode`, `status`, `list`) VALUES(0, 0, 0, 1, '会员签到', '', '', 1, 'signin', '', '/public/static/libs/bui/pages/signin/pop.html', '', 1, 0);
INSERT INTO `qb_chatmod` (`id`, `uid`, `aid`, `type`, `name`, `about`, `icon`, `pcwap`, `keywords`, `init_jsfile`, `init_iframe`, `init_jscode`, `status`, `list`) VALUES(0, 0, 0, 1, '红包', '', 'si si-present', 0, 'hongbao', '/public/static/libs/bui/pages/hongbao/init.js', '', '', 1, 0);
INSERT INTO `qb_chatmod` (`id`, `uid`, `aid`, `type`, `name`, `about`, `icon`, `pcwap`, `keywords`, `init_jsfile`, `init_iframe`, `init_jscode`, `status`, `list`) VALUES(0, 0, 0, 1, '打赏圈主', '', 'fa fa-database', 1, 'givermb', '/public/static/libs/bui/pages/givermb/init.js', '', '', 1, 0);
INSERT INTO `qb_chatmod` (`id`, `uid`, `aid`, `type`, `name`, `about`, `icon`, `pcwap`, `keywords`, `init_jsfile`, `init_iframe`, `init_jscode`, `status`, `list`) VALUES(0, 0, 0, 0, '表情(手机版已固定图标)', '', '', 1, 'qqface', '/public/static/libs/bui/pages/hack/qqface.js', '', '', 1, 0);
INSERT INTO `qb_chatmod` (`id`, `uid`, `aid`, `type`, `name`, `about`, `icon`, `pcwap`, `keywords`, `init_jsfile`, `init_iframe`, `init_jscode`, `status`, `list`) VALUES(0, 0, 0, 0, 'wap右上角菜单', '', '', 1, 'wap_top_right_menu', '/public/static/libs/bui/pages/hack/wap_top_right_menu.js', '', '', 1, 0);

ALTER TABLE  `qb_chatmod` ADD  `allowgroup` VARCHAR( 256 ) NOT NULL COMMENT  '允许使用此模块的用户组,多个用逗号隔开';
