DROP TABLE IF EXISTS qb_address;
CREATE TABLE `qb_address` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(7) NOT NULL COMMENT '用户UID',
  `user` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '联系人称呼',
  `sex` tinyint(1) NOT NULL COMMENT '性 别,1是男,2是女',
  `telphone` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '联系电话',
  `address` varchar(150) COLLATE utf8_bin NOT NULL COMMENT '收货地址',
  `title` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '备注:如家里还是公司',
  `often` tinyint(1) NOT NULL COMMENT '是否为常用联系方式',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户收货地址';


DROP TABLE IF EXISTS qb_admin_menu;
CREATE TABLE `qb_admin_menu` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `pid` mediumint(5) NOT NULL DEFAULT '0' COMMENT '父ID',
  `type` tinyint(1) NOT NULL COMMENT '0的话通用,1的话PC专用,2的话WAP专用',
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '链接名称',
  `url` varchar(150) NOT NULL DEFAULT '' COMMENT '链接地址',
  `target` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0本窗口打开,1新窗口打开',
  `ifshow` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1显示,0隐藏',
  `list` smallint(4) NOT NULL DEFAULT '0' COMMENT '排序值',
  `style` varchar(30) NOT NULL DEFAULT '' COMMENT 'CSS类名',
  `groupid` mediumint(5) NOT NULL COMMENT '归属哪个用户组',
  `tier` tinyint(1) NOT NULL COMMENT '是否跟前一个菜单并排一行',
  `icon` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='管理员常用菜单';


DROP TABLE IF EXISTS qb_alonepage;
CREATE TABLE `qb_alonepage` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `posttime` int(10) NOT NULL DEFAULT '0' COMMENT '创建日期',
  `template` varchar(255) NOT NULL DEFAULT '' COMMENT '模板',
  `filename` varchar(100) DEFAULT NULL COMMENT '静态文件名',
  `descrip` text NOT NULL COMMENT '分享描述',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '分享标题',
  `content` text NOT NULL COMMENT '内容',
  `view` int(7) NOT NULL DEFAULT '0' COMMENT '浏览量',
  `ifclose` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否禁用',
  `picurl` varchar(150) NOT NULL DEFAULT '' COMMENT '分享图片',
  `list` int(10) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='单篇文章独立页';


