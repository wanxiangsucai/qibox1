INSERT INTO `qb_chatmod` (`id`, `uid`, `aid`, `type`, `name`, `about`, `icon`, `pcwap`, `keywords`, `init_jsfile`, `init_iframe`, `init_jscode`, `status`, `list`, `allowgroup`) VALUES(0, 0, 0, 1, '视频点播', '', 'fa fa-fw fa-tv', 0, 'vod_mv', '/public/static/libs/bui/pages/vod_mv/init.js', '', '', 1, 0, '');

UPDATE `qb_config` SET c_descrip='没有自建直播服务器,就不要选择启用。<a href="http://help.php168.com/1459144" target="_blank">点击查看教程,如何搭建直播服务器</a>' WHERE  `c_key` =  'self_zhibo_server';