DROP TABLE IF EXISTS qb_area;
CREATE TABLE `qb_area` (
  `id` mediumint(7) unsigned NOT NULL AUTO_INCREMENT,
  `pid` mediumint(7) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `level` tinyint(2) NOT NULL DEFAULT '0' COMMENT '第几级',
  `list` int(10) NOT NULL DEFAULT '0' COMMENT '排序值',
  `logo` varchar(100) NOT NULL DEFAULT '' COMMENT '封面图',
  `content` text NOT NULL COMMENT '描述',
  PRIMARY KEY (`id`),
  KEY `fup` (`pid`),
  KEY `list` (`list`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS qb_attachment;
CREATE TABLE `qb_attachment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '文件名',
  `module` varchar(32) NOT NULL DEFAULT '' COMMENT '模块名，由哪个模块上传的',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径',
  `thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图路径',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '文件链接',
  `mime` varchar(128) NOT NULL DEFAULT '' COMMENT '文件mime类型',
  `ext` char(8) NOT NULL DEFAULT '' COMMENT '文件类型',
  `size` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT 'sha1 散列值',
  `driver` varchar(16) NOT NULL DEFAULT 'local' COMMENT '上传驱动',
  `download` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表';


DROP TABLE IF EXISTS qb_cms_category;
CREATE TABLE `qb_cms_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `list` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `list` (`list`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='辅栏目';


DROP TABLE IF EXISTS qb_cms_content;
CREATE TABLE `qb_cms_content` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `uid` int(8) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='内容索引表';


DROP TABLE IF EXISTS qb_cms_content1;
CREATE TABLE `qb_cms_content1` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `fid` mediumint(6) unsigned NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `title` varchar(256) NOT NULL DEFAULT '' COMMENT '标题',
  `ispic` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否带组图',
  `uid` mediumint(7) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `view` mediumint(7) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态：0未审 1已审 2推荐',
  `agree` mediumint(5) NOT NULL DEFAULT '0' COMMENT '点赞',
  `replynum` mediumint(5) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `list` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序值',
  `picurl` text NOT NULL COMMENT '封面图',
  `content` text NOT NULL COMMENT '文章内容',
  `province_id` mediumint(5) NOT NULL COMMENT '省会ID',
  `city_id` mediumint(5) NOT NULL COMMENT '城市ID',
  `zone_id` mediumint(5) NOT NULL COMMENT '县级市或所在区ID',
  `street_id` mediumint(5) NOT NULL COMMENT '乡镇或区域街道ID',
  `ext_sys` smallint(5) NOT NULL COMMENT '扩展字段,关联的系统',
  `ext_id` int(7) NOT NULL COMMENT '扩展字段,关联的ID',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `fid` (`fid`),
  KEY `view` (`view`),
  KEY `status` (`status`),
  KEY `list` (`list`),
  KEY `ispic` (`ispic`),
  KEY `province_id` (`province_id`),
  KEY `city_id` (`city_id`),
  KEY `ext_id` (`ext_id`,`ext_sys`),
  KEY `ext_id_2` (`ext_id`,`ext_sys`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='文章模型模型表';


DROP TABLE IF EXISTS qb_cms_content2;
CREATE TABLE `qb_cms_content2` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `fid` mediumint(7) unsigned NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `title` varchar(256) NOT NULL DEFAULT '' COMMENT '标题',
  `ispic` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否带组图',
  `picurl` text NOT NULL COMMENT '封面图',
  `uid` mediumint(7) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `view` mediumint(7) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态：0未审 1已审 2推荐',
  `agree` mediumint(5) NOT NULL DEFAULT '0' COMMENT '点赞',
  `replynum` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `content` text NOT NULL COMMENT '内容介绍',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `list` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序值',
  `pics` text NOT NULL COMMENT '组图',
  `province_id` mediumint(5) NOT NULL COMMENT '省会ID',
  `city_id` mediumint(5) NOT NULL COMMENT '城市ID',
  `zone_id` mediumint(5) NOT NULL COMMENT '县级市或所在区ID',
  `street_id` mediumint(5) NOT NULL COMMENT '乡镇或区域街道ID',
  `ext_sys` smallint(5) NOT NULL COMMENT '扩展字段,关联的系统',
  `ext_id` mediumint(7) NOT NULL COMMENT '扩展字段,关联的ID',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `fid` (`fid`),
  KEY `view` (`view`),
  KEY `status` (`status`),
  KEY `list` (`list`),
  KEY `ispic` (`ispic`),
  KEY `province_id` (`province_id`),
  KEY `city_id` (`city_id`),
  KEY `ext_id` (`ext_id`,`ext_sys`),
  KEY `ext_id_2` (`ext_id`,`ext_sys`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COMMENT='图片模型模型表';


DROP TABLE IF EXISTS qb_cms_content3;
CREATE TABLE `qb_cms_content3` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `fid` mediumint(7) unsigned NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `title` varchar(256) NOT NULL DEFAULT '' COMMENT '标题',
  `ispic` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否带组图',
  `picurl` text NOT NULL COMMENT '封面图',
  `uid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `view` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态：0未审 1已审 2推荐',
  `agree` mediumint(5) NOT NULL DEFAULT '0' COMMENT '点赞',
  `replynum` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `content` text NOT NULL COMMENT '内容介绍',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `list` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序值',
  `mv_url` varchar(128) NOT NULL COMMENT '视频地址',
  `province_id` mediumint(5) NOT NULL COMMENT '省会ID',
  `city_id` mediumint(5) NOT NULL COMMENT '城市ID',
  `zone_id` mediumint(5) NOT NULL COMMENT '县级市或所在区ID',
  `street_id` mediumint(5) NOT NULL COMMENT '乡镇或区域街道ID',
  `ext_sys` mediumint(5) NOT NULL COMMENT '扩展字段,关联的系统',
  `ext_id` int(8) NOT NULL COMMENT '扩展字段,供其它调用',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `fid` (`fid`),
  KEY `view` (`view`),
  KEY `status` (`status`),
  KEY `list` (`list`),
  KEY `ispic` (`ispic`),
  KEY `province_id` (`province_id`),
  KEY `city_id` (`city_id`),
  KEY `ext_id` (`ext_id`,`ext_sys`),
  KEY `ext_id_2` (`ext_id`,`ext_sys`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='视频模型模型表';


DROP TABLE IF EXISTS qb_cms_field;
CREATE TABLE `qb_cms_field` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '字段名称',
  `name` varchar(32) NOT NULL,
  `title` varchar(60) NOT NULL DEFAULT '' COMMENT '字段标题',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '字段类型',
  `field_type` varchar(128) NOT NULL DEFAULT '' COMMENT '字段定义',
  `value` text COMMENT '默认值',
  `options` text COMMENT '额外选项',
  `about` varchar(256) NOT NULL DEFAULT '' COMMENT '提示说明',
  `show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `mid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属模型id',
  `ajax_url` varchar(256) NOT NULL DEFAULT '' COMMENT '联动下拉框ajax地址',
  `next_items` varchar(256) NOT NULL DEFAULT '' COMMENT '联动下拉框的下级下拉框名，多个以逗号隔开',
  `param` varchar(32) NOT NULL DEFAULT '' COMMENT '联动下拉框请求参数名',
  `format` varchar(32) NOT NULL DEFAULT '' COMMENT '格式，用于格式文本',
  `table` varchar(32) NOT NULL DEFAULT '' COMMENT '表名，只用于快速联动类型',
  `level` tinyint(2) unsigned NOT NULL DEFAULT '2' COMMENT '联动级别，只用于快速联动类型',
  `key` varchar(32) NOT NULL DEFAULT '' COMMENT '键字段，只用于快速联动类型',
  `option` varchar(32) NOT NULL DEFAULT '' COMMENT '值字段，只用于快速联动类型',
  `pid` varchar(32) NOT NULL DEFAULT '' COMMENT '父级id字段，只用于快速联动类型',
  `list` int(10) NOT NULL DEFAULT '100' COMMENT '排序',
  `listshow` tinyint(1) NOT NULL COMMENT '是否在列表显示',
  `ifsearch` tinyint(1) NOT NULL COMMENT '是否作为搜索字段',
  `ifmust` tinyint(1) NOT NULL COMMENT '是否必填项',
  `nav` varchar(30) NOT NULL COMMENT '分组名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COMMENT='文档字段表';


DROP TABLE IF EXISTS qb_cms_info;
CREATE TABLE `qb_cms_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) NOT NULL COMMENT '内容ID',
  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '辅栏目ID',
  `list` int(10) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`id`),
  KEY `mid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='内容索引表';


DROP TABLE IF EXISTS qb_cms_module;
CREATE TABLE `qb_cms_module` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(32) NOT NULL DEFAULT '' COMMENT '区分符关键字',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '模型标题',
  `layout` varchar(50) NOT NULL COMMENT '模板路径',
  `icon` varchar(64) NOT NULL,
  `list` int(10) NOT NULL DEFAULT '100' COMMENT '排序',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='模型表';


DROP TABLE IF EXISTS qb_cms_sort;
CREATE TABLE `qb_cms_sort` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL,
  `mid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `name` varchar(50) NOT NULL,
  `list` int(10) NOT NULL,
  `logo` varchar(50) NOT NULL COMMENT '封面图',
  `template` varchar(255) NOT NULL COMMENT '模板',
  `allowpost` varchar(100) NOT NULL COMMENT '允许发布信息的用户组',
  `allowview` varchar(100) NOT NULL COMMENT '允许浏览内容的用户组',
  `seo_title` varchar(255) NOT NULL COMMENT 'SEO标题',
  `seo_keywords` varchar(255) NOT NULL COMMENT 'SEO关键字',
  `seo_description` varchar(255) NOT NULL COMMENT 'SEO描述',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `pid` (`pid`),
  KEY `list` (`list`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='主栏目表';


DROP TABLE IF EXISTS qb_comment_content;
CREATE TABLE `qb_comment_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL COMMENT '引用回复上级ID',
  `sysid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '插件或模块ID',
  `aid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模块的内容页ID',
  `ispic` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否带组图',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `agree` mediumint(7) unsigned NOT NULL DEFAULT '0' COMMENT '支持',
  `disagree` mediumint(7) NOT NULL COMMENT '反对',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态：比如审核与否',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `list` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序值',
  `picurl` text NOT NULL COMMENT '封面图',
  `mvurl` varchar(255) NOT NULL,
  `content` text NOT NULL COMMENT '文章内容',
  `reply` mediumint(4) NOT NULL COMMENT '回复数',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `list` (`list`),
  KEY `ispic` (`ispic`),
  KEY `sysid` (`sysid`),
  KEY `agree` (`agree`),
  KEY `aid` (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=utf8 COMMENT='评论内容表';


DROP TABLE IF EXISTS qb_config;
CREATE TABLE `qb_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` mediumint(6) NOT NULL COMMENT '分类分组ID,关联表config_group',
  `title` varchar(32) NOT NULL COMMENT '字段名称标题',
  `c_key` varchar(50) NOT NULL DEFAULT '' COMMENT '字段名',
  `c_value` text NOT NULL COMMENT '字段值',
  `form_type` varchar(16) NOT NULL COMMENT '字段表单类型',
  `options` varchar(256) NOT NULL COMMENT '字段参数 比如多选或单选要用到',
  `ifsys` tinyint(1) NOT NULL COMMENT '是否为全局变量',
  `htmlcode` text NOT NULL COMMENT 'html额外代码',
  `c_descrip` varchar(256) NOT NULL COMMENT '选项详细介绍描述',
  `list` int(10) NOT NULL COMMENT '排序值',
  `sys_id` mediumint(7) NOT NULL COMMENT '系统类型，正数是频道模块ID，负数是插件ID',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `list` (`list`),
  KEY `sys_type` (`sys_id`),
  KEY `c_key` (`c_key`)
) ENGINE=MyISAM AUTO_INCREMENT=645 DEFAULT CHARSET=utf8 COMMENT='系统参数配置';


DROP TABLE IF EXISTS qb_config_group;
CREATE TABLE `qb_config_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '分组名称',
  `list` int(11) NOT NULL COMMENT '排序值',
  `sys_id` mediumint(9) NOT NULL COMMENT '0为系统，正数为模块频道，负数为插件',
  `ifshow` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否在系统设置那里显示',
  `ifsys` tinyint(1) NOT NULL COMMENT '是否作为系统字段参数，主要是针对插件而言的',
  PRIMARY KEY (`id`),
  KEY `sys_type` (`sys_id`),
  KEY `ifsys` (`ifsys`),
  KEY `ifshow` (`ifshow`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='参数配置分组分类';


DROP TABLE IF EXISTS qb_group;
CREATE TABLE `qb_group` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0是会员组,1是系统组',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '用户组名称',
  `level` mediumint(7) NOT NULL DEFAULT '0' COMMENT '会员组升级所需积分',
  `powerdb` text NOT NULL COMMENT '前台权限',
  `allowadmin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否允许进进后台',
  `admindb` text COMMENT '后台权限',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS qb_hook;
CREATE TABLE `qb_hook` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `about` varchar(255) NOT NULL DEFAULT '' COMMENT '钩子描述',
  `ifopen` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `list` int(10) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='接口(钩子)列表';


DROP TABLE IF EXISTS qb_hook_plugin;
CREATE TABLE `qb_hook_plugin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hook_key` varchar(50) NOT NULL COMMENT '所归属的接口关键字',
  `plugin_key` varchar(50) NOT NULL COMMENT '所归属的插件关键字,也即目录名',
  `hook_class` varchar(80) NOT NULL COMMENT '钩子运行的类名',
  `about` varchar(255) NOT NULL COMMENT '此钩子插件能实现的功能描述',
  `ifopen` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `list` int(10) NOT NULL COMMENT '执行的先后顺序',
  `author` varchar(80) NOT NULL COMMENT '开发者',
  `author_url` varchar(120) NOT NULL COMMENT '开发者网站',
  `version` varchar(60) NOT NULL COMMENT '版本信息',
  `version_id` mediumint(7) NOT NULL COMMENT '云端对应的ID',
  PRIMARY KEY (`id`),
  KEY `hook_id` (`hook_key`),
  KEY `plugin_id` (`plugin_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='可供使用的接口钩子功能';


DROP TABLE IF EXISTS qb_label;
CREATE TABLE `qb_label` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自动增值ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '标签区分符',
  `pagename` varchar(32) NOT NULL COMMENT '标签所在模板文件',
  `class_cfg` varchar(100) NOT NULL COMMENT '获取标签数据的类名与方法',
  `cfg` text NOT NULL COMMENT '基础参数配置，比如显示哪些栏目，如何排序等等',
  `extend_cfg` text NOT NULL COMMENT '扩展配置，可以是纯代码、图片，也可以是模板数据',
  `type` varchar(25) NOT NULL COMMENT '调用类型，比如图片、代码等',
  `ifdata` tinyint(4) NOT NULL COMMENT '是否只要原始数据，不要直接输出',
  `hide` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `cache_time` int(10) NOT NULL DEFAULT '0' COMMENT '缓存时间',
  `uid` mediumint(7) NOT NULL DEFAULT '0' COMMENT '修改者',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '标签创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `system_id` mediumint(6) NOT NULL DEFAULT '0' COMMENT '所属模块，插件为负数',
  `if_js` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否站外调用,比如APP或小程序或JS',
  `power_cfg` varchar(255) NOT NULL COMMENT '权限扩展',
  `view_tpl` text NOT NULL COMMENT '标签的模板代码',
  `fid` mediumint(5) NOT NULL COMMENT '分类分组',
  `title` varchar(50) NOT NULL COMMENT '标签名称说明',
  `list` int(10) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `module_id` (`system_id`),
  KEY `page` (`pagename`),
  KEY `if_js` (`if_js`),
  KEY `fid` (`fid`,`list`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='数据标签';


DROP TABLE IF EXISTS qb_log_action;
CREATE TABLE `qb_log_action` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL COMMENT 'ԃۧUID',
  `create_time` int(10) NOT NULL COMMENT 'ʱݤ',
  `ip` varchar(15) COLLATE utf8_bin NOT NULL COMMENT 'әطIP',
  `model` varchar(20) COLLATE utf8_bin NOT NULL COMMENT 'ģࠩ',
  `controller` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '࠘׆Ƿ',
  `action` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '׽ר',
  `plugin` varchar(60) COLLATE utf8_bin NOT NULL COMMENT 'ӥݾРژәط',
  `content` text COLLATE utf8_bin NOT NULL COMMENT '͡ݻքŚɝ',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`create_time`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='۳̨әطɕ־';


DROP TABLE IF EXISTS qb_log_login;
CREATE TABLE `qb_log_login` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '`эìΪ1ˇ۳̨և¼ɕ־',
  `ip` varchar(15) COLLATE utf8_bin NOT NULL COMMENT 'և¼IP',
  `create_time` int(10) NOT NULL COMMENT 'և¼ʱݤ',
  `username` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'և¼ԃۧĻ',
  `password` varchar(32) COLLATE utf8_bin NOT NULL COMMENT 'և¼Ĝë',
  PRIMARY KEY (`id`),
  KEY `create_time` (`create_time`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='ԃۧև¼ɕ־';


DROP TABLE IF EXISTS qb_market;
CREATE TABLE `qb_market` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL COMMENT '类型:比如admin_style index_style member_style等等',
  `keywords` varchar(50) NOT NULL COMMENT '关键字',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '安装日期',
  `author` varchar(80) NOT NULL COMMENT '开发者',
  `author_url` varchar(120) NOT NULL COMMENT '开发者网站',
  `version` varchar(60) NOT NULL COMMENT '版本信息',
  `version_id` mediumint(7) NOT NULL DEFAULT '0' COMMENT '云端对应的ID',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `keywords` (`keywords`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='云端市场购买的应用，主要是做升级核对，但频道、插件、钩子不在这个表，目前主要是风格，后续可以拓展更多的';


DROP TABLE IF EXISTS qb_memberdata;
CREATE TABLE `qb_memberdata` (
  `uid` mediumint(7) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `password_rand` varchar(10) NOT NULL COMMENT '密码混淆加密字串',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '会员帐号ID',
  `nickname` varchar(80) NOT NULL COMMENT '用户昵称',
  `qq_api` varchar(32) NOT NULL DEFAULT '' COMMENT 'QQ登录接口',
  `weixin_api` varchar(32) NOT NULL DEFAULT '' COMMENT '微信登录接口',
  `wxapp_api` varchar(32) NOT NULL COMMENT '微信小程序登录接口',
  `groupid` smallint(4) NOT NULL DEFAULT '0' COMMENT '会员用户组ID',
  `grouptype` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户组扩展字段',
  `groups` varchar(255) NOT NULL DEFAULT '' COMMENT '多用户组,扩展用',
  `yz` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否审核',
  `money` mediumint(7) unsigned NOT NULL DEFAULT '0' COMMENT '会员积分数',
  `oltime` int(10) NOT NULL DEFAULT '0' COMMENT '在线时长',
  `lastvist` int(10) NOT NULL DEFAULT '0' COMMENT '会员最后一次访问时间',
  `lastip` varchar(15) NOT NULL DEFAULT '' COMMENT '会员最后一次访问IP',
  `regdate` int(10) NOT NULL DEFAULT '0' COMMENT '注册日期',
  `regip` varchar(15) NOT NULL DEFAULT '' COMMENT '注册IP',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别',
  `bday` date NOT NULL DEFAULT '0000-00-00' COMMENT '出生日期',
  `icon` varchar(150) NOT NULL DEFAULT '' COMMENT '会员头像',
  `introduce` varchar(255) NOT NULL COMMENT '签名或自我介绍',
  `qq` varchar(11) NOT NULL DEFAULT '' COMMENT 'QQ号码',
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '邮箱帐号',
  `provinceid` mediumint(6) NOT NULL DEFAULT '0' COMMENT '省份ID',
  `cityid` mediumint(7) NOT NULL DEFAULT '0' COMMENT '城市ID',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '会员联系方式',
  `mobphone` varchar(12) NOT NULL DEFAULT '' COMMENT '联系手机',
  `idcard` varchar(20) NOT NULL DEFAULT '' COMMENT '身份证号码',
  `truename` varchar(20) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `config` text NOT NULL COMMENT '扩展字段',
  `email_yz` tinyint(1) NOT NULL DEFAULT '0' COMMENT '邮箱是否验证',
  `mob_yz` tinyint(1) NOT NULL DEFAULT '0' COMMENT '手机号码是否验证',
  `idcard_yz` tinyint(1) NOT NULL DEFAULT '0' COMMENT '身份证是否验证',
  `rmb` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '可用余额',
  `rmb_freeze` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '冻结余额',
  `rmb_pwd` varchar(32) NOT NULL DEFAULT '' COMMENT '支付密码',
  `introducer_1` mediumint(7) NOT NULL DEFAULT '0' COMMENT '1级推荐人',
  `introducer_2` mediumint(7) NOT NULL DEFAULT '0' COMMENT '2级推荐人',
  `introducer_3` mediumint(7) NOT NULL DEFAULT '0' COMMENT '3级推荐人',
  `wx_attention` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否关注微信',
  `sendmsg` text NOT NULL COMMENT '不接收系统哪些消息',
  PRIMARY KEY (`uid`),
  KEY `groups` (`groups`),
  KEY `sex` (`sex`,`bday`,`cityid`),
  KEY `qq_api` (`qq_api`),
  KEY `username` (`username`),
  KEY `weixin_api` (`weixin_api`),
  KEY `lastvist` (`lastvist`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS qb_module;
CREATE TABLE `qb_module` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `author` varchar(50) NOT NULL COMMENT '开发者',
  `author_url` varchar(100) NOT NULL COMMENT '开发者网站或演示网址',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '当前频道是否可以复制',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '频道名称',
  `keywords` varchar(30) NOT NULL DEFAULT '' COMMENT '目录名也即关键字',
  `domain` varchar(100) NOT NULL DEFAULT '' COMMENT '频道使用的二级域名',
  `config` text NOT NULL COMMENT '扩展配置',
  `list` mediumint(5) NOT NULL DEFAULT '0' COMMENT '排序值',
  `admingroup` varchar(150) NOT NULL DEFAULT '',
  `adminmember` text NOT NULL,
  `ifopen` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `ifsys` tinyint(1) NOT NULL DEFAULT '0',
  `about` text NOT NULL COMMENT '介绍',
  `version` varchar(60) NOT NULL COMMENT '版本信息',
  `icon` varchar(64) NOT NULL COMMENT '图标',
  `version_id` mediumint(7) NOT NULL COMMENT '对应官方的APP应用ID,升级用来核对',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='频道模块列表';


DROP TABLE IF EXISTS qb_moneycard;
CREATE TABLE `qb_moneycard` (
  `id` mediumint(7) NOT NULL AUTO_INCREMENT,
  `passwd` varchar(32) NOT NULL DEFAULT '',
  `moneyrmb` int(7) NOT NULL DEFAULT '0',
  `moneycard` int(7) NOT NULL DEFAULT '0',
  `ifsell` tinyint(1) NOT NULL DEFAULT '0',
  `uid` mediumint(7) NOT NULL DEFAULT '0',
  `username` varchar(32) NOT NULL DEFAULT '',
  `posttime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='点卡';


DROP TABLE IF EXISTS qb_moneylog;
CREATE TABLE `qb_moneylog` (
  `id` mediumint(7) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(7) NOT NULL DEFAULT '0',
  `money` mediumint(7) NOT NULL DEFAULT '0',
  `about` varchar(255) NOT NULL DEFAULT '',
  `posttime` int(10) NOT NULL DEFAULT '0',
  `city_id` mediumint(7) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `city_id` (`city_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='积分日志';


DROP TABLE IF EXISTS qb_msg;
CREATE TABLE `qb_msg` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sysmsg_id` mediumint(7) NOT NULL COMMENT '系统群发消息的内容ID,系统消息的话,就没必要每个用户重复插入标题与内容',
  `touid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '接收者的UID',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '发送者的UID',
  `ifread` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已读',
  `title` varchar(130) NOT NULL DEFAULT '' COMMENT '消息标题',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送时间',
  `content` text NOT NULL COMMENT '详情',
  PRIMARY KEY (`id`),
  KEY `fromuid` (`uid`),
  KEY `touid` (`touid`,`ifread`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='站内短消息';


DROP TABLE IF EXISTS qb_plugin;
CREATE TABLE `qb_plugin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL COMMENT '是否可复制',
  `keywords` varchar(32) NOT NULL DEFAULT '' COMMENT '目录名也即关键字',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '插件名称',
  `icon` varchar(64) NOT NULL DEFAULT '' COMMENT '图标',
  `about` text NOT NULL COMMENT '插件描述',
  `author` varchar(32) NOT NULL DEFAULT '' COMMENT '开发者',
  `author_url` varchar(255) NOT NULL DEFAULT '' COMMENT '开发者网站或演示网址',
  `config` text NOT NULL COMMENT '配置信息',
  `version` varchar(60) NOT NULL DEFAULT '' COMMENT '版本信息',
  `admin` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台管理',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `list` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  `ifopen` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `ifsys` tinyint(1) NOT NULL COMMENT '是否是系统插件不可删除与复制',
  `version_id` mediumint(7) NOT NULL COMMENT '对应官方的APP应用ID,升级用来核对',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='插件列表';


DROP TABLE IF EXISTS qb_regnum;
CREATE TABLE `qb_regnum` (
  `sid` varchar(8) NOT NULL DEFAULT '',
  `num` varchar(6) NOT NULL DEFAULT '',
  `posttime` int(10) NOT NULL DEFAULT '0',
  UNIQUE KEY `sid` (`sid`),
  KEY `posttime` (`num`,`posttime`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COMMENT='手机注册码';


DROP TABLE IF EXISTS qb_rmb_consume;
CREATE TABLE `qb_rmb_consume` (
  `id` mediumint(7) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(7) NOT NULL DEFAULT '0',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `about` varchar(255) NOT NULL DEFAULT '',
  `posttime` int(10) NOT NULL DEFAULT '0',
  `freeze` tinyint(1) NOT NULL DEFAULT '0',
  `fx` tinyint(2) NOT NULL,
  `shopid` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `fx` (`fx`),
  KEY `shopid` (`shopid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS qb_rmb_getout;
CREATE TABLE `qb_rmb_getout` (
  `id` mediumint(7) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(7) NOT NULL DEFAULT '0',
  `username` varchar(30) NOT NULL DEFAULT '',
  `money` float NOT NULL DEFAULT '0',
  `posttime` int(10) NOT NULL DEFAULT '0',
  `banktype` varchar(20) NOT NULL DEFAULT '',
  `bankname` varchar(100) NOT NULL DEFAULT '',
  `ifpay` tinyint(1) NOT NULL DEFAULT '0',
  `why` varchar(255) NOT NULL DEFAULT '',
  `truename` varchar(30) NOT NULL DEFAULT '',
  `tel` varchar(20) NOT NULL DEFAULT '',
  `quitabout` text NOT NULL,
  `admin` varchar(30) NOT NULL DEFAULT '',
  `replytime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `ifpay` (`ifpay`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS qb_rmb_infull;
CREATE TABLE `qb_rmb_infull` (
  `id` mediumint(7) NOT NULL AUTO_INCREMENT,
  `numcode` varchar(32) NOT NULL DEFAULT '' COMMENT '订单号',
  `money` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '支付金额',
  `ifpay` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已付款',
  `posttime` int(10) NOT NULL DEFAULT '0' COMMENT '订单提交时间',
  `uid` mediumint(7) NOT NULL DEFAULT '0' COMMENT '用户的UID',
  `banktype` varchar(15) NOT NULL DEFAULT '' COMMENT '付款方式',
  `paytime` varchar(20) NOT NULL DEFAULT '' COMMENT '支付时间',
  `callback_class` varchar(80) NOT NULL COMMENT '支付成功后 后台执行的类',
  PRIMARY KEY (`id`),
  KEY `numcode` (`numcode`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='人民币充值';


DROP TABLE IF EXISTS qb_scanlogin;
CREATE TABLE `qb_scanlogin` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(7) NOT NULL COMMENT '登录成功的用户ID',
  `sid` varchar(32) COLLATE utf8_bin NOT NULL COMMENT '用户生成的随机字串',
  `ip` varchar(15) COLLATE utf8_bin NOT NULL COMMENT '安全起见同一IP才能登录',
  `posttime` int(10) NOT NULL COMMENT '时间有效期',
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='微信扫码PC登录';


DROP TABLE IF EXISTS qb_shop_car;
CREATE TABLE `qb_shop_car` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增值',
  `shopid` int(10) NOT NULL COMMENT '商品ID',
  `type1` tinyint(2) NOT NULL COMMENT '商品属性1',
  `type2` tinyint(2) NOT NULL COMMENT '商品属性2',
  `type3` tinyint(2) NOT NULL COMMENT '商品属性3',
  `num` mediumint(5) NOT NULL COMMENT '购买数量',
  `create_time` int(10) NOT NULL COMMENT '时间',
  `update_time` int(10) NOT NULL,
  `ifchoose` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否钩选要购买',
  `uid` mediumint(7) NOT NULL COMMENT '用户的UID',
  PRIMARY KEY (`id`),
  KEY `shopid` (`shopid`,`uid`),
  KEY `uid` (`uid`,`update_time`,`ifchoose`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='购物车';


DROP TABLE IF EXISTS qb_shop_content;
CREATE TABLE `qb_shop_content` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `uid` int(8) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='内容索引表';


DROP TABLE IF EXISTS qb_shop_content1;
CREATE TABLE `qb_shop_content1` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `fid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `title` varchar(256) NOT NULL DEFAULT '' COMMENT '标题',
  `ispic` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否带组图',
  `uid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `view` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态：0未审 1已审 2推荐',
  `replynum` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `list` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序值',
  `picurl` text NOT NULL COMMENT '封面图',
  `content` text NOT NULL COMMENT '文章内容',
  `price` decimal(10,2) unsigned NOT NULL COMMENT '商品价格',
  `province_id` mediumint(5) NOT NULL COMMENT '省会ID',
  `city_id` mediumint(5) NOT NULL COMMENT '城市ID',
  `zone_id` mediumint(5) NOT NULL COMMENT '县级市或所在区ID',
  `street_id` mediumint(5) NOT NULL COMMENT '乡镇或区域街道ID',
  `ext_sys` smallint(5) NOT NULL COMMENT '扩展字段,关联的系统',
  `type1` varchar(255) NOT NULL COMMENT '商品属性1',
  `type2` varchar(255) NOT NULL COMMENT '商品属性2',
  `type3` varchar(255) NOT NULL COMMENT '商品属性3',
  `ext_id` int(8) NOT NULL COMMENT '扩展字段,供其它调用',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `fid` (`fid`),
  KEY `view` (`view`),
  KEY `comment` (`replynum`),
  KEY `status` (`status`),
  KEY `list` (`list`),
  KEY `ispic` (`ispic`),
  KEY `province_id` (`province_id`),
  KEY `city_id` (`city_id`),
  KEY `ext_id` (`ext_id`,`ext_sys`),
  KEY `ext_id_2` (`ext_id`,`ext_sys`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='商品内容表';


DROP TABLE IF EXISTS qb_shop_field;
CREATE TABLE `qb_shop_field` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '字段名称',
  `name` varchar(32) NOT NULL,
  `title` varchar(60) NOT NULL DEFAULT '' COMMENT '字段标题',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '字段类型',
  `field_type` varchar(128) NOT NULL DEFAULT '' COMMENT '字段定义',
  `value` text COMMENT '默认值',
  `options` text COMMENT '额外选项',
  `about` varchar(256) NOT NULL DEFAULT '' COMMENT '提示说明',
  `show` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `mid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属模型id',
  `ajax_url` varchar(256) NOT NULL DEFAULT '' COMMENT '联动下拉框ajax地址',
  `next_items` varchar(256) NOT NULL DEFAULT '' COMMENT '联动下拉框的下级下拉框名，多个以逗号隔开',
  `param` varchar(32) NOT NULL DEFAULT '' COMMENT '联动下拉框请求参数名',
  `format` varchar(32) NOT NULL DEFAULT '' COMMENT '格式，用于格式文本',
  `table` varchar(32) NOT NULL DEFAULT '' COMMENT '表名，只用于快速联动类型',
  `level` tinyint(2) unsigned NOT NULL DEFAULT '2' COMMENT '联动级别，只用于快速联动类型',
  `key` varchar(32) NOT NULL DEFAULT '' COMMENT '键字段，只用于快速联动类型',
  `option` varchar(32) NOT NULL DEFAULT '' COMMENT '值字段，只用于快速联动类型',
  `pid` varchar(32) NOT NULL DEFAULT '' COMMENT '父级id字段，只用于快速联动类型',
  `list` int(10) NOT NULL DEFAULT '100' COMMENT '排序',
  `listshow` tinyint(1) NOT NULL COMMENT '是否在列表显示',
  `ifsearch` tinyint(1) NOT NULL COMMENT '是否作为搜索字段',
  `ifmust` tinyint(1) NOT NULL COMMENT '是否必填项',
  `nav` varchar(30) NOT NULL COMMENT '分组名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COMMENT='文档字段表';


DROP TABLE IF EXISTS qb_shop_module;
CREATE TABLE `qb_shop_module` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(32) NOT NULL DEFAULT '' COMMENT '区分符关键字',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '模型标题',
  `layout` varchar(50) NOT NULL COMMENT '模板路径',
  `icon` varchar(64) NOT NULL,
  `list` int(10) NOT NULL DEFAULT '100' COMMENT '排序',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='模型表';


DROP TABLE IF EXISTS qb_shop_order;
CREATE TABLE `qb_shop_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单id',
  `order_sn` varchar(20) NOT NULL DEFAULT '' COMMENT '订单编号',
  `shop` varchar(255) NOT NULL COMMENT '购买的商品,存放格式如下:shopid-num-type1-type2-type3 商品ID,购买数量,商品属性1、2、3,多个商品用,号隔开',
  `shop_uid` mediumint(7) NOT NULL COMMENT '店主的UID',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '下单客户的uid',
  `totalmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单总金额',
  `shipping_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '邮费',
  `pay_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际付款金额',
  `user_rmb` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '使用余额',
  `user_jf` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '使用积分',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下单时间',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态',
  `pay_name` varchar(120) NOT NULL DEFAULT '' COMMENT '付款方式',
  `linkman` varchar(60) NOT NULL DEFAULT '' COMMENT '收货人',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '收货地址',
  `telphone` varchar(60) NOT NULL DEFAULT '' COMMENT '手机',
  `shipping_time` int(11) DEFAULT '0' COMMENT '发货时间',
  `receive_time` int(10) DEFAULT '0' COMMENT '收货时间',
  `receive_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '收货状态',
  `shipping_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发货状态',
  `shipping_name` varchar(120) NOT NULL DEFAULT '' COMMENT '物流名称',
  `shipping_code` varchar(32) NOT NULL DEFAULT '' COMMENT '物流单号',
  `user_note` varchar(255) NOT NULL DEFAULT '' COMMENT '用户备注',
  `admin_note` varchar(255) DEFAULT '' COMMENT '管理员备注',
  PRIMARY KEY (`id`),
  KEY `create_time` (`create_time`),
  KEY `uid` (`uid`),
  KEY `order_sn` (`order_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品订单';


DROP TABLE IF EXISTS qb_shop_sort;
CREATE TABLE `qb_shop_sort` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL,
  `mid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `name` varchar(50) NOT NULL,
  `list` int(10) NOT NULL,
  `logo` varchar(50) NOT NULL COMMENT '封面图',
  `template` varchar(255) NOT NULL COMMENT '模板',
  `allowpost` varchar(100) NOT NULL COMMENT '允许发布信息的用户组',
  `allowview` varchar(100) NOT NULL COMMENT '允许浏览内容的用户组',
  `seo_title` varchar(255) NOT NULL COMMENT 'SEO标题',
  `seo_keywords` varchar(255) NOT NULL COMMENT 'SEO关键字',
  `seo_description` varchar(255) NOT NULL COMMENT 'SEO描述',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `pid` (`pid`),
  KEY `list` (`list`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='主栏目表';


DROP TABLE IF EXISTS qb_webmenu;
CREATE TABLE `qb_webmenu` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `pid` mediumint(5) NOT NULL DEFAULT '0' COMMENT '父ID',
  `type` tinyint(1) NOT NULL COMMENT '0的话通用,1的话PC专用,2的话WAP专用',
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '链接名称',
  `url` varchar(150) NOT NULL DEFAULT '' COMMENT '链接地址',
  `target` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0本窗口打开,1新窗口打开',
  `ifshow` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1显示,0隐藏',
  `list` smallint(4) NOT NULL DEFAULT '0' COMMENT '排序值',
  `style` varchar(30) NOT NULL DEFAULT '' COMMENT 'CSS类名',
  `activate` varchar(20) NOT NULL COMMENT '频道等于这个值就代表当前在该链接下',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='网站导航菜单';


DROP TABLE IF EXISTS qb_weixinmenu;
CREATE TABLE `qb_weixinmenu` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `uid` int(7) NOT NULL DEFAULT '0',
  `fid` mediumint(5) NOT NULL DEFAULT '0',
  `name` varchar(80) NOT NULL DEFAULT '',
  `keyword` varchar(255) NOT NULL DEFAULT '',
  `linkurl` varchar(150) NOT NULL DEFAULT '',
  `type` tinyint(2) NOT NULL DEFAULT '0',
  `hide` tinyint(1) NOT NULL DEFAULT '0',
  `list` smallint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS qb_weixinmsg;
CREATE TABLE `qb_weixinmsg` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `fid` int(10) NOT NULL DEFAULT '0',
  `appid` varchar(32) NOT NULL DEFAULT '',
  `uid` int(7) NOT NULL DEFAULT '0',
  `username` varchar(50) NOT NULL DEFAULT '',
  `posttime` int(10) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `url` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS qb_weixinword;
CREATE TABLE `qb_weixinword` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ask` varchar(150) NOT NULL DEFAULT '',
  `answer` text NOT NULL,
  `list` int(10) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `list` (`list`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS qb_weixinyznum;
CREATE TABLE `qb_weixinyznum` (
  `sid` varchar(16) NOT NULL DEFAULT '',
  `username` varchar(50) NOT NULL DEFAULT '',
  `num` varchar(6) NOT NULL DEFAULT '',
  `posttime` int(10) NOT NULL DEFAULT '0',
  `wx_id` varchar(50) NOT NULL DEFAULT '',
  UNIQUE KEY `sid` (`sid`),
  KEY `username` (`username`,`num`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;


INSERT INTO `qb_admin_menu` VALUES ('1','0','0','基础功能设置','','0','1','100','','3','0','fa fa-fw fa-gear');
INSERT INTO `qb_admin_menu` VALUES ('2','1','0','网站参数设置','/admin.php/admin/setting/index.html','0','1','0','','3','0','');
INSERT INTO `qb_admin_menu` VALUES ('3','1','0','网站菜单设置','/admin.php/admin/webmenu/index.html','0','1','0','','3','0','');
INSERT INTO `qb_admin_menu` VALUES ('4','0','0','用户与权限设置','','0','1','0','','3','0','fa fa-fw fa-user');
INSERT INTO `qb_admin_menu` VALUES ('5','4','0','用户资料管理','/admin.php/admin/member/index.html','0','1','0','','3','0','');
INSERT INTO `qb_admin_menu` VALUES ('6','0','0','应用市场','','0','1','0','','3','0','fa fa-fw fa-skyatlas');
INSERT INTO `qb_admin_menu` VALUES ('7','6','0','频道应用市场','/admin.php/admin/module/market.html','0','1','0','','3','0','');
INSERT INTO `qb_admin_menu` VALUES ('8','6','0','插件应用市场','/admin.php/admin/plugin/market.html','0','1','0','','3','0','');
INSERT INTO `qb_admin_menu` VALUES ('9','6','0','钩子接口应用市场','/admin.php/admin/hook_plugin/market.html','0','1','0','','3','0','');
INSERT INTO `qb_admin_menu` VALUES ('10','6','0','风格市场','/admin.php/admin/style/market.html','0','1','0','','3','0','');
INSERT INTO `qb_alonepage` VALUES ('8','欢迎关注官方微信','欢迎关注公众号','1447747983','','do/weixin.htm','','','<p style=\"text-align:center;\"><br/></p><p>亲爱的会员，</p><p>&nbsp; 本站会员微信官方账号现已开通，您可以通过“扫一扫”功能或查找公众号中输入“齐博软件”即可添加。最新、最热的活动资讯将第一时间通知到您！</p><p><br/></p><p style=\"text-align:center;\"><img src=\"http://bbs.qibosoft.com/attachment123456br666vh00/Day_140402/10312_243423_d93579d4987ee55.jpg\" width=\"430\" height=\"430\"/><br/></p>','28','0','','0');
INSERT INTO `qb_area` VALUES ('1','0','广东省','1','0','','');
INSERT INTO `qb_area` VALUES ('2','0','湖南省','1','0','','');
INSERT INTO `qb_area` VALUES ('6','1','广州市','2','0','','');
INSERT INTO `qb_area` VALUES ('7','6','天河区','3','0','','');
INSERT INTO `qb_area` VALUES ('8','6','荔湾区','3','0','','');
INSERT INTO `qb_area` VALUES ('9','6','增城区','3','0','','');
INSERT INTO `qb_area` VALUES ('10','6','花都区','3','0','','');
INSERT INTO `qb_area` VALUES ('11','6','番禺区','3','0','','');
INSERT INTO `qb_area` VALUES ('12','6','白云区','3','0','','');
INSERT INTO `qb_area` VALUES ('13','6','黄埔区','3','0','','');
INSERT INTO `qb_area` VALUES ('14','6','海珠区','3','0','','');
INSERT INTO `qb_area` VALUES ('15','6','越秀区','3','0','','');
INSERT INTO `qb_area` VALUES ('16','6','从化区','3','0','','');
INSERT INTO `qb_area` VALUES ('17','7','体育中心','4','0','','');
INSERT INTO `qb_area` VALUES ('18','7','天河北','4','0','','');
INSERT INTO `qb_area` VALUES ('19','7','广州东站','4','0','','');
INSERT INTO `qb_area` VALUES ('20','7','天河南','4','0','','');
INSERT INTO `qb_area` VALUES ('21','7','粤垦','4','0','','');
INSERT INTO `qb_area` VALUES ('22','7','岗顶','4','0','','');
INSERT INTO `qb_area` VALUES ('23','7','沙河','4','0','','');
INSERT INTO `qb_area` VALUES ('24','7','五山','4','0','','');
INSERT INTO `qb_area` VALUES ('25','7','天河公园','4','0','','');
INSERT INTO `qb_area` VALUES ('26','7','珠江新城','4','0','','');
INSERT INTO `qb_area` VALUES ('27','7','跑马场','4','0','','');
INSERT INTO `qb_area` VALUES ('28','7','员村','4','0','','');
INSERT INTO `qb_area` VALUES ('29','7','棠下','4','0','','');
INSERT INTO `qb_area` VALUES ('30','7','黄村','4','0','','');
INSERT INTO `qb_area` VALUES ('31','7','东圃','4','0','','');
INSERT INTO `qb_area` VALUES ('32','7','车陂','4','0','','');
INSERT INTO `qb_area` VALUES ('33','7','林和','4','0','','');
INSERT INTO `qb_area` VALUES ('4','0','直辖市','1','0','','');
INSERT INTO `qb_area` VALUES ('5','0','浙江省','1','0','','');
INSERT INTO `qb_area` VALUES ('3','0','山东省','1','0','','');
INSERT INTO `qb_cms_content` VALUES ('12','1','1');
INSERT INTO `qb_cms_content` VALUES ('13','1','1');
INSERT INTO `qb_cms_content` VALUES ('14','1','1');
INSERT INTO `qb_cms_content` VALUES ('15','1','1');
INSERT INTO `qb_cms_content` VALUES ('16','1','1');
INSERT INTO `qb_cms_content` VALUES ('17','1','1');
INSERT INTO `qb_cms_content` VALUES ('18','2','1');
INSERT INTO `qb_cms_content` VALUES ('19','2','1');
INSERT INTO `qb_cms_content1` VALUES ('12','1','10','曾是阿里旗下的猛将，马云挥泪斩“马谡”，将他送入五年牢狱','1','1','35','1','0','0','1517533498','1519974088','0','uploads/images/20180302/acc5222477f61f1791a5f1c3e1029a33.jpg','<p style=\"text-align: left;\">细数中国商业史，在某个时刻被命运抛入深谷，或一夜之间锒铛入狱者不在少数，但能经历成功——失败——再成功三部曲者寥寥无几。马云曾梦想着阿里“猛将如云,美女如潮”,但人多了最难管。阿里巴巴是个大江湖，马云按员工入职先后定工牌编号。阎利珉和他带领的聚划算员工，都是80后新人，这些员工是整个阿里巴巴集团和旗下子公司最拼命、最辛苦、最具有创新能力的一群人。</p><p style=\"text-align:center\"><img src=\"http://p1.pstatp.com/large/46de0005a4f54342df06\" alt=\"曾是阿里旗下的猛将，马云挥泪斩“马谡”，将他送入五年牢狱\"/></p><p>阎利珉2002年毕业于四川大学，拥有八年的互联网软件和电子商务的从业经验，2006年加入阿里巴巴。2009年其所在业务板块并入淘宝，其后便在阿里实现了”火箭式上升”，主要依靠的是两大业绩：一是推动了淘江湖的建立，第二发起了聚划算项目，这两个项目都被阿里巴巴内部评为&quot;金旺旺奖&quot;。</p><p>阎利珉在加入阿里巴巴之后，一开始是做产品经理，后来转做运营，这种大跨度的转型也让他的能力越来越全面。阿里内部人士评价称，阿里能出阎利珉这样的人不多，真正能做事拿结果的创新型专业人才。</p><p><img src=\"http://p3.pstatp.com/large/46e000055cb666bbaaf9\" alt=\"曾是阿里旗下的猛将，马云挥泪斩“马谡”，将他送入五年牢狱\"/></p><p>自聚划算平台成立后担任平台总监，2011年10月聚划算从淘宝独立出来后，阎利珉出任聚划算总经理。<br/></p><p>背靠淘宝系资源，聚划算发展迅猛。根据团800在2013年6月份的统计数据，聚划算（本地单）成交额6733万元，聚划算（商品单）成交额1.64亿元。业内人士表示，对于参加聚划算平台活动的淘宝网店主来说，能够进入聚划算平台亮相，意味着巨大的流量和购买量的保证。</p><p>但随着聚划算业务的迅速扩张，团队的管理和制度的规范却相对滞后。自2011年年中起，有关聚划算在招商过程中存在不规范、甚至有小二谋取不当利益的举报就接连出现。</p><p><img src=\"http://p1.pstatp.com/large/46e30002f441db0b1009\" alt=\"曾是阿里旗下的猛将，马云挥泪斩“马谡”，将他送入五年牢狱\"/></p><p>阿里巴巴方面介绍，在调查过程中发现，聚划算在制度规范和团队管理上存在很大漏洞，是导致出现上述情况的重要原因。作为聚划算的负责人，阎利珉负有重要管理责任，必须对此负责。阿里巴巴对聚划算“放养”式的管理，带来业绩腾飞，也成为滋生腐败的温床。成为王被捧，败为寇受辱。阎利珉和其团队成了风箱中的老鼠。</p><p>阎利珉爱帮忙，凡有求于他，尽量满足，但不能满足的心生抱怨。聚划算团队中，员工也摸准他的脾气，即便背后和商家达成隐秘利益交换，也有办法通过说法、正常的工作流程让这位年轻老板“批准”。</p><p><img src=\"http://p3.pstatp.com/large/46e1000548e3fea75179\" alt=\"曾是阿里旗下的猛将，马云挥泪斩“马谡”，将他送入五年牢狱\"/></p><p>他先是被阿里巴巴集团宣布免去了总经理职务，后因涉嫌非国家工作人员受贿罪被杭州警方刑事拘留，再后来就是一纸有期徒刑7年的判决书。现如今，曾经风光无限的聚划算早已被并入天猫，而已经走出人生最低谷的阎利珉正准备重新出发，不管怎样，重启不易，祝福之。</p><p><br/></p>','0','0','0','0','0','0');
INSERT INTO `qb_cms_content1` VALUES ('13','1','12','死在公司倒闭之前的茅侃侃，和一批从高处坠落的明星创业者们','0','1','12','1','0','0','1517533576','1517566429','0','','<p style=\"text-align: left;\">茅侃侃之死</p><p style=\"text-align: left;\">2006年，四个80后创业者登上了罗永浩主持的《对话》节目。央视舞台上的聚光灯让他们一夜爆红。名气随着搜索率一路飙升，让茅侃侃，李想、高燃、戴志康这四个二十出头的男孩兴奋不已。</p><p style=\"text-align: left;\">二十一世纪初，那是个充满可能性的时期，非典之后，万物开始复苏。大家还不认识什么马化腾，张朝阳，年度经纪人物榜上挂着的还是郭广昌，王宪章。</p><p style=\"text-align: left;\">彼时，茅侃侃创立的MaJoy，头顶真人CS业内第一的帽子一路开挂，轰轰烈烈朝着融资的方向驶去；李想的汽车之家，刚挤进国内汽车网站访问量前五名；高燃的P2P流媒体平台Mysee赶上了互联网视频元年的快车，顺风顺水；戴志康的“Discuz！”成为了国内领先的网络社区软件。</p><p style=\"text-align:center\"><img src=\"http://p1.pstatp.com/large/5b54000271990c0ae1b5\" alt=\"死在公司倒闭之前的茅侃侃，和一批从高处坠落的明星创业者们\"/></p><p>茅侃侃</p><p>那时候的茅侃侃是媒体的重点关注对象，媒体一边大肆强调他的初中文凭，一边赞扬着他的才华，把这场不易的创业之旅标榜为一场华丽的逆袭。</p><p>谁也想不到，2018的开年，茅侃侃三个字再次出现在热搜和头条里，带来的却是他煤气自杀的消息。</p><p><img src=\"http://p1.pstatp.com/large/5b5700000b3005f20359\" alt=\"死在公司倒闭之前的茅侃侃，和一批从高处坠落的明星创业者们\"/></p><p>回头再去观望当初被冠以“京城IT四少”的他们：李想带着汽车之家，在2013年12月，敲响了纽交所的大钟，如今已是上亿身价；高燃被免去公司CEO之为之后，MySee也于不久后倒闭。戴志康的“Discuz！”于2010年成功变现，但在把“儿子”交付腾讯之后，老戴曾三个月未曾出门，轻度抑郁。这些曾被捧为80后明星的创业者们，大多正在以各种方式回归平淡。</p><p>茅侃侃是不愿倒下的那个，2013年，创业十年之际，茅侃侃选择了出任GTV（游戏竞技频道）的副总裁，2015年，茅侃侃出任其和万家文化成立的合资公司，万家电竞的CEO。31岁的这年，他希望还能搏一把。但正是这一把，把他带入了无尽的深渊。</p><p>2017，赵薇收购万家文化的闹剧结束，万家电竞成为了牺牲品之一，同年8月，万家文化卖身祥源集团，祥源不愿意接受万家电竞背后的6000万债务。茅侃侃开始了抵押房车的生活。10月，万家电竞员工因为工资拖欠，集体去了朝阳区劳动局进行劳动仲裁。</p><p>2017年的最后一天，茅侃侃发了一条朋友圈：2017失去了所有的所有。这些所有，不仅代表了创业路上的困境，也代表了资本圈的无法控制的风险。他到那个时候都没有放弃努力，付出一切代价苦苦支撑。</p><p>但仅仅一个多月之后，就传来了他自杀的消息。</p><p>有人说他不堪资金压力放弃挣扎，也有人言之凿凿说他被股权交易的黑手玩弄致死，但我想不管怎样，压死骆驼的最后一根稻草，应该是他最终认命凭一己之力真的无法挽救一腔心血的那刻。</p><p>于无声处，茅侃侃带着遗憾归入创业大军溃退的洪流之中，留下一片唏嘘和惋惜。</p><p>被捧上天又摔的无比惨的明星创业者们</p><p>茅侃侃之死，之所以获得那么多人关注，很大原因是因为，他曾经被媒体捧得很高，他的成功失败都在放大镜下一览无遗。茅侃侃不是唯一一个被捧上创业天才宝座的人，也不是唯一一个从聚光灯处摔落的人。有太多被或主动或被动打上追光灯的创业者，在逆流而行的路途中死于船漏，死于巨浪，最终湮灭于路人目送中。</p><p>前些时间，小蓝被叫停，留下一地鸡毛。最终，小蓝单车被滴滴接管。小蓝单车曾是被开启复用最多的共享单车，一度超过膜拜和OFO，如今却只能依附于滴滴发展。但这对于小蓝单车创始人李刚来说，这场创业，从某种程度上来说，到底还是失败了。</p><p><img src=\"http://p3.pstatp.com/large/5b53000289ddf233db1e\" alt=\"死在公司倒闭之前的茅侃侃，和一批从高处坠落的明星创业者们\"/></p><p>小蓝单车创始人：李刚</p><p>我想对于创业者来说，最痛苦的不是倒在血泊中，而是你曾在鲜花掌声中意气风发，如今却只能臣服于强者。李刚在滴滴托管小蓝单车之后坦言：那时候压力很大，可我没有勇气走那最坏的一条路，小蓝单车危机中，我看到了人性中善良的一面，我选择相信希望。</p><p>李刚是幸运的，他没有拧开煤气罐，事情也没有糟到一发不可收拾。</p><p>比起80后，被资本偏爱的90后，创业之路要顺得多。如徐小平一类的投资人，希望通过投资强劲的新鲜血液，获得主流网民的支持。创业大潮如火如荼，方兴未艾，投资人的支持，加上媒体的吹捧，资本开始疯狂押注90后创业者，甚至过分在乎他们的影响力而忽视创业产品和商业模式，这为这一批创业者的覆灭埋下了伏笔。</p><p>2016年，90后创业者尹桑宣布公司解散。尹桑曾是90后创业大军的代表者。他曾赴美读书，又被宾利商学院全奖录取，先前的几次创业也都以成功收尾。2012年，他选择大二辍学回国开发APP“一起唱”，企图颠覆线下KTV。“一起唱”先后获得IDG三次融资，曾入选福布斯。那时的尹桑也被封为90后高材生创业典范，被无数人看好，但没几年，尹桑就遗憾宣布公司因投资人延缓投资，现金流告急，不得不面临员工遣散公司倒闭的结局。</p><p><img src=\"http://p3.pstatp.com/large/5b550001e81040450cfe\" alt=\"死在公司倒闭之前的茅侃侃，和一批从高处坠落的明星创业者们\"/></p><p>尹桑</p><p>不同于茅侃侃的努力无果戛然而止，尹桑的结束倒更像是一场明哲保身触地溜走的滑稽戏。在尹桑发布声明之后，公司员工在知乎上将尹桑推上了风口浪尖。员工集体指责尹桑满篇情怀，丝毫不提员工补偿和工资的裁员公告信，质问尹桑为何不在年前召集员工宣布该消息而是通过邮件遣散员工，更对尹桑解散和禁言员工群的行为十分不满。</p><p>一个匿名的用户po出了尹桑在Facebook上计划年后夏威夷旅游的动态，以及尹桑在发完遣散信还在德州扑克牌桌上的消息。他称自己与尹桑相熟，但并不确认他是否是真的没钱了。</p><p>发布公告信没几天，公司人事给员工发了正式邮件，希望员工主动离职。主动离职信一写，意味着第一员工拿不到N+1的补偿金，第二也拿不到失业保险，无法劳动仲裁。比起茅侃侃变卖家产补齐员工工资，尹桑冷冰冰的一纸算计，于一起创业打拼的团队心里，恐颇有卸磨杀驴的寒心之感。</p><p>尹桑的退场，真的算不上是一场体面的全身而退。</p><p>同样不算体面而退的创业者，还有很多。1990年出生的美女学霸马佳佳，是90后创业者中的绝对话题人物。她毕业于中国传媒大学，传言还是云南省高考语文状元，她是《非诚勿扰》女嘉宾，中欧商学院创业演讲者，两会期间媒体代言人，徐小平曾评价她说，她身上“有中国企业家缺乏的直击心灵的力量”。但也有人指责她逻辑混乱，只会炒作，商业天赋不高。</p><p>太多的光环加身于这个胆大出格的女生身上，她带着情趣用品商店“泡否”在一路非议中横冲直撞，霸占了某一时期的创业板头条。而仅仅2年过去，热度消失的马佳佳，被人发现人气店“泡否”已经倒闭，泡否科技融资状态也在2014年年初再没变化过。</p><p><img src=\"http://p3.pstatp.com/large/5b550001e8d669dc8e90\" alt=\"死在公司倒闭之前的茅侃侃，和一批从高处坠落的明星创业者们\"/></p><p>马佳佳</p><p>2017年10月，网易上线“春风”情趣用品品牌时，曾邀请过马佳佳参与直播。再次回到公众视线的马佳佳微博简介已经变成了”少女实验室“品牌的创始人，她不承认自己的创业之路已断，但没有资本临幸，仅靠标榜“性、美女、成人用品、营销天才”光环的话题女王马佳佳，鲜有媒体愿意再给与她关注度。</p><p>除了马佳佳，被捧杀高坠的还有王凯歆。或许时至今日，很多人都忘了这个名字，但提起神奇百货，还是有人会发出“噢~”的一声，恍然大悟的同时报以轻蔑一笑。王凯歆，1998年出生，创业时候才15岁，17岁就获得天使轮投资，18岁拿到千万A轮，真的是件让人很骄傲的事情。但这个最初做做小生意起家的孩子，在面临千万投资和嗷嗷待哺的团队时，还是显现出了致命的缺陷：经验不足且品质堪忧。</p><p>拿到千万投资的那一年，神奇百货因为盲目扩张、大幅增员，现金流告急。投资人在这个时候发现了王凯歆公司存在数据造假的问题，这还不是最重要的，最致命的是，王凯歆拿着投资人的钱请保姆，买奢侈品，还威胁员工保密，种种心思无一花在公司经营之上。最终，王凯歆被爆出侵吞公款600万，投资人忍无可忍，将其列入死亡名单之中。</p><p>2017年7月，王凯歆被指其新做的保健品微商项目涉嫌诈骗，一天之内，其公众号被迅速封杀。8月，她还为“华茵”健康饮品宣传，但8月之后，再未有过创业相关的消息。</p><p>小小年纪的王凯歆，算是近几年创业者中，谢幕姿势最难看的一位了。</p><p>和生命赛跑的创业之路</p><p>创业之路是残酷的，账上没钱是每个创业者的噩梦。无论是刚刚离去的茅侃侃，还是以上90后中的谁谁谁，这批曾被媒体推上风口浪尖的创业明星们，承受着不同他人的压力，这是省不去的代价。</p><p>在80后创业者身上，我看到了更多的是谨慎和稳重，中产的压力逼着他们无法停下脚步，“被收割”的恐惧支撑着他们必须上岸的决心，他们的试错成本太高了。自杀和猝死的新闻不断传来，但从来没成为他们止步不前的理由。在茅侃侃去世之前，厦门维信科技董事长黄国斌因故去世，时年32岁，重庆游戏界元老，手游开发公司老总冒朝华突然脑溢血，时年37岁。</p><p>在90后创业者身上，我看到更多的是勇气和张扬，以及怎么都要折腾到最后一秒的执着，这不是贬义的评价。无论是因为自身能力不足停止发展也好，负隅顽抗，坚守未遂也好，他们都曾是一个优秀而光荣的创业者，曾在高处享受过注目礼的成功者。</p><p>创业是一场征程，生在这最好也是最坏的时代，如果因为怕有朝一日的高坠，而放弃登高，那才真是比失败落幕还要让人不屑的愚蠢。</p><p><br/></p>','0','0','0','0','0','0');
INSERT INTO `qb_cms_content1` VALUES ('14','1','12','茅侃侃昨日在家中自杀 前合作伙伴：太遗憾','0','1','17','1','0','0','1517533674','1517566412','0','uploads/images/20180302/acc5222477f61f1791a5f1c3e1029a33.jpg','<p style=\"text-align: left;\">法制晚报·看法新闻（记者 张蕊 李阳煜）今日上午，有媒体曝出<a href=\"http://money.163.com/keywords/0/3/00380030540e/1.html\" title=\"80后\" target=\"_blank\">80后</a><a href=\"http://money.163.com/keywords/5/1/521b4e1a/1.html\" title=\"创业\" target=\"_blank\">创业</a>代表人物<a href=\"http://money.163.com/keywords/8/0/83054f834f83/1.html\" title=\"茅侃侃\" target=\"_blank\">茅侃侃</a><a href=\"http://money.163.com/keywords/8/e/81ea6740/1.html\" title=\"自杀\" target=\"_blank\">自杀</a>身亡。法制晚报·看法新闻记者经核实，茅侃侃自杀一事属实。</p><p style=\"text-align:center\"><img alt=\"茅侃侃昨日在家中自杀 前合作伙伴：太遗憾\" src=\"http://cms-bucket.nosdn.127.net/catchpic/c/ca/ca88a9b263f1746f96ccaa058bb135b0.jpg?imageView&thumbnail=550x0\"/></p><p>茅侃侃资料图</p><p>据跟茅侃侃公司有交集的人士称，茅侃侃的公司近期遇到很大的经营困难。法制晚报·看法新闻记者采访了一位茅侃侃曾经的合伙人，据其介绍，茅侃侃有时会为了开发“找灵感”而短期失联，对于其自杀身亡，“遗憾，他还是太年轻了”。</p><p>在多家公司担任法人、董事 其中一家公司员工证实自杀</p><p>茅侃侃出生于1983年，被称为80后创业代表人物，曾被媒体和大众狂热追捧。2004年底，茅侃侃开始运营MaJoy，他把网络游戏搬到线下、模仿其后台数据运行，但用实景、由玩家实际扮演。此后，茅侃侃又先后做了移动医疗领域的APP以及提供实时路况信息APP“哪儿堵”；2013年，茅侃侃加入GTV，踏入电竞圈；2015年，茅侃侃与万家文化成立合资公司万家电竞，并出任CEO。</p><p>法制晚报·看法新闻记者查询发现，茅侃侃目前仍是北京万好万家<a href=\"http://money.163.com/keywords/7/3/75355b507ade6280/1.html\" title=\"电子竞技\" target=\"_blank\">电子竞技</a>传媒有限公司和海南万好万家电子竞技传媒有限公司的法人，同时也是多家公司的股东、董事。</p><p>今天上午，法制晚报·看法新闻记者致电其担任法人的北京万好万家电子竞技传媒有限公司和海南万好万家电子竞技传媒有限公司，电话均无人接听。</p><p>而在茅侃侃为董事的北京鸣鹤鸣和文化传媒有限公司中，公司工作人员向记者证实了茅侃侃的死讯。“在北京家中自杀，昨天的事情。”工作人员称，家人发现茅侃侃的时候，其已经去世，没有遗书。</p><p>对于茅侃侃自杀的原因，该人士称不知情。但记者从一名曾经和茅侃侃公司有关交集的相关人士处获悉，茅侃侃的公司经营困难，“前段时间和他们公司的员工聊天时，曾听对方说他们公司目前处于很困难的阶段。”但这究竟是不是导致茅侃侃自杀的原因，目前还没有答案。</p><p><img alt=\"茅侃侃昨日在家中自杀 前合作伙伴：太遗憾\" src=\"http://cms-bucket.nosdn.127.net/catchpic/c/c6/c6c06abc63db6aba53853bfe9d0ad243.jpg?imageView&thumbnail=550x0\"/></p><p>茅侃侃担任董事的一家公司，并没有员工上班，门口贴满小广告 摄/法制晚报·看法新闻记者 李阳煜</p><p>1月25日上午，记者来到位于朝阳区建国路北岸1292三间房创意生活园区的北京鸣鹤鸣和文化传媒有限公司，多次敲门无人应答。其邻居称，该栋楼内有公司也有住户，邻里之间很少交往，偶尔见到有人出入该公司所在处，但从未交流过。记者注意到，该楼层内大多数房间较小，部分用作单身公寓用户租住。</p><p>负责片区送货的某快递公司快递员称，每次给这家公司送快递都是一个女生接电话，“让把快递送到快递站，有人自己去取”。园区物业查询得知，公司所在房间面积有25平米，对于该公司是否租用此地办公，物业称没有去房间看过。</p><p>曾经的合作者：太遗憾，还是太年轻</p><p>10年前，Majoy的茅侃侃、泡泡网的李想、康盛创想的戴志康、Mysee的高燃，4个出生在80年代，20岁出头便有公司，成了当时80后年轻人的创业偶像。</p><p>这时候的茅侃侃应该正值青春年少，踌躇满志。</p><p>许昌（化名）是茅侃侃曾经的合作伙伴，2007年的一个晚上，他在一档电视节目中看到和李想坐在一起侃侃而谈的茅侃侃，“当时电视节目把他定性为怪才，奇才。”看着年纪不大，但一脸沉着的茅侃侃，许昌想，是不是可以和他一起合作开个公司呢？</p><p>第二天，许昌就开始托人找茅侃侃的联系方式，辗转找了很多人，终于联系到了茅侃侃。</p><p>第一次见茅侃侃，许昌觉得和电视上不太一样，“怎么说呢，电视中，他还是比较善谈的，但是私下里其实并不太喜欢说话。”许昌说自己当时想开发一款关于大学生就业指导的软件，就和茅侃侃聊了自己的想法。并说自己想和茅侃侃一起开公司来做这个事情，没想到茅侃侃很爽快的就同意了，“他当时就说，好啊。”</p><p>这让许昌多少有些意外，要知道当时的茅侃侃在互联网界还是挺有名的，接下来的一切就顺理成章了，许昌注册了公司，自己和茅侃侃都是股东，记者注意到，茅侃侃在这家公司的职位是总经理，占45%的股权，“他当时没有钱，注册公司都是我出的钱。”许昌告诉法制晚报·看法新闻记者说，之所以给茅侃侃股份，就是觉得有点儿股份，能让他踏实在公司呆着做事。</p><p>但之后的一切让许昌有些始料未及，按照许昌的说法，他主外，茅侃侃主内，“我当时都联系好了不少单位，就等软件开发出来就可以合作了，但东西迟迟出不了，我也着急。”许昌说，茅侃侃和他的合作最后只持续了一年多，“最后软件一直没开发出来，项目就不了了之了。”</p><p>在许昌的印象当中，茅侃侃是个比较随意的人，这不仅表现在穿着上，性格上、工作上亦是如此，“有时候，他要找灵感，就会一连十几天，二十天不出现，也联系不上。”第一次遇到这样的情况，许昌还紧张了很长时间，但后来，渐渐也就习惯了，许昌将这总结为性格所致。</p><p><br/></p><p><a target=\"_self\"></a></p><p>许昌告诉法制晚报·看法新闻记者，茅侃侃没有上过大学，是自学成才，这一度让许昌特别佩服，所以在当时开公司的时候，许昌也很少过问软件开发的进度，“我也不懂，偶尔问一次，他就说正在弄。”</p><p>那时候的许昌是相信茅侃侃的，因为他相信茅侃侃“怪才”、“奇才”的称谓，“他确实还是比较有才的，但是太年轻了。”</p><p>2009年，软件一直没有开发出来，许昌觉得两人无法继续合作下去了，“忘了当时什么情况，反正就是不联系了。”</p><p>这些年来，许昌没有再关注过茅侃侃，只是偶尔看到他的消息。</p><p>得知茅侃侃自杀的消息，许昌特别震惊，对此他觉得很遗憾，“还是太年轻了。”</p><p><br/></p>','0','0','0','0','0','0');
INSERT INTO `qb_cms_content1` VALUES ('15','1','10','茅侃侃之死：成年人的生活里没有容易二字','1','1','14','1','0','0','1517533725','1519974065','0','uploads/images/20180302/f4cb941062607e7ece08699e2955eaf3.jpg,uploads/images/20180302/d27a7a45c3fcd543a5a5dc4b1cb22d16.jpg','<p style=\"text-align: left;\">昨天，有一个80后的创业偶像自杀死了。说实话，作为一个90后，我真的没有听过他，倒是我那个80后的总监一下子就激动的从座位上面炸了起来，大嚎了一句：天啦，茅侃侃居然死了。</p><p style=\"text-align: left;\">出于好奇和尊重，我去百度了这个人的故事。他被誉为第一批 80 后创业者、京城 IT 四少之一。他从小学五年级开始玩电脑，14岁开始在《大众软件》等杂志发表数篇文章。</p><p style=\"text-align: left;\">高一辍学后，他把全部的精力投入到了计算机和英语的学习中，在两个月内的时间里，拿下微软MCP（微软认证专家）、MCSE（微软认证系统工程师）、MCDBA（微软认证数据库管理员）三项认证。</p><p style=\"text-align: left;\">当年在全亚洲18岁拿下三本证书的，只有两人。2006年，在换了一打工作后开始创业的茅侃侃，接受了《中国企业家》的采访，并登上封面，标题为《生于八零年代》。</p><p style=\"text-align: left;\">当时尚在央视工作的罗振宇深受触动，于是邀请了茅侃侃、李想、高燃、戴志康四位80后创业者参加央视的《对谈》节目。节目播出后，第二天，“茅侃侃”一词的搜索，从1000名外直线上升至81名。<br/></p><p style=\"text-align:center\"><img src=\"http://p1.pstatp.com/large/5b560001bab72d3272ff\" alt=\"茅侃侃之死：成年人的生活里没有容易二字\"/></p><p>在“京城IT四少”中，茅侃侃是最小的，他的个人经历和当时的普世价值，冲突也最大。他每月的花销也比其他3位似乎要多一些。“不多，两三万吧。”侃侃说得很轻松，那时他穿着时尚，烫卷的黑发，自曝爱好是逛夜店。</p><p>在网上的照片我看到了这样的一个形象：染着一头黄毛，加上两条大花臂，特别扎眼——看上去“很社会”，一点也不像是那个我想象中的创业家。</p><p>“他可是我中学时的偶像啊，居然那么年轻就自杀了。”我总监一脸的惋惜和无奈。</p><p>而我想起了另外一个人，前不久自杀的韩国艺人金钟铉。据说他是一个人跑到酒店，给姐姐发了条“我好累”的信息，随后点燃了准备好的炭，被人发现送到医院抢救无效后死亡，年仅27岁。而仅仅在十天前，他刚刚结束自己的个人演唱会。</p><p><img src=\"http://p3.pstatp.com/large/5b5000063314559c49e3\" alt=\"茅侃侃之死：成年人的生活里没有容易二字\"/></p><p>直到他的遗书曝光，在很多人意料之中，他的离开是长期遭受抑郁症的困扰。</p><p>在遗书里面有一段话格外让人心痛：</p><blockquote><p>“我从里面开始出了故障。</p><p>一点点啃噬着我的抑郁最终将我吞噬。</p><p>我无法战胜它。</p><p>我厌恶我自己。</p><p>断开的记忆抓住我，不管怎么对自己说要打起来精神来，也找不到答案。”</p></blockquote><p>他们，一个创业大神，一个韩国偶像，在我们普通人的眼里，都是神一样的存在，对我们来说，他们根本不用像我们一样天天思考着茶米油盐，有钱有名有人脉有前途。人生走到这个阶段，已经是完美了。</p><p>可是他们却选择了在三十而立的时间段，选择了离开。</p><p>如果是你，你真的有那样的勇气站上那个最高点吗？你一边在埋怨人生，一边在厌恶社会，可是你有没有想过，当你还年轻的时候，你的确有尝试一切的资本，但是当你不在年轻的时候，你还能做什么呢？</p><p><img src=\"http://p1.pstatp.com/large/5b53000278c1231becc5\" alt=\"茅侃侃之死：成年人的生活里没有容易二字\"/></p><p>我今年25岁，我刚毕业的时候，工资只有2500，在深圳这座城市，我住着500块的床位，和一群做清洁工的阿姨在一个房间，阿姨们每天晚上都很晚睡觉，呼噜声还打得像雷一样，我基本每天都睡不好。每天下班就去超市，买特价的面包，作为第二天的早餐，吃5快钱一份的炒粉。这不是电视剧，这就是我们的生活。</p><p>有一天我回宿舍的时候发现房间被锁住了，开了很久都打不开，后来房东来了，说我的床位到期了，有一个人下午来看房子就立刻租出去了。我拿着我的行李在门口呆住了，那一个瞬间我不知道该怎么办。我以为以前看的偶像剧那种狗血的剧情都是假的，没想到会发生在我自己的身上。</p><p>后来，我找了一个青旅，把身上所有的人都拿了出来，租了半个月。那天晚上，我一个人在回家的那条路上的一个天桥上面，哭了很久。我不知道能找谁，也不想找谁，一个人最彻底的崩溃，就是这样，悄无声息地，毫无生机地默默流泪。</p><p>这让我想起来，以前看过一个热门微博：“现代人的崩溃是一种默不作声的崩溃。看起来很正常，会说笑、会打闹、会社交，表面平静，实际上心里的糟心事已经积累到一定程度了。</p><p>不会摔门砸东西，不会流眼泪或歇斯底里。但可能某一秒突然就积累到极致了，也不说话，也不真的崩溃，也不太想活，也不敢去死。”</p><p>曾经有张动图流传很广，在日本地铁里，有一个男生，坐在那里啃着面包，强忍着委屈，眼泪似乎就要夺眶而出。谁也不知道他发生了什么，但那份心酸，每个人都理解。</p><p><img src=\"http://p1.pstatp.com/large/5b550001cff6a3d1bca6\" alt=\"茅侃侃之死：成年人的生活里没有容易二字\"/></p><p>谁身后都有一堆不可说的故事。但他的那身打扮，给他定位了一个体面的身份。这个身份，让他除了忍住不哭，毫无办法。</p><p>《这个杀手不太冷》里有一句特别著名的台词，马蒂尔德问：“生活是一直这么艰辛，还是只有童年如此。”里昂说：“一直如此。”</p><p><img src=\"http://p3.pstatp.com/large/5b560001bee30924499e\" alt=\"茅侃侃之死：成年人的生活里没有容易二字\"/></p><h1>每个人的生活，从来都是不容易的。</h1><p>三十而立，这将是一道坎。</p><p>这个时间点上，你的职业规划，和你前半生学到的技能，往往会决定着今后数十年一直到你退休那一天的职业方向。</p><p>这个年纪，大部分人都已结婚生子，都已承担起支撑整个家庭的责任。你职场中的每一个决定，都不会再像你年轻时那样轻狂。你的生活如履薄冰，战战兢兢。</p><p>你我都失败不起。</p><p><img src=\"http://p1.pstatp.com/large/5b54000263c2eacdbfc8\" alt=\"茅侃侃之死：成年人的生活里没有容易二字\"/></p><p>成年人的生活，没有容易二字。不容易在，你明明知道，真正的自己，早就被这些社会身份包装到被遗弃，甚至埋葬。但你却没有退路，没有第二个选择，你只能哭着爬着把那些被人寄予厚望的身份扮演下去。</p><p>可每个人，无论爬得多辛苦，演得多艰难，内心深处，还都会有一个微弱到快熄灭的声音，不停地拷问自己：生活的意义到底是什么？是身在红尘的体验，还是看破红尘的顿悟。山的那头，到底有什么？我们只有爬过去才知道。</p><p>我特别喜欢一首歌《What’ up》有句歌词：25年的人生就这样过去了，我仍要努力去翻越那希望的高山，为了让人生有意义。</p><p>我不想说，泥沼总会过去，星辰大海在向你招手。甚至，我都不觉得吃苦是有必要的。但是，找到生活的意义的时刻，恰恰是在，当你熬过去，撑下去后，可以用上帝视角去审视当年那个奋斗到呲牙列嘴的自己的时候。</p><p>人生就真的像爬山一样。山脚下的我们，就是小时候，天真无邪，井底之蛙。爬到半山腰才发现，体力费光，下山已经没有路，还恐高不敢回头看；往上爬，手脚并用都不见得能再挪一步。但是能爬过去的，就是那些，能挺住的人。</p><p>在压力面前，有些人能承受九分，但像我们普通人，光是活着就快花光了所有力气。</p><p>死者为大，愿茅侃侃安息，愿所有的你们都能过的好。</p><p><br/></p>','0','0','0','0','0','0');
INSERT INTO `qb_cms_content1` VALUES ('16','1','11','马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！','1','1','19','1','0','0','1517533788','1520411501','0','uploads/images/20180302/e04c0b963510abd7a6a75770ac73cfe5.jpg','<p>大家都知道，阿里巴巴“18罗汉”是阿里草创时的18位创始人，他们当时大都默默无名，跟着屌丝马云创业，一起把阿里做到了今天的辉煌。</p><p>蔡崇信，放下年薪70万美元的工作，跟着马云领月薪500，现在是阿里执行副主席；</p><p><img src=\"http://p1.pstatp.com/large/5e710003f37705c10d44\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>彭蕾，当年的HR，现在是蚂蚁金服董事长；</p><p><img src=\"http://p3.pstatp.com/large/5e6f00032791bd8a9271\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>童文红，当年的前台小妹，帮忙订咖啡、做客服，现在是阿里集团资深副总裁兼菜鸟首席运营官；</p><p><img src=\"http://p1.pstatp.com/large/5e710003f373496ee250\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>除此之外，还有孙彤宇、吴泳铭、盛一飞等人，后来也成为阿里集团的骨干。</p><p>然而，最近马云透露，原来当年那批创始人，曾经被开掉一个！</p><p>到底是谁被开掉了，为什么开掉的？这里面又看出马云什么样的用人哲学？</p><p><img src=\"http://p3.pstatp.com/large/5b5c0004c48c8ea342bb\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>“阿里只有一个人可以叽叽哇哇”</p><p>在湖畔大学上，马云曾经提过这个人，不说名字，只说他当时的表现：</p><p>他是马云从硅谷带回来的，人也聪明，技术也算不错，在美国待了七八年，他总喜欢叽叽歪歪，干不干就蔫了，问他有什么主意，他也说不出。</p><p>马云抱怨：</p><p>“这个人是我们当时最厉害的，又是18个创始人之一，但永远不做决定，永远叽叽歪歪。”</p><p><img src=\"http://p3.pstatp.com/large/5e710003f3740fcb9c21\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>后来有人开玩笑：阿里巴巴只能有一个人叽叽哇哇，那就是马云！</p><p>马云本来还想“拯救”他，有一天和他谈事情，一直和他谈到凌晨两点，终于说服他应该怎么办，马云才去睡觉。</p><p>谁知道三点钟不到，时任阿里COO关明生打来电话：“我已经把他开除了！”</p><p>马云惊呆了，我刚和他谈好，你就开除他了？关明生说：“这样对我们好，也对他好，没必要浪费时间。”</p><p><img src=\"http://p3.pstatp.com/large/5e700003274b9abeed11\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>视频在这里：</p><p>当时，马云还有点不理解，但后来一看，开除他实在太对了！</p><p>阿里“18罗汉”现在很出名，但只是阿里成功后大家编的高帽，当时这群创始人只是“乌合之众”。</p><p>吴晓波就曾很不客气地指出：</p><blockquote><p>“当时只要愿意跟着马云干，愿意每个月只领600块钱工资，就可以成为创始人。”</p></blockquote><p><img src=\"http://p1.pstatp.com/large/5b5a0005426bf9cd5eb6\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>所以，大家工作都不上心，老员工各自为政，能自己解决就自己解决；新员工来了也不知道干什么，就在工位上假装工作，甚至能假装一周。</p><p>当马云连最出名、官最大的那个创始人都开除了，其他人一下子打起精神了：“真的会开除啊？”</p><p>公司的氛围一下子积极起来，再加上公司架构的完善，阿里巴巴才总算有了一个公司的样子。</p><p><img src=\"http://p3.pstatp.com/large/5b590005541f701bf437\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>虽说这次开除是关明生主导的，但好好给马云上了一课：</p><p>合适的人就要放在合适的岗位，不合适就赶紧换，拖得越久越麻烦。</p><p>后来几次人员调动，都显示出了马云的用人手段。</p><p>2006年，上任仅40天的雅虎中国总经理谢文辞职，据报道，他为人过于强硬，没法融合雅虎中国内部的派系，所以只能离开；</p><p><img src=\"http://p3.pstatp.com/large/5e710003f372196c273b\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>谢文</p><p>2007年，阿里二号人物，淘宝创始人孙彤宇被宣布“离岗进修”，他是创业老臣，马云工号是“1”，他的工号是“2”，地位举足轻重。</p><p>据说，他是因为和马云对淘宝的未来的设想不合而离开。</p><p><img src=\"http://p1.pstatp.com/large/5b5c0004c48d6bc15a96\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>孙彤宇</p><p>2011年，阿里CEO卫哲、COO李旭晖辞职，原因是前一年阿里查出平台有0.8%的“中国供应商”客户涉嫌欺诈，这两人引咎辞职。</p><p>马云发邮件直言：“我们必须刮骨疗毒。”</p><p><img src=\"http://p3.pstatp.com/large/5e710003f375af6c7026\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>卫哲</p><p>后来，马云曾在电视节目上这么说：</p><p>“我曾经开除过阿里的创始人，课间有人问我，你们在大年三十开除人，的确是有点残忍，我说，开除人，要心好，要刀快。</p><p>有时候开除一个人，对他也是帮助。你离过婚，破过产，进过监狱，还能乐观的面对，这样的人一辈子就没有白活。”</p><p><img src=\"http://p3.pstatp.com/large/5b590005541e001c699b\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>马云的7大用人原则</p><p>老板们开公司，总爱感叹：千金易得，一将难求。</p><p>员工们找工作，总爱抱怨：怀才不遇，天赋埋没。</p><p>一边想要人才，一边想被赏识，两者之间的复杂关系可见一斑。</p><p>所谓用人，其实只是用在所长，避其所短。那么，作为中国最知名的企业，马云和阿里巴巴究竟是怎么用人的？</p><p>读过了与马云和阿里巴巴相关的大量资料，我们给你总结出七条：</p><p>1、人才不是招来的，而是培养出来的</p><p>上面也说了，阿里巴巴的创始人们，其实都是“乌合之众”，许多都是因为在市场上找不到工作，才被马云招到阿里巴巴。</p><p>但这样一群人，却为阿里巴巴打下了最初的根基，后来他们也在阿里集团身居高位。</p><p>这就是马云的理念：用人的最高境界就是提升人。</p><p>2002年互联网寒冬，阿里巴巴陷入绝境，但马云依然狠砸100万元，为员工办了两个培训班。</p><p><img src=\"http://p9.pstatp.com/large/5e710003f37650608f86\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>100万元，对当时的阿里来说是一笔不小的开支，但马云坚持：</p><p>“人是最关键的产品，所以，我们要在三年内锻炼我们的队伍。我们盼望着三年内培养出最优秀的互联网员工。”</p><p>马云不仅砸钱请人培训，还自己带着高管去讲课：他和关明生讲价值观、彭蕾讲阿里巴巴的历史、李旭晖和孙彤宇讲产品和销售技巧。</p><p>在这样的培训下，阿里巴巴才从游击队变成正规军，员工也跟着企业一起成长。</p><p>2、少招应届生，多招社会人</p><p>从2015年起，阿里取消了招聘应届毕业生的要求，每年最多不超过300人，这是阿里不同于其他任何大企业的用人法则。</p><p>马云觉得，应届毕业生初入职场，只是一张白纸，虽然标准化、容易管理，但3到5年就会成为管理者，15年后都会成为一样的人。</p><p>与此相比，阿里更喜欢社招，他们更有经验、更有想法，虽然不容易管理，不过碰撞就能出火花。</p><p><img src=\"http://p3.pstatp.com/large/5e6f000327933119ca57\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>3、永远不找最完美的人</p><p>马云曾说：</p><blockquote><p>“我永远不选最好的员工，只选最合适的员工。选最好的员工是个灾难。</p><p>我喜欢这样的人：他会说，1、I am a man （我是个普通人），我有缺点，但我想努力；2、我有梦想。我讨厌人说 This is a job ...（这只是工作），智商高的人情商一般都低。”</p></blockquote><p>因为员工不完美，你就能指导他、帮助他，让他的成长，带动公司的成长，实现双赢。</p><p>4、知人善用，合适的人放在合适的位置</p><p>马云作为阿里巴巴的创始人，清楚地知道自己的位置，他不需要懂管理、业务、财务，他需要调兵遣将，把合适的人放在合适的位置上。</p><p>马云不懂管理，就请来通用高管关明生担任COO；不懂财务，就找来国际专家蔡崇信当CEO；不懂技术，就从雅虎挖来吴炯担任CTO...</p><p>知道自己和员工的长处和短处，每一个人放在合适的位置上，才能把大家的潜能最大化。</p><p><img src=\"http://p3.pstatp.com/large/5b5900055420eb358b22\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>5、不要野狗和小白兔</p><p>什么是野狗？业绩很好，每年都能销售出很多产品，但价值观很差，不讲究团队精神，也不讲究质量、服务。</p><p>什么是小白兔？人特别善良，特别热情，但业绩好不起来了。</p><p>这两种人，马云说，一定不能手软，一定要杀。野狗对团队的伤害很大，小白兔根本创造不出价值。</p><p>不过，小白兔离开公司三个月后，还有机会再回来，只要他能把业绩搞上，野狗就没有这个机会了。</p><p><img src=\"http://p3.pstatp.com/large/5e700003274c851a2312\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>6、用人要疑、疑人要用</p><p>有些人讲究“用人不疑”，有什么工作只订个目标，让手下去做，也不过问过程。</p><p>马云说，这样做是不对的，用人也要疑。</p><p>譬如你在年初给手下定了目标，年末要达到多少目标，到年中时，你也要检查一下进度，这不是不信任，而是监督、鞭策。这就是“用人要疑”。</p><p>如果他的进度不如人意，向你解释了原因，你心存疑惑，但也要让他继续下去，这就是“疑人要用”。</p><p>这才是真正的信任，哪怕你也摸不准他的做法是对是错，也会用他，让他去做。为什么允许自己失败，却不允许别人失败呢？这就是“用人要疑，疑人要用”。</p><p><img src=\"http://p1.pstatp.com/large/5b5a0005426c26c8a0f2\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>7、保持乐观心态</p><p>很多老板遇到员工出错，就会大发雷霆，事情没解决，自己还伤身体。</p><p>马云觉得，凡是要放轻松，多点经历，就会平淡地看待这个世界，都有好、都有坏，了解人性是什么、世界是什么。</p><p>员工总会出错，关键是如果帮助他成长、修补制度，让类似事情不再发生；</p><p>公司总会遭受挫折，关键是跌倒了如何爬起来，继续前进。</p><p>心态一定要乐观，如果不乐观，人就很容易走偏，公司的氛围也会很糟糕。</p><p><img src=\"http://p3.pstatp.com/large/5b5c0004c48b59a598cc\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>用人八字诀：</p><p>“惜马用牛赶猪打狗”</p><p>企业用人，其实是重中之重，但怎么用人，各位企业家就头疼了。</p><p>其实，所谓“人”，虽然千差万别，但拿动物做比喻，无外乎“马、牛、猪、狗”四类，因此，有古语云：“惜马用牛赶猪打狗。”</p><p>精髓就是：用其所长，避其所短。</p><p>马云也说：</p><p>“我脸上的每部分拆开来看还可以，但是合起来就那么难看，有的人每个部位都不漂亮，但合在一起就很漂亮了。”</p><p><img src=\"http://p1.pstatp.com/large/5b59000554212e7bdc67\" alt=\"马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！\"/></p><p>找准每个人的位置，让大家都能发挥自己的最大潜质，把一个人的小能量，汇集成所有人的大能量，公司才能迎来长足的进步！</p><p>所以，如果你是企业家，在抱怨底下的人太糟糕时，请扪心自问，你把人用对了吗？你把人放对地方了吗？</p><p>再好的千里马，放在不识货的人手里，也只能终于困在马房之中，郁郁不得志。千里马常有，而伯乐不常有。老板们想要得到人才，自己先做个伯乐吧！</p><p><br/></p>','0','0','0','0','0','0');
INSERT INTO `qb_cms_content1` VALUES ('17','1','9','李小龙的儿子李国豪究竟是被谁谋杀的？这三个人嫌疑最大！','1','1','80','1','0','0','1517533867','1519973979','0','uploads/images/20180302/1cb8ca8badefa49403b56d02365b5246.JPG,uploads/images/20180302/1271aa70880157872ecd4c1cac3f6eca.jpg','<p style=\"text-align:center\"><img src=\"http://p1.pstatp.com/large/5e7c000294983680cce9\" alt=\"李小龙的儿子李国豪究竟是被谁谋杀的？这三个人嫌疑最大！\"/></p><p>一代功夫巨星李小龙于1973年7月20日死在好友丁佩家中，死前无任何征兆，李小龙的突然死亡让所有人震惊，尽管众说纷纭，但是李小龙的死至今还是一个谜团，其好友丁佩几十年来也遭受了巨大的压力，指责和谩骂如影随形，让她每一天都过的心惊胆战，正当大家都已经对李小龙的死逐渐淡忘的时候，又一爆炸性的消息瞬间出现在了世界各大媒体报刊上，时隔二十年之后，李小龙的儿子李国豪同样突然死亡，而且死的不明不白，这一切还要从李国豪当时正在拍摄的电影《乌鸦》说起。</p><p><img src=\"http://p3.pstatp.com/large/5e780003390d1a44e98c\" alt=\"李小龙的儿子李国豪究竟是被谁谋杀的？这三个人嫌疑最大！\"/></p><p>当时，李国豪正在美国北卡罗莱纳州威尔明顿市的电影片场拍摄《乌鸦》，这是一部枪战剧，而李国豪所饰演的角色在电影中被人一枪毙命，但让李国豪没想到的是他自己本人真的死在了剧组的道具枪下，枪声一响，李国豪应声而倒，鲜血四溅，当大家发现异常时，所有人都吓坏了，于是大家七手八脚的将李国豪抬到医院，可是由于抢救不及时加上受伤太重，李国豪最终抢救无效死亡。</p><p><img src=\"http://p3.pstatp.com/large/5e830000d9d2a5ae4524\" alt=\"李小龙的儿子李国豪究竟是被谁谋杀的？这三个人嫌疑最大！\"/></p><p>李国豪的死和当年李小龙的死马上就被大家联系到了一起，天下难道还会有如此巧合的事情吗？或者这就是命运的安排？一时之间李小龙当年的死因又被挖掘出来，成了当时最大的谜团，后来警方对剧组的道具枪进行了检查，发现其他子弹都是空子弹，而只有射向李国豪身体的那一刻子弹是实弹，于是警方顺藤摸瓜，找出了杀害李国豪嫌疑最大的三个人，他们分别是演员麦西，在剧中正是他向李国豪开枪，很自然他被当成了杀害李国豪的第一嫌疑人，但是麦西却大喊自己冤枉，因为自己在演出之前并没有接触到枪支，他是按照剧情开枪的，他认为其中装的是空子弹，而枪是开拍前剧组道具总管交给他的。所以道具总管很自然的就成了第二嫌疑人。</p><p><img src=\"http://p3.pstatp.com/large/5e7b0002f8f9d108e83c\" alt=\"李小龙的儿子李国豪究竟是被谁谋杀的？这三个人嫌疑最大！\"/></p><p>剧组道具总管同样摸不着头脑，因为在剧组中，他只管保管枪支，而装弹药这种活是由特技人员来干的，所以特技人员就是第三嫌疑人，但特技人员说自己装的都是空子弹，装完后交给了道具总管，按照程序来说，道具总管在收到枪之后应该进行检查，但是由于当时道具总管的粗心，这支枪没被检查就用上了，美国警方排查来排查去，觉得这三个人任何一个都可能暗杀李国豪，到最后也没能理出个头绪，而这场谋杀案就这样不了了之了，其实这三人不一定非要提前将实弹装入枪支，只要其中任何一人准备一颗实弹，李国豪就必死无疑，所有人都忽略了这一点，可能连李国豪自己都没有想到他的仇人会用这种方式来谋杀自己。</p><p><img src=\"http://p3.pstatp.com/large/5e7a00032b53b07369d0\" alt=\"李小龙的儿子李国豪究竟是被谁谋杀的？这三个人嫌疑最大！\"/></p><p>后来警方放过了这三名嫌疑人，当然也放弃了对真凶的追捕，李国豪的死亡就此成为了一个新的谜团，但实际上凶手肯定就是这三人其中的一个，因为剧组在拍戏之前只有这三人接触过枪支，可是因为证据不足，所以只能不了了之，李国豪去世的时候仅仅二十八岁，比他的父亲还年轻，他本来可以成为和他父亲一样优秀的电影演员，但是却因为一次组织周密的谋杀失去了生命，实在是非常遗憾，二十多年前，李国豪曾经在李小龙的葬礼上指着父亲的遗照叫喊“电影，电影”，他以为父亲的死是虚假的，只是拍摄的一个电影镜头而已，但没想到自己二十年之后死在了电影中，这难道真是冥冥中注定吗</p><p><br/></p>','0','0','0','0','0','0');
INSERT INTO `qb_cms_content2` VALUES ('18','2','14','李国豪正在美国北卡罗莱纳州威尔明顿市的电影片场拍摄《乌鸦》','0','','1','67','1','0','0','李国豪的死和当年李小龙的死马上就被大家联系到了一起，天下难道还会有如此巧合的事情吗？或者这就是命运的安排？一时之间李小龙当年的死因又被挖掘出来，成了当时最大的谜团，后来警方对剧组的道具枪进行了检查，发现其他子弹都是空子弹，而只有射向李国豪身体的那一刻子弹是实弹，于是警方顺藤摸瓜，找出了杀害李国豪嫌疑最大的三个人，他们分别是演员麦西，在剧中正是他向李国豪开枪，很自然他被当成了杀害李国豪的第一嫌疑人，但是麦西却大喊自己冤枉，因为自己在演出之前并没有接触到枪支，他是按照剧情开枪的，他认为其中装的是空子弹，而枪是开拍前剧组道具总管交给他的。所以道具总管很自然的就成了第二嫌疑人。','1517534109','1522116690','0','[{\"title\":\"004.jpg\",\"url\":\"\",\"picurl\":\"uploads/images/20180302/2334683a87b83f94f7106d31fc6ce0be.jpg\"},{\"title\":\"2.jpeg\",\"url\":\"\",\"picurl\":\"uploads/images/20180312/5518ce3a2f1c8eb669146b67bffc7a56.jpeg\"},{\"title\":\"3.jpeg\",\"url\":\"\",\"picurl\":\"uploads/images/20180312/efebd1b5ec8979134a8da76c539b8f05.jpeg\"}]','0','0','0','0','0','30');
INSERT INTO `qb_cms_content2` VALUES ('19','2','14','赵薇夫妇沦陷，满脸憔悴，昔日好友悉数离开，只有他俩力挺！','0','','1','150','1','0','0','1999年，赵薇发行首张专辑《Double双》、《天使旅行箱》，凭专辑获2005年MTV亚洲大奖中国地区最受欢迎歌手和2008年最佳女歌手等奖项。2001年，赵薇开始投身大银幕。','1517556671','1523951822','0','[{\"title\":\"1a2aabf066b644be8336521049acef89.jpg\",\"url\":\"\",\"picurl\":\"uploads/images/20180302/ed46f4c7a30f7036bdb4182c2e91ce85.jpg\"},{\"title\":\"35.jpg\",\"url\":\"\",\"picurl\":\"uploads/images/20180302/09eb406c7b68b32f4f1a08d91e0405a4.jpg\"},{\"title\":\"35C0B9830E8D885E4EDAB66A83300DD392BA32E7_800_800.jpg\",\"url\":\"\",\"picurl\":\"uploads/images/20180302/4e130207d16de8e9325372e10f24b1c6.jpg\"},{\"title\":\"36B3CFF456C7BAB023B0B9E4B41113A0076BC4D6_800_800.jpg\",\"url\":\"\",\"picurl\":\"uploads/images/20180302/bbdad5bba94b25b1eb45004c3a946b06.jpg\"},{\"title\":\"53C7B7256FC241FB8A41F3CAD014DCE6200608031155.jpg\",\"url\":\"\",\"picurl\":\"uploads/images/20180302/2fca054b5a1adda5a4867a3dab39339c.jpg\"},{\"title\":\"60.JPG\",\"url\":\"\",\"picurl\":\"uploads/images/20180302/47ec268298550dbaf723f63f9c8068f8.JPG\"}]','0','0','0','0','0','30');
INSERT INTO `qb_cms_field` VALUES ('10','title','标题','text','varchar(256) NOT NULL',NULL,NULL,'','0','1','','','','','','2','','','','100','1','1','1','');
INSERT INTO `qb_cms_field` VALUES ('11','picurl','组图','images','text NOT NULL','','','','0','1','','','','','','2','','','','100','0','0','0','');
INSERT INTO `qb_cms_field` VALUES ('12','content','文章内容','ueditor','text NOT NULL','','','','0','1','','','','','','2','','','','100','0','0','0','');
INSERT INTO `qb_cms_field` VALUES ('19','title','图片主题','text','varchar(256) NOT NULL','','','','0','2','','','','','','2','','','','100','1','1','1','');
INSERT INTO `qb_cms_field` VALUES ('20','picurl','封面图','hidden','varchar(32) NOT NULL','','','','0','2','','','','','','2','','','','98','0','0','0','');
INSERT INTO `qb_cms_field` VALUES ('21','content','图片介绍','textarea','text NOT NULL','','','','0','2','','','','','','2','','','','90','0','0','0','');
INSERT INTO `qb_cms_field` VALUES ('22','title','标题','text','varchar(256) NOT NULL',NULL,NULL,'','0','3','','','','','','2','','','','100','1','1','1','');
INSERT INTO `qb_cms_field` VALUES ('23','picurl','封面图','jcrop','varchar(128) NOT NULL','','','','0','3','','','','','','2','','','','99','0','0','0','');
INSERT INTO `qb_cms_field` VALUES ('24','content','内容介绍','ueditor','text NOT NULL','','','','0','3','','','','','','2','','','','90','0','0','0','');
INSERT INTO `qb_cms_field` VALUES ('25','pics','组图带介绍','images2','text NOT NULL','','','','0','2','','','','','','2','','','','97','0','0','0','');
INSERT INTO `qb_cms_field` VALUES ('26','mv_url','视频地址','file','varchar(128) NOT NULL','','','','0','3','','','','','','2','','','','98','0','0','0','');
INSERT INTO `qb_cms_module` VALUES ('1','','文章模型','','','100','1515221331','0');
INSERT INTO `qb_cms_module` VALUES ('2','','图片模型','','','100','1515236691','0');
INSERT INTO `qb_cms_module` VALUES ('3','','视频模型','','','100','1515236720','0');
INSERT INTO `qb_cms_sort` VALUES ('9','0','1','娱乐新闻','10','','','','','','','');
INSERT INTO `qb_cms_sort` VALUES ('10','9','1','香港娱乐新闻','0','','','','','','','');
INSERT INTO `qb_cms_sort` VALUES ('11','9','1','大陆娱乐新闻','0','','','','','','','');
INSERT INTO `qb_cms_sort` VALUES ('12','9','1','广东娱乐','0','','','','','','','');
INSERT INTO `qb_cms_sort` VALUES ('14','0','2','图片专栏','9','','','','','','','');
INSERT INTO `qb_config` VALUES ('29','2','是否对图片加水印','is_waterimg','2','radio','1|启用\n2|禁用','1','','','0','0');
INSERT INTO `qb_config` VALUES ('62','8','是否允许重复的邮箱','emailOnly','1','radio','0|允许重复\r\n1|不允许重复','1','','','97','0');
INSERT INTO `qb_config` VALUES ('81','0','论坛接口数据表前缀','passport_pre','`bbs`.pw_','','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('82','0','是否整合论坛','passport_type','','text','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('89','20','QQ登录接口ID','qqlogin_appid','','text','','1','','','0','-9');
INSERT INTO `qb_config` VALUES ('142','20','QQ登录接口密钥','qqlogin_appsecret','','text','','1','','网站回调域是:    http://你的域名/index.php/p/login-qq-index.html;http://你的域名/p/login-qq-index.html','0','-9');
INSERT INTO `qb_config` VALUES ('143','7','是否启用QQ登录','QQ_login','0','','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('163','1','网站风格','style','default','select','app\\common\\util\\Style@listStyle','1','','','99','0');
INSERT INTO `qb_config` VALUES ('172','1','COOKIE前缀','cookiePre','','','','1','','安装多份系统才面要，默认留空','0','0');
INSERT INTO `qb_config` VALUES ('174','2','水印图片地址','waterimg','','image','','1','','最好是透明的PNG图','0','0');
INSERT INTO `qb_config` VALUES ('175','2','是否启用GD库','if_gdimg','0','radio','1|启用\n0|禁用','1','','可以对大图进行缩小/截取缩略图','0','0');
INSERT INTO `qb_config` VALUES ('196','8','手工注册是否自动通过审核','RegYz','1','radio','0|人工审核\r\n1|自动审核','1','','','98','0');
INSERT INTO `qb_config` VALUES ('228','3','邮箱端口','MailPort','465','text','','1','','QQ邮箱端口是465,很多邮箱已经不支持25端口了','0','0');
INSERT INTO `qb_config` VALUES ('229','3','邮箱接口帐号','MailId','','text','','1','','推荐用QQ邮箱','0','0');
INSERT INTO `qb_config` VALUES ('232','8','注册需要验证码','yzImgReg','0','radio','0|不需要\r\n1|需要','1','','','105','0');
INSERT INTO `qb_config` VALUES ('246','3','邮箱接口密码','MailPw','','text','','1','','QQ邮箱不是真实登录密码,而是另外的一个KEY','0','0');
INSERT INTO `qb_config` VALUES ('251','3','邮箱服务器IP或域名','MailServer','smtp.qq.com','text','','1','','QQ普通邮箱smtp.qq.com 而QQ域名邮箱是smtp.exmail.qq.com','0','0');
INSERT INTO `qb_config` VALUES ('256','1','网站是否开放','web_open','1','radio','0|关闭\n1|开放','1','','','100','0');
INSERT INTO `qb_config` VALUES ('266','8','注册可得多少积分','regmoney','5','number','','1','','','96','0');
INSERT INTO `qb_config` VALUES ('269','8','禁止注册的用户名','forbidRegName','管理员\r\nadmin','textarea','','1','','每个换一行','0','0');
INSERT INTO `qb_config` VALUES ('271','8','是否关闭手工注册','forbid_normal_reg','0','radio','0|开放\r\n1|关闭','1','','','110','0');
INSERT INTO `qb_config` VALUES ('299','1','版权信息','copyright','联系电话:020-28998648 @广州齐博网络科技有限公司','textarea','','1','','','-1','0');
INSERT INTO `qb_config` VALUES ('300','1','百度地图KEY','baidu_map_key','','','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('302','1','COOKIE域名','cookieDomain','','','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('303','1','是否关闭网站调试','forbid_show_bug','1','radio','0|调试 还没上线\n1|关闭调试 正式运营','1','','','0','0');
INSERT INTO `qb_config` VALUES ('305','1','远程附件地址','remote_updir','upload_files/web1','text','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('306','1','上传文件的最大尺寸','upfileMaxSize','','','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('307','1','允许上传文件的类型','upfileType','.rar .txt .jpg .gif .bmp .png .zip .mp3 .wma .wmv .mpeg .mpg .rm .ram .htm .doc .swf .avi .flv .sql .doc .ppt .xls .chm .pdf .mp4 .pem','','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('643','1','会员中心风格','member_style','default','select','app\\common\\util\\Style@get_style@[\"member\"]','1','','','98','0');
INSERT INTO `qb_config` VALUES ('313','1','站点关闭原因','close_why','网站维护当中,暂停访问.','text','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('318','11','PC版支付宝接口帐号','alipay_id','','text','','1','','邮箱或手机号','89','0');
INSERT INTO `qb_config` VALUES ('320','1','站点SEO关键字','seo_keyword','','text','','1','','','998','0');
INSERT INTO `qb_config` VALUES ('321','1','站点SEO描述','seo_description','','text','','1','','','997','0');
INSERT INTO `qb_config` VALUES ('323','1','网站名称','webname','齐博X1.0','text','','1','','','1000','0');
INSERT INTO `qb_config` VALUES ('333','2','图片水印透明度','waterAlpha','62','range','','1','','请输入数值，80代表80%','0','0');
INSERT INTO `qb_config` VALUES ('335','4','微信公众号AppSecret（应用密钥）','weixin_appsecret','','text','','1','','','99','0');
INSERT INTO `qb_config` VALUES ('339','4','微信公众号TOKEN','weixin_token','','text','','1','','微信服务器地址(URL)那里输入的网址是 http://你的域名/p/weixin-api-index.html','98','-2');
INSERT INTO `qb_config` VALUES ('340','11','PC版支付宝交易安全校验码（key）','alipay_key','','text','','1','','','87','0');
INSERT INTO `qb_config` VALUES ('347','12','新用户关注微信时回复的纯文本内容','weixin_welcome','','text','','1','','下面4项填写了，这一项就无效','100','0');
INSERT INTO `qb_config` VALUES ('348','12','新用户关注微信时图文标题','weixin_welcome_title','感谢你关注齐博微信营销系统！','text','','1','','','99','0');
INSERT INTO `qb_config` VALUES ('349','2','水印位置','waterpos','9','radio','0|随机\n1|图片顶部左边\n2|图片顶部中间\n3|图片顶部右边\n4|图片中部左边\n5|图片中部中间\n6|图片中部右边\n7|图片底部左边\n8|图片底部中间\n9|图片底部右边','1','','','0','0');
INSERT INTO `qb_config` VALUES ('350','12','回答不上的问题','weixin_problem','请稍候，商家晚点会回复你的问题！','textarea','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('351','12','新关注微信发送的图片','weixin_welcome_pic','','image','','1','','','98','0');
INSERT INTO `qb_config` VALUES ('352','12','客服人员','weixin_reply_kefu','','text','','1','','请输入客服的UID数字值，每个用空格隔开，留空则是管理员做客服','70','0');
INSERT INTO `qb_config` VALUES ('353','12','微信客服回复标志','weixin_reply_Tag',' ','text','','1','','比如你可以设置在文字的前面输入一个空格即代表回复最近那个用户的信息，两个空格就回复倒数第二个用户咨询的信息','69','0');
INSERT INTO `qb_config` VALUES ('354','12','微信客户离开多久算离线','weixin_reply_Time','1','number','','1','','单位小时（默认是1小时）','68','0');
INSERT INTO `qb_config` VALUES ('355','5','插件评论','Commend2CodeImgRegJF','101','text','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('365','4','微信公众号AppID（应用ID）','weixin_appid','','text','','1','','','100','0');
INSERT INTO `qb_config` VALUES ('366','4','微信支付接口KEY（商户API密钥）','weixin_paykey','','text','','1','','认证服务号才有的微信支付功能，留空就不能收款，填写后，才能在线收款','79','0');
INSERT INTO `qb_config` VALUES ('405','1','备案号','miibeian_gov_cn','京ICP备050453号','','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('425','1','积分名称','MoneyName','金币','text','','1','','可以取名为积分、金币','0','0');
INSERT INTO `qb_config` VALUES ('433','1','站点加密字符串','mymd5','eff2278b0a71d32ac8db','text','','1','','设置后就不要随意修改，不然会影响到之前的数据','0','0');
INSERT INTO `qb_config` VALUES ('439','11','wap版支付宝接口合作者身份','wap_ali_partner','','text','','1','','','98','0');
INSERT INTO `qb_config` VALUES ('440','11','wap版支付宝接口私钥','wap_ali_private_key','','text','','1','','这一项可以留空，使用默认的','96','0');
INSERT INTO `qb_config` VALUES ('441','11','wap版支付宝接口公钥','wap_ali_public_key','','text','','1','','','97','0');
INSERT INTO `qb_config` VALUES ('442','11','PC版支付宝合作者身份','alipay_partner','','text','','1','','合作身份者id，以2088开头的16位纯数字','88','0');
INSERT INTO `qb_config` VALUES ('444','1','网站SEO标题','seo_title','齐博著名的开源软件提供商','text','','1','','','999','0');
INSERT INTO `qb_config` VALUES ('447','6','模块红包','RegHongBao','0.100','text','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('451','13','微信公众号二维码图片','weixin_code_img','http://www.wxyxpt.com/upload_files/web1/wenxin_center/49b_1_20160923120948_nu45d.jpg','image','','1','','','0','-2');
INSERT INTO `qb_config` VALUES ('466','1','积分单位','MoneyDW','元','','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('467','11','PC版支付宝接口类型','alipay_service','create_direct_pay_by_user','radio','create_direct_pay_by_user|即时到账\ncreate_partner_trade_by_buyer|纯担保交易\ntrade_create_by_buyer|标准实物\n','1','','推荐第一项','86','0');
INSERT INTO `qb_config` VALUES ('468','11','wap版支付宝接口收款帐号','wap_ali_id','','text','','1','','','99','0');
INSERT INTO `qb_config` VALUES ('473','4','微信对外转帐apiclient_cert证书','weixin_apiclient_cert','','file','','1','','商家对外转帐接口，收款用不到，留空就不能对外转帐','0','0');
INSERT INTO `qb_config` VALUES ('474','4','微信对外转帐apiclient_key证书','weixin_apiclient_key','','file','','1','','商家对外转帐接口，收款用不到，留空就不能对外转帐','0','0');
INSERT INTO `qb_config` VALUES ('475','4','微信对外转帐rootca证书','weixin_rootca','','file','','1','','商家对外转帐接口，收款用不到，留空就不能对外转帐','0','0');
INSERT INTO `qb_config` VALUES ('476','6','地要','reg_group00','','text','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('477','4','微信支付接口MCHID（商户号）','weixin_payid','','text','','1','','认证服务号才有的微信支付功能，留空就不能收款，填写后，才能在线收款，微信接口设置的支付授权目录是 https://你的域名/index.php/index/pay/index.html','80','-2');
INSERT INTO `qb_config` VALUES ('478','12','新用户关注微信时图文链接','weixin_welcome_link','','text','','1','','','97','0');
INSERT INTO `qb_config` VALUES ('479','12','新用户关注微信时图文描述','weixin_welcome_desc','','textarea','','1','','','96','0');
INSERT INTO `qb_config` VALUES ('481','4','公众号类型','weixin_type','0','radio','0|没有对接公众号\n-1|未认证的订阅号\n1|未认证的服务号\n2|认证订阅号\n3|认证服务号','1','','','101','0');
INSERT INTO `qb_config` VALUES ('482','1','购买用户组有效天数','group_expire_data','365','number','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('483','14','SEO标题','mseo_title','','text','','0','','','100','1');
INSERT INTO `qb_config` VALUES ('484','14','SEO优化关键字keywords','mseo_keyword','','text','','0','','','99','1');
INSERT INTO `qb_config` VALUES ('485','17','客服邮箱','service_email','','text','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('486','17','联系电话','service_tel','020-28998648','text','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('487','17','客服QQ','service_qq','','text','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('488','17','微信二维码','service_wxcode','','image','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('490','4','微信企业帐户可用余额','weixinTotalMoney','','text','','1','','填写的数值小于或等于企业帐户里的可用余额，当数值小于1时，系统将无法给会员微信付款','0','-2');
INSERT INTO `qb_config` VALUES ('496','1','登录后台是否启用验证码','admin_login_usercode','0','radio','0|不启用\n1|启用','1','','','0','0');
INSERT INTO `qb_config` VALUES ('497','14','SEO优化描述description','mseo_description','','text','','0','','','98','1');
INSERT INTO `qb_config` VALUES ('498','14','是否开启当前模块','is_open_modlue','1','radio','1|开启\n0|关闭','0','','','97','1');
INSERT INTO `qb_config` VALUES ('499','14','允许发布内容的用户组','can_post_group','','checkbox','app\\common\\model\\Group@getTitleList@[{\"id\":[\"<>\",2]}]','0','','','96','1');
INSERT INTO `qb_config` VALUES ('500','14','发布内容自动通过审核的用户组','post_auto_pass_group','','checkbox','app\\common\\model\\Group@getTitleList@[{\"id\":[\"<>\",2]}]','0','','','95','1');
INSERT INTO `qb_config` VALUES ('503','14','内容被设为精华奖励积分个数','com_info_add_money','','text','','0','','','94','1');
INSERT INTO `qb_config` VALUES ('505','16','SEO标题','mseo_title','','text','','0','','','100','2');
INSERT INTO `qb_config` VALUES ('506','16','SEO优化关键字keywords','mseo_keyword','','text','','0','','','99','2');
INSERT INTO `qb_config` VALUES ('507','16','SEO优化描述description','mseo_description','','text','','0','','','98','2');
INSERT INTO `qb_config` VALUES ('508','16','是否开启当前模块','is_open_modlue','1','radio','1|开启\n0|关闭','0','','','97','2');
INSERT INTO `qb_config` VALUES ('509','16','允许发布商品的用户组','can_post_group','','checkbox','app\\common\\model\\Group@getTitleList@[{\"id\":[\"<>\",2]}]','0','','','96','2');
INSERT INTO `qb_config` VALUES ('510','16','发布商品自动通过审核的用户组','post_auto_pass_group','','checkbox','app\\common\\model\\Group@getTitleList@[{\"id\":[\"<>\",2]}]','0','','','95','2');
INSERT INTO `qb_config` VALUES ('515','16','是否允许删除已付款商品订单','can_del_ispay_order','','radio','1|允许\n0|不允许','0','','','94','2');
INSERT INTO `qb_config` VALUES ('516','16','客户下单是否短消息通知商家','post_order_msg_hy','','radio','1|通知\n0|不通知','0','','','93','2');
INSERT INTO `qb_config` VALUES ('517','16','客户下单是否短信通知商家','post_order_sms_hy','','radio','1|通知\n0|不通知','0','','','92','2');
INSERT INTO `qb_config` VALUES ('518','16','客户下单是否微信通知商家','post_order_wx_hy','','radio','1|通知\n0|不通知','0','','','91','2');
INSERT INTO `qb_config` VALUES ('519','16','客户付款成功是否短消息通知商家','pay_order_msg_hy ','','radio','1|通知\n0|不通知','0','','','90','2');
INSERT INTO `qb_config` VALUES ('520','16','客户付款成功是否短信通知商家','pay_order_sms_hy ','','radio','1|通知\n0|不通知','0','','','89','2');
INSERT INTO `qb_config` VALUES ('521','16','客户付款成功是否微信通知商家','pay_order_wx_hy','','radio','1|通知\n0|不通知','0','','','88','2');
INSERT INTO `qb_config` VALUES ('522','16','是否启用会员帐户余额付款','can_user_rmb_pay','','radio','1|启用\n0|关闭','0','','','87','2');
INSERT INTO `qb_config` VALUES ('523','4','小程序AppID','wxapp_appid','','text','','1','','','151','-2');
INSERT INTO `qb_config` VALUES ('524','4','小程序密钥','wxapp_appsecret','','text','','1','','','150','-2');
INSERT INTO `qb_config` VALUES ('535','4','小程序支付接口（商户号）','wxapp_payid','','text','','1','','','149','-2');
INSERT INTO `qb_config` VALUES ('536','4','小程序支付接口KEY（商户API密钥）','wxapp_paykey','','text','','1','','','148','-2');
INSERT INTO `qb_config` VALUES ('557','1','是否定义频道当作默认主页','set_module_index','0','select','app\\common\\util\\Module@getTitleList@[\"不使用频道做主页\",\"keywords\"]','1','','','0','0');
INSERT INTO `qb_config` VALUES ('616','1','网站LOGO','logo','','image','','1','','','0','0');
INSERT INTO `qb_config` VALUES ('631','1','是否隐藏前台网址中的index.php文件名','hiden_index_php','0','radio','0|显示\r\n1|隐藏','1','','如果空间不支持,就选择显示,不然前台页面会无法打开','0','0');
INSERT INTO `qb_config` VALUES ('632','31','阿里云短信模板','sms_template','','text','','1','','模板中使用的变量只能是code,如果不是的话,请先修改或者重新申请一个把变量名换用code','7','-11');
INSERT INTO `qb_config` VALUES ('633','31','阿里云短信签名','sms_sign_name','','text','','1','','','8','-11');
INSERT INTO `qb_config` VALUES ('634','31','阿里云短信接口密钥','sms_access_key','','text','','1','','即AccessKeySecret','9','-11');
INSERT INTO `qb_config` VALUES ('635','31','阿里云短信接口id','sms_access_id','','text','','1','','即AccessKeyId','10','-11');
INSERT INTO `qb_config` VALUES ('639','8','注册是否启用邮箱获取验证码','reg_email_num','0','radio','0|禁用\r\n1|启用','1','','务必先配置好邮箱接口','102','0');
INSERT INTO `qb_config` VALUES ('640','8','注册是否启用手机短信获取验证码','reg_phone_num','0','radio','0|禁用\r\n1|启用','1','','务必先配置好短信接口','101','0');
INSERT INTO `qb_config` VALUES ('641','8','是否启用微信公众号获取验证码','reg_weixin_num','0','radio','0|禁用\r\n1|启用','1','','用户需要先关注公众号,然后回复验证码,才能收到验证码','100','0');
INSERT INTO `qb_config` VALUES ('642','8','前台显示用户的帐号还是昵称','show_nickname','1','radio','0|显示帐号\r\n1|显示昵称','1','','昵称可以随意修改,帐号是固定的','0','0');
INSERT INTO `qb_config` VALUES ('644','1','后台风格','admin_style','default','select','app\\common\\util\\Style@get_style@[\"admin\"]','1','','','97','0');
INSERT INTO `qb_config_group` VALUES ('1','基础设置','999','0','1','1');
INSERT INTO `qb_config_group` VALUES ('2','水印设置','993','0','1','0');
INSERT INTO `qb_config_group` VALUES ('3','邮箱接口设置','994','0','1','0');
INSERT INTO `qb_config_group` VALUES ('4','微信接口','998','-2','1','1');
INSERT INTO `qb_config_group` VALUES ('14','基础设置','899','1','0','0');
INSERT INTO `qb_config_group` VALUES ('8','会员注册','996','0','1','0');
INSERT INTO `qb_config_group` VALUES ('11','支付宝接口','997','0','1','0');
INSERT INTO `qb_config_group` VALUES ('12','微信客服回复设置','699','-2','0','1');
INSERT INTO `qb_config_group` VALUES ('13','微信其它设置','698','-2','0','1');
INSERT INTO `qb_config_group` VALUES ('16','基础设置','799','2','0','0');
INSERT INTO `qb_config_group` VALUES ('17','联系方式','992','0','1','1');
INSERT INTO `qb_config_group` VALUES ('20','登录接口','0','-9','1','1');
INSERT INTO `qb_config_group` VALUES ('31','阿里云短信接口','9','-11','1','1');
INSERT INTO `qb_group` VALUES ('2','1','黑名单','0','a:54:{s:10:\"upfileType\";s:0:\"\";s:13:\"upfileMaxSize\";s:0:\"\";s:14:\"PassContribute\";s:1:\"1\";s:17:\"SearchArticleType\";s:1:\"1\";s:16:\"PostArticleYzImg\";s:1:\"1\";s:13:\"EditPassPower\";s:1:\"0\";s:12:\"SetTileColor\";s:1:\"0\";s:14:\"SetSellArticle\";s:1:\"0\";s:17:\"SetSpecialArticle\";s:1:\"0\";s:17:\"SetArticleKeyword\";s:1:\"0\";s:20:\"AddArticleKeywordNum\";s:0:\"\";s:21:\"AddArticleCopyfromNum\";s:0:\"\";s:18:\"SelectArticleStyle\";s:1:\"0\";s:16:\"SelectArticleTpl\";s:1:\"0\";s:13:\"SetArticleTpl\";s:1:\"0\";s:18:\"SetArticlePosttime\";s:1:\"0\";s:18:\"SetArticleViewtime\";s:1:\"0\";s:16:\"SetArticleHitNum\";s:1:\"0\";s:18:\"SetArticlePassword\";s:1:\"0\";s:19:\"SetArticleDownGroup\";s:1:\"0\";s:19:\"SetArticleViewGroup\";s:1:\"0\";s:17:\"SetArticleJumpurl\";s:1:\"0\";s:19:\"SetArticleIframeurl\";s:1:\"0\";s:21:\"SetArticleDescription\";s:1:\"0\";s:16:\"SetArticleTopCom\";s:1:\"0\";s:13:\"SetSmallTitle\";s:1:\"0\";s:19:\"CommentArticleYzImg\";s:1:\"1\";s:17:\"CollectArticleNum\";s:0:\"\";s:15:\"CreatSpecialNum\";s:0:\"\";s:13:\"PostNoDelCode\";s:1:\"0\";s:7:\"SetVote\";s:1:\"0\";s:11:\"SetHtmlName\";s:1:\"0\";s:16:\"PassContributeSP\";s:1:\"0\";s:14:\"AllowUploadMax\";s:1:\"0\";s:11:\"comment_num\";s:2:\"10\";s:10:\"comment_yz\";s:1:\"1\";s:11:\"comment_img\";s:1:\"0\";s:16:\"sell_postauto_yz\";s:1:\"0\";s:15:\"buy_postauto_yz\";s:1:\"0\";s:15:\"post_pingpai_yz\";s:1:\"0\";s:10:\"use2domain\";s:1:\"0\";s:16:\"useHomepageStyle\";s:1:\"0\";s:21:\"view_buy_view_contact\";s:1:\"0\";s:13:\"post_sell_num\";s:0:\"\";s:12:\"post_buy_num\";s:0:\"\";s:13:\"post_news_num\";s:0:\"\";s:14:\"post_photo_num\";s:0:\"\";s:11:\"post_hr_num\";s:0:\"\";s:17:\"post_zhanghui_num\";s:0:\"\";s:12:\"post_ZLG_num\";s:0:\"\";s:16:\"post_pingpai_num\";s:0:\"\";s:19:\"post_baojiadian_num\";s:0:\"\";s:19:\"post_xunjiadian_num\";s:0:\"\";s:24:\"post_info_collection_num\";s:0:\"\";}','0','');
INSERT INTO `qb_group` VALUES ('3','1','超级管理员','0','a:51:{s:10:\"upfileType\";s:0:\"\";s:13:\"upfileMaxSize\";s:0:\"\";s:14:\"PassContribute\";s:1:\"1\";s:13:\"EditPassPower\";s:1:\"0\";s:14:\"AllowUploadMax\";s:1:\"1\";s:11:\"comment_num\";s:3:\"999\";s:10:\"comment_yz\";s:1:\"1\";s:11:\"comment_img\";s:1:\"1\";s:16:\"sell_postauto_yz\";s:1:\"0\";s:15:\"buy_postauto_yz\";s:1:\"0\";s:15:\"post_pingpai_yz\";s:1:\"0\";s:10:\"use2domain\";s:1:\"1\";s:16:\"useHomepageStyle\";s:1:\"1\";s:21:\"view_buy_view_contact\";s:1:\"0\";s:13:\"post_sell_num\";s:0:\"\";s:12:\"post_buy_num\";s:0:\"\";s:13:\"post_news_num\";s:3:\"999\";s:14:\"post_photo_num\";s:3:\"999\";s:11:\"post_hr_num\";s:3:\"999\";s:17:\"post_zhanghui_num\";s:0:\"\";s:12:\"post_ZLG_num\";s:0:\"\";s:16:\"post_pingpai_num\";s:0:\"\";s:19:\"post_baojiadian_num\";s:0:\"\";s:19:\"post_xunjiadian_num\";s:0:\"\";s:24:\"post_info_collection_num\";s:3:\"999\";s:18:\"allow_get_homepage\";s:1:\"1\";s:18:\"shoptg_postauto_yz\";s:1:\"1\";s:16:\"shop_postauto_yz\";s:1:\"1\";s:14:\"tg_postauto_yz\";s:1:\"1\";s:14:\"post_coupon_yz\";s:1:\"1\";s:15:\"post_shoptg_num\";s:3:\"999\";s:13:\"post_shop_num\";s:3:\"999\";s:11:\"post_tg_num\";s:3:\"999\";s:15:\"post_coupon_num\";s:3:\"999\";s:13:\"post_gift_num\";s:3:\"999\";s:13:\"postNewsYzImg\";s:1:\"0\";s:13:\"postShopYzImg\";s:1:\"0\";s:11:\"postTgYzImg\";s:1:\"0\";s:15:\"postShopTgYzImg\";s:1:\"0\";s:11:\"postHrYzImg\";s:1:\"0\";s:15:\"postCouponYzImg\";s:1:\"0\";s:13:\"view_hy_money\";s:0:\"\";s:10:\"post_hr_yz\";s:1:\"1\";s:14:\"consumptionMin\";s:1:\"1\";s:10:\"GetCashMin\";s:2:\"20\";s:12:\"GetCashScale\";s:2:\"10\";s:11:\"AllowMakeHy\";s:1:\"1\";s:15:\"AllowMakeMoreHy\";s:1:\"1\";s:13:\"AllowUesStyle\";s:1:\"1\";s:18:\"AllowUesPicMsgSort\";s:1:\"1\";s:13:\"postHyNewsNum\";s:4:\"5000\";}','1','{\"base-admin-setting\\/index\":\"1\",\"base-admin-setting\\/clearcache\":\"1\",\"base-admin-plugin\\/index\":\"1\",\"base-admin-plugin\\/add\":\"1\",\"base-admin-plugin\\/market\":\"1\",\"base-admin-plugin\\/edit\":\"1\",\"base-admin-plugin\\/delete\":\"1\",\"base-admin-plugin\\/copy\":\"1\",\"base-admin-module\\/index\":\"1\",\"base-admin-module\\/add\":\"1\",\"base-admin-module\\/market\":\"1\",\"base-admin-module\\/edit\":\"1\",\"base-admin-module\\/delete\":\"1\",\"base-admin-module\\/copy\":\"1\",\"base-admin-hook\\/index\":\"1\",\"base-admin-hook\\/add\":\"1\",\"base-admin-hook\\/edit\":\"1\",\"base-admin-hook\\/delete\":\"1\",\"base-admin-hook_plugin\\/market\":\"1\",\"base-admin-hook_plugin\\/index\":\"1\",\"base-admin-hook_plugin\\/add\":\"1\",\"base-admin-hook_plugin\\/edit\":\"1\",\"base-admin-hook_plugin\\/delete\":\"1\",\"base-admin-admin_menu\\/index\":\"1\",\"base-admin-admin_menu\\/add\":\"1\",\"base-admin-admin_menu\\/edit\":\"1\",\"base-admin-admin_menu\\/delete\":\"1\",\"base-admin-webmenu\\/index\":\"1\",\"base-admin-webmenu\\/add\":\"1\",\"base-admin-webmenu\\/edit\":\"1\",\"base-admin-webmenu\\/delete\":\"1\",\"base-admin-alonepage\\/index\":\"1\",\"base-admin-alonepage\\/add\":\"1\",\"base-admin-alonepage\\/edit\":\"1\",\"base-admin-alonepage\\/delete\":\"1\",\"base-admin-style\\/market\":\"1\",\"base-admin-style\\/add\":\"1\",\"base-admin-upgrade\\/index\":\"1\",\"base-admin-upgrade\\/sysup\":\"1\",\"base-admin-upgrade\\/check_files\":\"1\",\"base-admin-upgrade\\/view_file\":\"1\",\"base-admin-mysql\\/index\":\"1\",\"base-admin-mysql\\/backup\":\"1\",\"base-admin-mysql\\/showtable\":\"1\",\"base-admin-mysql\\/into\":\"1\",\"base-admin-mysql\\/tool\":\"1\",\"member-admin-member\\/index\":\"1\",\"member-admin-member\\/add\":\"1\",\"member-admin-member\\/edit\":\"1\",\"member-admin-member\\/delete\":\"1\",\"member-admin-group\\/index\":\"1\",\"member-admin-group\\/add\":\"1\",\"member-admin-group\\/edit\":\"1\",\"member-admin-group\\/delete\":\"1\",\"member-admin-group\\/admin_power\":\"1\",\"module-cms-setting\\/index\":\"1\",\"module-cms-content\\/postnew\":\"1\",\"module-cms-content\\/index\":\"1\",\"module-cms-content\\/add\":\"1\",\"module-cms-content\\/edit\":\"1\",\"module-cms-content\\/delete\":\"1\",\"module-cms-sort\\/index\":\"1\",\"module-cms-sort\\/add\":\"1\",\"module-cms-sort\\/edit\":\"1\",\"module-cms-sort\\/delete\":\"1\",\"module-cms-module\\/index\":\"1\",\"module-cms-module\\/add\":\"1\",\"module-cms-module\\/edit\":\"1\",\"module-cms-module\\/delete\":\"1\",\"module-cms-field\\/index\":\"1\",\"module-cms-field\\/add\":\"1\",\"module-cms-field\\/edit\":\"1\",\"module-cms-field\\/delete\":\"1\",\"module-cms-category\\/index\":\"1\",\"module-cms-category\\/add\":\"1\",\"module-cms-category\\/edit\":\"1\",\"module-cms-category\\/delete\":\"1\",\"module-cms-info\\/index\":\"1\",\"module-cms-info\\/add\":\"1\",\"module-cms-info\\/edit\":\"1\",\"module-cms-info\\/delete\":\"1\",\"module-shop-setting\\/index\":\"1\",\"module-shop-content\\/postnew\":\"1\",\"module-shop-content\\/index\":\"1\",\"module-shop-content\\/add\":\"1\",\"module-shop-content\\/edit\":\"1\",\"module-shop-content\\/delete\":\"1\",\"module-shop-sort\\/index\":\"1\",\"module-shop-sort\\/add\":\"1\",\"module-shop-sort\\/edit\":\"1\",\"module-shop-sort\\/delete\":\"1\",\"module-shop-module\\/index\":\"1\",\"module-shop-module\\/add\":\"1\",\"module-shop-module\\/edit\":\"1\",\"module-shop-module\\/delete\":\"1\",\"module-shop-field\\/index\":\"1\",\"module-shop-field\\/add\":\"1\",\"module-shop-field\\/edit\":\"1\",\"module-shop-field\\/delete\":\"1\",\"module-shop-order\\/index\":\"1\",\"module-shop-order\\/edit\":\"1\",\"module-shop-order\\/delete\":\"1\",\"plugin-log-action\\/index\":\"1\",\"plugin-log-action\\/delete\":\"1\",\"plugin-log-login\\/index\":\"1\",\"plugin-log-login\\/delete\":\"1\",\"plugin-weixin-setting\\/index\":\"1\",\"plugin-weixin-menu\\/config\":\"1\",\"plugin-weixin-weixin_autoreply\\/index\":\"1\",\"plugin-weixin-weixin_autoreply\\/add\":\"1\",\"plugin-weixin-weixin_autoreply\\/edit\":\"1\",\"plugin-weixin-weixin_autoreply\\/delete\":\"1\",\"plugin-weixin-weixin_msg\\/index\":\"1\",\"plugin-config_set-config\\/index\":\"1\",\"plugin-config_set-config\\/add\":\"1\",\"plugin-config_set-config\\/edit\":\"1\",\"plugin-config_set-config\\/delete\":\"1\",\"plugin-config_set-group\\/index\":\"1\",\"plugin-config_set-group\\/add\":\"1\",\"plugin-config_set-group\\/edit\":\"1\",\"plugin-config_set-group\\/delete\":\"1\",\"plugin-smsali-setting\\/index\":\"1\",\"plugin-label-applabel\\/index\":\"1\",\"plugin-label-applabel\\/add\":\"1\",\"plugin-label-applabel\\/edit\":\"1\",\"plugin-label-applabel\\/delete\":\"1\",\"plugin-label-applabel\\/set\":\"1\",\"plugin-login-setting\\/index\":\"1\",\"plugin-comment-content\\/index\":\"1\",\"plugin-comment-content\\/delete\":\"1\",\"plugin-area-province\\/index\":\"1\",\"plugin-area-province\\/add\":\"1\",\"plugin-area-province\\/edit\":\"1\",\"plugin-area-province\\/delete\":\"1\",\"plugin-area-city\\/index\":\"1\",\"plugin-area-city\\/add\":\"1\",\"plugin-area-city\\/edit\":\"1\",\"plugin-area-city\\/delete\":\"1\",\"plugin-area-zone\\/index\":\"1\",\"plugin-area-zone\\/add\":\"1\",\"plugin-area-zone\\/edit\":\"1\",\"plugin-area-zone\\/delete\":\"1\",\"plugin-area-street\\/index\":\"1\",\"plugin-area-street\\/add\":\"1\",\"plugin-area-street\\/edit\":\"1\",\"plugin-area-street\\/delete\":\"1\",\"plugin-marketing-rmb_getout\\/index\":\"1\",\"plugin-marketing-rmb_getout\\/delete\":\"1\",\"plugin-marketing-rmb_infull\\/index\":\"1\",\"plugin-marketing-rmb_infull\\/delete\":\"1\",\"plugin-marketing-rmb_consume\\/index\":\"1\",\"plugin-marketing-rmb_consume\\/delete\":\"1\",\"plugin-marketing-moneylog\\/index\":\"1\",\"plugin-marketing-moneylog\\/delete\":\"1\"}');
INSERT INTO `qb_group` VALUES ('11','0','VIP会员','5000','a:11:{s:14:\"AllowUploadMax\";s:1:\"0\";s:10:\"upfileType\";s:0:\"\";s:13:\"upfileMaxSize\";s:0:\"\";s:14:\"consumptionMin\";s:2:\"10\";s:10:\"GetCashMin\";s:2:\"10\";s:12:\"GetCashScale\";s:2:\"10\";s:11:\"AllowMakeHy\";s:1:\"1\";s:15:\"AllowMakeMoreHy\";s:1:\"0\";s:13:\"AllowUesStyle\";s:1:\"0\";s:18:\"AllowUesPicMsgSort\";s:1:\"1\";s:13:\"postHyNewsNum\";s:4:\"5000\";}','0',NULL);
INSERT INTO `qb_group` VALUES ('8','0','普通会员','0','a:80:{s:10:\"upfileType\";s:0:\"\";s:13:\"upfileMaxSize\";s:0:\"\";s:14:\"PassContribute\";s:1:\"1\";s:13:\"EditPassPower\";s:1:\"0\";s:17:\"SearchArticleType\";s:1:\"1\";s:12:\"SetTileColor\";s:1:\"0\";s:14:\"SetSellArticle\";s:1:\"0\";s:13:\"SetSmallTitle\";s:1:\"0\";s:17:\"SetSpecialArticle\";s:1:\"1\";s:17:\"SetArticleKeyword\";s:1:\"1\";s:20:\"AddArticleKeywordNum\";s:1:\"0\";s:16:\"PostArticleYzImg\";s:1:\"0\";s:21:\"AddArticleCopyfromNum\";s:1:\"0\";s:16:\"SelectArticleTpl\";s:1:\"0\";s:13:\"SetArticleTpl\";s:1:\"0\";s:18:\"SelectArticleStyle\";s:1:\"0\";s:18:\"SetArticlePosttime\";s:1:\"0\";s:18:\"SetArticleViewtime\";s:1:\"0\";s:16:\"SetArticleHitNum\";s:1:\"0\";s:18:\"SetArticlePassword\";s:1:\"0\";s:19:\"SetArticleDownGroup\";s:1:\"0\";s:19:\"SetArticleViewGroup\";s:1:\"0\";s:17:\"SetArticleJumpurl\";s:1:\"0\";s:19:\"SetArticleIframeurl\";s:1:\"0\";s:21:\"SetArticleDescription\";s:1:\"0\";s:16:\"SetArticleTopCom\";s:1:\"0\";s:17:\"CollectArticleNum\";s:2:\"30\";s:15:\"CreatSpecialNum\";s:1:\"7\";s:19:\"CommentArticleYzImg\";s:1:\"1\";s:11:\"SetHtmlName\";s:1:\"0\";s:7:\"SetVote\";s:1:\"1\";s:16:\"PassContributeSP\";s:1:\"0\";s:13:\"PostNoDelCode\";s:1:\"0\";s:14:\"AllowUploadMax\";s:1:\"0\";s:11:\"comment_num\";s:0:\"\";s:10:\"comment_yz\";s:1:\"0\";s:11:\"comment_img\";s:1:\"0\";s:16:\"sell_postauto_yz\";s:1:\"1\";s:15:\"buy_postauto_yz\";s:1:\"1\";s:15:\"post_pingpai_yz\";s:1:\"1\";s:10:\"use2domain\";s:1:\"0\";s:16:\"useHomepageStyle\";s:1:\"1\";s:21:\"view_buy_view_contact\";s:1:\"0\";s:13:\"post_sell_num\";s:1:\"5\";s:12:\"post_buy_num\";s:1:\"5\";s:13:\"post_news_num\";s:1:\"5\";s:14:\"post_photo_num\";s:2:\"10\";s:11:\"post_hr_num\";s:1:\"5\";s:17:\"post_zhanghui_num\";s:1:\"5\";s:12:\"post_ZLG_num\";s:1:\"0\";s:16:\"post_pingpai_num\";s:1:\"5\";s:19:\"post_baojiadian_num\";s:1:\"5\";s:19:\"post_xunjiadian_num\";s:1:\"5\";s:24:\"post_info_collection_num\";s:2:\"30\";s:18:\"allow_get_homepage\";s:1:\"1\";s:16:\"shop_postauto_yz\";s:1:\"1\";s:14:\"tg_postauto_yz\";s:1:\"1\";s:14:\"post_coupon_yz\";s:1:\"1\";s:13:\"post_shop_num\";s:1:\"5\";s:11:\"post_tg_num\";s:1:\"3\";s:15:\"post_coupon_num\";s:1:\"3\";s:13:\"post_gift_num\";s:1:\"0\";s:18:\"shoptg_postauto_yz\";s:1:\"0\";s:15:\"post_shoptg_num\";s:0:\"\";s:13:\"postNewsYzImg\";s:1:\"1\";s:13:\"view_hy_money\";s:0:\"\";s:13:\"postShopYzImg\";s:1:\"1\";s:11:\"postTgYzImg\";s:1:\"1\";s:15:\"postShopTgYzImg\";s:1:\"1\";s:11:\"postHrYzImg\";s:1:\"1\";s:15:\"postCouponYzImg\";s:1:\"1\";s:10:\"post_hr_yz\";s:1:\"1\";s:14:\"consumptionMin\";s:2:\"10\";s:10:\"GetCashMin\";s:2:\"10\";s:12:\"GetCashScale\";s:2:\"10\";s:11:\"AllowMakeHy\";s:1:\"1\";s:15:\"AllowMakeMoreHy\";s:1:\"0\";s:13:\"AllowUesStyle\";s:1:\"0\";s:13:\"postHyNewsNum\";s:2:\"30\";s:18:\"AllowUesPicMsgSort\";s:1:\"1\";}','0','');
INSERT INTO `qb_group` VALUES ('12','1','普通管理员','0','','1',NULL);
INSERT INTO `qb_hook` VALUES ('1','user_add_begin','新增(注册)用户之前','1','0');
INSERT INTO `qb_hook` VALUES ('2','user_add_end','新增(注册)用户之后','1','0');
INSERT INTO `qb_hook` VALUES ('3','user_edit_begin','用户修改信息之前','1','0');
INSERT INTO `qb_hook` VALUES ('4','user_edit_end','用户修改信息之后','1','0');
INSERT INTO `qb_hook` VALUES ('5','user_delete_begin','删除用户之前','1','0');
INSERT INTO `qb_hook` VALUES ('6','user_delete_end','删除用户之后','1','0');
INSERT INTO `qb_hook` VALUES ('7','upfile_begin','上传文件之前','1','0');
INSERT INTO `qb_hook` VALUES ('8','upfile_end','上传文件之后','1','0');
INSERT INTO `qb_hook` VALUES ('9','cms_add_begin','新发表信息之前','1','0');
INSERT INTO `qb_hook` VALUES ('10','cms_add_end','新发表信息之后','1','0');
INSERT INTO `qb_hook` VALUES ('11','cms_edit_begin','修改信息之前','1','0');
INSERT INTO `qb_hook` VALUES ('12','cms_edit_end','修改信息之后','1','0');
INSERT INTO `qb_hook` VALUES ('13','cms_delete_begin','删除信息之前','1','0');
INSERT INTO `qb_hook` VALUES ('14','cms_delete_end','删除信息之后','1','0');
INSERT INTO `qb_hook` VALUES ('15','user_login_begin','用户登录之前','1','0');
INSERT INTO `qb_hook` VALUES ('16','user_login_end','用户登录之后','1','0');
INSERT INTO `qb_hook` VALUES ('17','user_quit_end','用户退出登录','1','0');
INSERT INTO `qb_hook` VALUES ('18','cms_content_show','内容展示页接口','1','0');
INSERT INTO `qb_hook` VALUES ('19','layout_body_head','前台布局模板头部','1','0');
INSERT INTO `qb_hook` VALUES ('20','layout_body','前台布局模板版权信息之上','1','0');
INSERT INTO `qb_hook` VALUES ('21','layout_body_foot','前台布局模板底部','1','0');
INSERT INTO `qb_hook` VALUES ('22','template_form_head','内容发布页表单模板上面的接口','1','0');
INSERT INTO `qb_hook` VALUES ('23','template_form_foot','内容发布页表单模板下面的接口','1','0');
INSERT INTO `qb_log_action` VALUES ('20','1','1526869213','127.0.0.1','admin','hook','add','','{\"name\":\"layout_body_head\",\"about\":\"\\u524d\\u53f0\\u5e03\\u5c40\\u6a21\\u677f\\u5934\\u90e8\",\"ifopen\":\"1\"}');
INSERT INTO `qb_log_action` VALUES ('21','1','1526869262','127.0.0.1','admin','hook','add','','{\"name\":\"layout_body\",\"about\":\"\\u524d\\u53f0\\u5e03\\u5c40\\u6a21\\u677f\\u7248\\u6743\\u4fe1\\u606f\\u4e4b\\u4e0a\",\"ifopen\":\"1\"}');
INSERT INTO `qb_log_action` VALUES ('22','1','1526869287','127.0.0.1','admin','hook','add','','{\"name\":\"layout_body_foot\",\"about\":\"\\u524d\\u53f0\\u5e03\\u5c40\\u6a21\\u677f\\u5e95\\u90e8\",\"ifopen\":\"1\"}');
INSERT INTO `qb_log_action` VALUES ('23','1','1526869306','127.0.0.1','admin','setting','index','','{\"group\":\"1\",\"webname\":\"\\u9f50\\u535aX1.0\",\"seo_title\":\"\\u9f50\\u535a\\u8457\\u540d\\u7684\\u5f00\\u6e90\\u8f6f\\u4ef6\\u63d0\\u4f9b\\u5546\",\"web_open\":\"1\",\"style\":\"default\",\"member_style\":\"default\",\"admin_style\":\"default\",\"forbid_show_bug\":\"1\",\"remote_updir\":\"upload_files\\/web1\",\"upfileType\":\".rar .txt .jpg .gif .bmp .png .zip .mp3 .wma .wmv .mpeg .mpg .rm .ram .htm .doc .swf .avi .flv .sql ..\",\"close_why\":\"\\u7f51\\u7ad9\\u7ef4\\u62a4\\u5f53\\u4e2d,\\u6682\\u505c\\u8bbf\\u95ee.\",\"miibeian_gov_cn\":\"\\u4eacICP\\u5907050453\\u53f7\",\"MoneyName\":\"\\u91d1\\u5e01\",\"mymd5\":\"d612f19c764a69f3305\",\"MoneyDW\":\"\\u5143\",\"group_expire_data\":\"365\",\"copyright\":\"\\u8054\\u7cfb\\u7535\\u8bdd:020-28998648 @\\u5e7f\\u5dde\\u9f50\\u535a\\u7f51\\u7edc\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8\"}');
INSERT INTO `qb_log_login` VALUES ('3','1','127.0.0.1','1526868885','admin','21232f297a57a5a743894a0e4a801fc3');
INSERT INTO `qb_log_login` VALUES ('4','1','127.0.0.1','1526869218','admin','21232f297a57a5a743894a0e4a801fc3');
INSERT INTO `qb_log_login` VALUES ('5','1','127.0.0.1','1528008805','admin','21232f297a57a5a743894a0e4a801fc3');
INSERT INTO `qb_memberdata` VALUES ('1','2b7234b8149d8cda328f763605e35cfd','a8f49','admin','我是超管','','wf','','3','1','','1','186','8774571','1528008805','127.0.0.1','1528008798','127.0.0.1','1','1890-00-00','uploads/images/20180320/719d42cdf564010d411f2be85c5f47b9.jpeg','fdfdf','','bb@126.com','0','1','cvbnmmm','13399999999','','张学友','a:4:{s:7:\"endtime\";s:0:\"\";s:9:\"alipay_id\";s:6:\"666666\";s:4:\"bank\";s:114:\"62223333333333373 张三 中国工商银行北京**支行\n62284444444919 张三 中国农业银行北京***分行\";s:7:\"pay_pwd\";s:1:\"3\";}','0','0','0','77.90','359.00','e10adc3949ba59abbe56e057f20f883e','22','0','0','0','a:1:{s:6:\"RegMsg\";i:1;}');
INSERT INTO `qb_module` VALUES ('1','','','1','CMS模块','cms','','','0','','','1','0','','','fa fa-fw fa-file-text','0');
INSERT INTO `qb_module` VALUES ('2','','','1','商城','shop','','','-1','','','1','0','','','fa fa-shopping-bag','0');
INSERT INTO `qb_plugin` VALUES ('1','0','log','系统日志','fa fa-fw fa-info-circle','在后台首页显示服务器信息','','','','1.0.0','0','1477757503','1004','1','1','0');
INSERT INTO `qb_plugin` VALUES ('2','0','weixin','微信相关功能','fa fa-fw fa-comments','','','','','1.0.0','0','1477755780','1002','1','0','0');
INSERT INTO `qb_plugin` VALUES ('4','0','config_set','开发者功能','fa fa-fw fa-gears','','','','','','0','0','900','1','1','0');
INSERT INTO `qb_plugin` VALUES ('5','0','marketing','营销功能','fa fa-fw fa-users','','','','','','0','0','100','1','1','0');
INSERT INTO `qb_plugin` VALUES ('6','0','alipay','支付宝接口','fa fa-fw fa-briefcase','','','','','','0','0','100','1','0','0');
INSERT INTO `qb_plugin` VALUES ('7','0','area','城市地区管理','fa fa-fw fa-flag','','','','','','0','0','100','1','1','0');
INSERT INTO `qb_plugin` VALUES ('8','0','comment','评论','fa fa-fw fa-bullhorn','','','','','','0','1517134278','100','1','0','0');
INSERT INTO `qb_plugin` VALUES ('9','0','login','第三方登录接口','fa fa-fw fa-handshake-o','','','','','','0','0','100','1','0','0');
INSERT INTO `qb_plugin` VALUES ('10','0','label','标签管理','fa fa-fw fa-sticky-note-o','','','','','','0','0','100','1','0','0');
INSERT INTO `qb_plugin` VALUES ('11','0','smsali','阿里云短信接口','si si-envelope-letter','','SuiFeng','http://www.php168.com','','1.0','0','0','100','1','1','0');
INSERT INTO `qb_shop_content` VALUES ('3','1','1');
INSERT INTO `qb_shop_content` VALUES ('2','1','1');
INSERT INTO `qb_shop_content1` VALUES ('3','1','1','魅族蓝牙小音箱','1','1','36','1','0','1517981293','1526352423','0','uploads/images/20180302/CnQOjVikE4qAYN55AAMAoDa-ghQ407.jpg','<p><img class=\"lazy\" width=\"1240\" alt=\"\" src=\"https://openfile.meizu.com/group1/M00/01/1E/CnQOjVij_geANlzXAAJ4JdyIZ7g421.jpg\"/><img class=\"lazy\" width=\"1240\" alt=\"\" src=\"https://openfile.meizu.com/group1/M00/01/1E/Cix_s1ij_giAVoRwAALyOhoZT6k135.jpg\"/><img class=\"lazy\" width=\"1240\" alt=\"\" src=\"https://openfile.meizu.com/group1/M00/01/1E/Cix_s1ij_giAcddhAAIAdrNbQ5I909.jpg\"/></p>','100.22','0','0','0','0','0','[\"16G|32\",\"32G\",\"64G|45\"]','[\"红色\",\"白色\"]','[]','30');
INSERT INTO `qb_shop_content1` VALUES ('2','1','1','魅族手机M9最新款隆重上市了','1','1','243','1','0','1516259334','1526352365','0','uploads/images/20180302/Cgbj0FnCGy2AQhMOAA5ZxbK1GIo722.jpg','<p><img class=\"lazy\" width=\"1240\" alt=\"\" src=\"https://openfile.meizu.com/group1/M00/04/1E/Cgbj0VrcdgOAf_cEAAHV1O1wNn4780.jpg\"/><a href=\"https://detail.meizu.com/item/spx.html\" target=\"_blank\"><img class=\"lazy\" width=\"1240\" alt=\"\" src=\"https://openfile.meizu.com/group1/M00/03/86/Cgbj0FqzftuAFRuEAAFDN8UQ57I852.jpg\"/></a><img class=\"lazy\" width=\"1240\" alt=\"\" src=\"https://openfile.meizu.com/group1/M00/04/10/Cgbj0FrcdgOADH3rAAJvD1XrMDg850.jpg\"/><img class=\"lazy\" width=\"1240\" alt=\"\" src=\"https://openfile.meizu.com/group1/M00/04/1E/Cgbj0VrcdgOAV3G7AAVRP964D3A280.jpg\"/><img class=\"lazy\" width=\"1240\" alt=\"\" src=\"https://openfile.meizu.com/group1/M00/04/10/Cgbj0FrcdgWAbRd5AAFG0cWJKug303.jpg\"/><img class=\"lazy\" width=\"1240\" alt=\"\" src=\"https://openfile.meizu.com/group1/M00/04/10/Cgbj0FrcdgWAZNLyAAHNcu8f9V8672.jpg\"/><img class=\"lazy\" width=\"1240\" alt=\"\" src=\"https://openfile.meizu.com/group1/M00/04/1E/Cgbj0VrcdgWAMuu2AAVk0BJBeg4924.jpg\"/><img class=\"lazy\" width=\"1240\" alt=\"\" src=\"https://openfile.meizu.com/group1/M00/04/1E/Cgbj0VrcdgaAPMMqAAJ5D3ohZpM015.jpg\"/></p>','2020.02','0','0','0','0','0','[\"大份|10\",\"中份|20\",\"小份|40\"]','[\"XX\",\"XL\",\"XXXL\"]','[\"红\",\"黄\",\"蓝\"]','30');
INSERT INTO `qb_shop_field` VALUES ('10','title','商品名称','text','varchar(256) NOT NULL','','','','0','1','','','','','','2','','','','100','1','1','1','');
INSERT INTO `qb_shop_field` VALUES ('11','picurl','商品介绍图','images','text NOT NULL','','','','0','1','','','','','','2','','','','98','0','0','0','');
INSERT INTO `qb_shop_field` VALUES ('12','content','商品介绍','ueditor','text NOT NULL','','','','0','1','','','','','','2','','','','0','0','0','0','');
INSERT INTO `qb_shop_field` VALUES ('47','price','商品价格','money','decimal(10, 2 ) UNSIGNED NOT NULL','','','','0','1','','','','','','2','','','','99','0','0','0','');
INSERT INTO `qb_shop_field` VALUES ('48','type1','型号','array','varchar(255) NOT NULL','','','','0','1','','','','','','2','','','','0','0','0','0','商品属性');
INSERT INTO `qb_shop_field` VALUES ('49','type2','尺寸','array','varchar(255) NOT NULL','','','','0','1','','','','','','2','','','','0','0','0','0','商品属性');
INSERT INTO `qb_shop_field` VALUES ('50','type3','颜色','array','varchar(255) NOT NULL','','','','0','1','','','','','','2','','','','0','0','0','0','商品属性');
INSERT INTO `qb_shop_module` VALUES ('1','','商品模型','','','100','1515221331','0');
INSERT INTO `qb_shop_sort` VALUES ('1','0','1','数码产品','0','','','','','','','');
INSERT INTO `qb_shop_sort` VALUES ('2','0','1','家居用品','0','','','','','','','');
INSERT INTO `qb_webmenu` VALUES ('1','0','1','网上商城','/index.php/shop/','0','1','100','','shop');
INSERT INTO `qb_webmenu` VALUES ('24','0','1','图片专栏','/index.php/cms/list-14.html','0','1','0','','cms-14');
INSERT INTO `qb_webmenu` VALUES ('23','0','1','娱乐新闻','/index.php/cms/list-9.html','0','1','10','','cms-9');
INSERT INTO `qb_webmenu` VALUES ('22','0','1','联系我们','/index.php/page/8.html','0','1','0','','index-alonepage8');
INSERT INTO `qb_webmenu` VALUES ('18','0','1','新闻资讯','/index.php/cms/','0','1','11','','cms-');
INSERT INTO `qb_webmenu` VALUES ('21','0','1','网站首页','/index.php','0','1','101','','index-');
INSERT INTO `qb_webmenu` VALUES ('26','0','3','我的圈子','/index.php/qun/content/my.html','0','1','10','glyphicon glyphicon-star','');
INSERT INTO `qb_webmenu` VALUES ('27','0','3','圈子广场','/index.php/qun/index/index.html','0','1','9','glyphicon glyphicon-bullhorn','');
INSERT INTO `qb_webmenu` VALUES ('28','0','3','创建圈子','/index.php/qun/content/add.html','0','1','8','glyphicon glyphicon-plus','');
INSERT INTO `qb_webmenu` VALUES ('29','0','3','会员中心','/member.php/member/index.html','0','1','0','si si-user','');
INSERT INTO `qb_webmenu` VALUES ('30','0','2','网站首页','/index.php','0','1','0','','');
INSERT INTO `qb_weixinmenu` VALUES ('1','0','0','菜单一','','http://www.php168.com','0','0','0');
INSERT INTO `qb_weixinmenu` VALUES ('2','0','0','菜单二','','http://www.php168.com','0','0','0');
INSERT INTO `qb_weixinmenu` VALUES ('3','0','0','菜单三','','http://www.php168.com','0','0','0');
INSERT INTO `qb_weixinmenu` VALUES ('4','0','1','子菜单','','http://www.php168.com','0','0','0');
INSERT INTO `qb_weixinword` VALUES ('2','价格','门户系统价格分别是6800元、9500元，分类系统价格分类别3500元、4500元等','10','0');
INSERT INTO `qb_weixinword` VALUES ('3','产品 商品','我们的产品有地方门户系统，CMS系统，B2B电子商务系统，分类信息系统等','11','0');
