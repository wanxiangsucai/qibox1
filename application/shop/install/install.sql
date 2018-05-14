INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES('', -1, 'SEO标题', 'mseo_title', '', 'text', '', 0, '', '', 100, 4);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES('', -1, 'SEO优化关键字keywords', 'mseo_keyword', '', 'text', '', 0, '', '', 99, 4);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES('', -1, 'SEO优化描述description', 'mseo_description', '', 'text', '', 0, '', '', 98, 4);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES('', -1, '是否开启当前模块', 'is_open_modlue', '1', 'radio', '1|开启\r\n0|关闭', 0, '', '', 97, 4);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES('', -1, '允许发布内容的用户组', 'can_post_group', '', 'checkbox', 'app\\common\\model\\Group@getTitleList@[{"id":["<>",2]}]', 0, '', '', 96, 4);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES('', -1, '发布内容自动通过审核的用户组', 'post_auto_pass_group', '', 'checkbox', 'app\\common\\model\\Group@getTitleList@[{"id":["<>",2]}]', 0, '', '', 95, 4);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES('', -1, '内容被设为精华奖励积分个数', 'com_info_add_money', '', 'text', '', 0, '', '', 94, 4);



DROP TABLE IF EXISTS `qb_shop_car`;
CREATE TABLE IF NOT EXISTS `qb_shop_car` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='购物车' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `qb_shop_car`
--

INSERT INTO `qb_shop_car` (`id`, `shopid`, `type1`, `type2`, `type3`, `num`, `create_time`, `update_time`, `ifchoose`, `uid`) VALUES(1, 3, 3, 1, 0, 8, 1521779315, 1521780887, 1, 5);

-- --------------------------------------------------------

--
-- 表的结构 `qb_shop_content`
--

DROP TABLE IF EXISTS `qb_shop_content`;
CREATE TABLE IF NOT EXISTS `qb_shop_content` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `uid` int(8) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='内容索引表' AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `qb_shop_content`
--

INSERT INTO `qb_shop_content` (`id`, `mid`, `uid`) VALUES(3, 1, 1);
INSERT INTO `qb_shop_content` (`id`, `mid`, `uid`) VALUES(2, 1, 1);
INSERT INTO `qb_shop_content` (`id`, `mid`, `uid`) VALUES(4, 1, 1);
INSERT INTO `qb_shop_content` (`id`, `mid`, `uid`) VALUES(5, 1, 1);
INSERT INTO `qb_shop_content` (`id`, `mid`, `uid`) VALUES(6, 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `qb_shop_content1`
--

DROP TABLE IF EXISTS `qb_shop_content1`;
CREATE TABLE IF NOT EXISTS `qb_shop_content1` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='商品内容表' AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `qb_shop_content1`
--

INSERT INTO `qb_shop_content1` (`id`, `mid`, `fid`, `title`, `ispic`, `uid`, `view`, `status`, `replynum`, `create_time`, `update_time`, `list`, `picurl`, `content`, `price`, `province_id`, `city_id`, `zone_id`, `street_id`, `ext_sys`, `type1`, `type2`, `type3`, `ext_id`) VALUES(3, 1, 1, '魅族手机M9最新款', 1, 1, 25, 1, 0, 1517981293, 1523952575, 0, 'uploads/images/20180208/c30043460273133c77ddb0ccc93d7f9d.jpg', '<p>这是手机哦</p>', '10.22', 0, 0, 0, 0, 0, '["16G|32","32G","64G|45"]', '["红色","白色"]', '[]', 30);
INSERT INTO `qb_shop_content1` (`id`, `mid`, `fid`, `title`, `ispic`, `uid`, `view`, `status`, `replynum`, `create_time`, `update_time`, `list`, `picurl`, `content`, `price`, `province_id`, `city_id`, `zone_id`, `street_id`, `ext_sys`, `type1`, `type2`, `type3`, `ext_id`) VALUES(2, 1, 1, '女人裤子最新款', 1, 1, 234, 1, 0, 1516259334, 1521098081, 0, 'uploads/images/20180315/bde984aaef393d496a50d3847282f7b5.jpg', '<p>王猛不避讳自己性格的“弱点”：“内向，敏感，不善交际”。他认为，这正与父母有关，“我母亲一直倾向于把我关在家里，按自己的喜好包办事情。”</p><p>王猛至今记忆深刻的一件事发生在小学一二年级——班里文艺演出要求穿齐膝短裤。“母亲却不由分说让我穿长裤，我提出带上短裤备用也没被准许。”王猛说，从小到大几乎所有的衣服都是按照父母的意愿和审美来置办的，几乎没有一次是按照自己的意愿来进行选择的。</p><p>王猛五六年级时，对奥数很有感觉，一开始母亲并不乐意让他去。有次参加奥数考试，携带的文件夹不见了，找回后发现被划坏并涂抹，“回到家后，母亲不但没有安慰我，反而说‘这下你知道外面的世界很精彩了吧’！”</p><p>高中毕业前，所有的社交圈子几乎都在生活的大院里，“朋友，都是父母认识、了解或者听过的”。高中时，王猛曾强烈要求到外面的学校上学，但遭到父母的拒绝。尽管后来考上北大，也因社交障碍很难与人交往。</p><p><br/></p>', '20.02', 0, 0, 0, 0, 0, '["大份|10","中份|20","小份|40"]', '["XX","XL","XXXL"]', '["红","黄","蓝"]', 30);
INSERT INTO `qb_shop_content1` (`id`, `mid`, `fid`, `title`, `ispic`, `uid`, `view`, `status`, `replynum`, `create_time`, `update_time`, `list`, `picurl`, `content`, `price`, `province_id`, `city_id`, `zone_id`, `street_id`, `ext_sys`, `type1`, `type2`, `type3`, `ext_id`) VALUES(4, 1, 1, 'test', 0, 1, 0, 1, 0, 1523498601, 1523952604, 0, '', '<p>testsssssssss<br/></p>', '0.00', 0, 0, 0, 0, 0, '', '', '', 0);
INSERT INTO `qb_shop_content1` (`id`, `mid`, `fid`, `title`, `ispic`, `uid`, `view`, `status`, `replynum`, `create_time`, `update_time`, `list`, `picurl`, `content`, `price`, `province_id`, `city_id`, `zone_id`, `street_id`, `ext_sys`, `type1`, `type2`, `type3`, `ext_id`) VALUES(5, 1, 1, 'fdasfd', 0, 1, 2, 1, 0, 1523498648, 0, 0, '', '<p>afdsafds</p>', '0.00', 0, 0, 0, 0, 0, '', '', '', 0);
INSERT INTO `qb_shop_content1` (`id`, `mid`, `fid`, `title`, `ispic`, `uid`, `view`, `status`, `replynum`, `create_time`, `update_time`, `list`, `picurl`, `content`, `price`, `province_id`, `city_id`, `zone_id`, `street_id`, `ext_sys`, `type1`, `type2`, `type3`, `ext_id`) VALUES(6, 1, 1, 'efdsa', 0, 1, 6, 1, 0, 1523501487, 0, 0, '', '<p>fdsafds</p>', '0.00', 0, 0, 0, 0, 0, '', '', '', 0);

-- --------------------------------------------------------

--
-- 表的结构 `qb_shop_field`
--

DROP TABLE IF EXISTS `qb_shop_field`;
CREATE TABLE IF NOT EXISTS `qb_shop_field` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '字段名称',
  `name` varchar(32) NOT NULL,
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '字段标题',
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='文档字段表' AUTO_INCREMENT=51 ;

--
-- 转存表中的数据 `qb_shop_field`
--

INSERT INTO `qb_shop_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(10, 'title', '商品名称', 'text', 'varchar(256) NOT NULL', '', '', '', 0, 1, '', '', '', '', '', 2, '', '', '', 100, 1, 1, 1, '');
INSERT INTO `qb_shop_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(11, 'picurl', '商品介绍图', 'images', 'text NOT NULL', '', '', '', 0, 1, '', '', '', '', '', 2, '', '', '', 98, 0, 0, 0, '');
INSERT INTO `qb_shop_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(12, 'content', '商品介绍', 'ueditor', 'text NOT NULL', '', '', '', 0, 1, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '');
INSERT INTO `qb_shop_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(47, 'price', '商品价格', 'money', 'decimal(10, 2 ) UNSIGNED NOT NULL', '', '', '', 0, 1, '', '', '', '', '', 2, '', '', '', 99, 0, 0, 0, '');
INSERT INTO `qb_shop_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(48, 'type1', '型号', 'array', 'varchar(255) NOT NULL', '', '', '', 0, 1, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '商品属性');
INSERT INTO `qb_shop_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(49, 'type2', '尺寸', 'array', 'varchar(255) NOT NULL', '', '', '', 0, 1, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '商品属性');
INSERT INTO `qb_shop_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(50, 'type3', '颜色', 'array', 'varchar(255) NOT NULL', '', '', '', 0, 1, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '商品属性');

-- --------------------------------------------------------

--
-- 表的结构 `qb_shop_module`
--

DROP TABLE IF EXISTS `qb_shop_module`;
CREATE TABLE IF NOT EXISTS `qb_shop_module` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(32) NOT NULL DEFAULT '' COMMENT '区分符关键字',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '模型标题',
  `layout` varchar(50) NOT NULL COMMENT '模板路径',
  `icon` varchar(64) NOT NULL,
  `list` int(10) NOT NULL DEFAULT '100' COMMENT '排序',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='模型表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `qb_shop_module`
--

INSERT INTO `qb_shop_module` (`id`, `keyword`, `title`, `layout`, `icon`, `list`, `create_time`, `status`) VALUES(1, '', '商品模型', '', '', 100, 1515221331, 0);

-- --------------------------------------------------------

--
-- 表的结构 `qb_shop_order`
--

DROP TABLE IF EXISTS `qb_shop_order`;
CREATE TABLE IF NOT EXISTS `qb_shop_order` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='商品订单' AUTO_INCREMENT=15 ;

--
-- 转存表中的数据 `qb_shop_order`
--

INSERT INTO `qb_shop_order` (`id`, `order_sn`, `shop`, `shop_uid`, `uid`, `totalmoney`, `shipping_price`, `pay_money`, `user_rmb`, `user_jf`, `create_time`, `pay_time`, `pay_status`, `pay_name`, `linkman`, `address`, `telphone`, `shipping_time`, `receive_time`, `receive_status`, `shipping_status`, `shipping_name`, `shipping_code`, `user_note`, `admin_note`) VALUES(4, 'kqlub2z6wb', '3-1-2-0-0,2-3-2-2-2', 1, 1, '50.22', '0.00', '50.22', '0.00', 0, 1519631489, 0, 0, '', '', '', '', 0, 0, 0, 0, '', '', '', '');
INSERT INTO `qb_shop_order` (`id`, `order_sn`, `shop`, `shop_uid`, `uid`, `totalmoney`, `shipping_price`, `pay_money`, `user_rmb`, `user_jf`, `create_time`, `pay_time`, `pay_status`, `pay_name`, `linkman`, `address`, `telphone`, `shipping_time`, `receive_time`, `receive_status`, `shipping_status`, `shipping_name`, `shipping_code`, `user_note`, `admin_note`) VALUES(3, 'hdy36zdk4a', '3-2-2-0-0', 1, 1, '10.22', '0.00', '10.22', '0.00', 0, 1519631402, 0, 0, '', '张三', '', '13585544', 0, 0, 0, 0, '', '', '', '');
INSERT INTO `qb_shop_order` (`id`, `order_sn`, `shop`, `shop_uid`, `uid`, `totalmoney`, `shipping_price`, `pay_money`, `user_rmb`, `user_jf`, `create_time`, `pay_time`, `pay_status`, `pay_name`, `linkman`, `address`, `telphone`, `shipping_time`, `receive_time`, `receive_status`, `shipping_status`, `shipping_name`, `shipping_code`, `user_note`, `admin_note`) VALUES(5, 'g1um11j3ep', '3-1-2-0-0,2-3-2-2-2', 1, 1, '50.22', '0.00', '50.22', '0.00', 0, 1519633419, 0, 0, '', '', '', '', 0, 0, 0, 0, '', '', '', '');
INSERT INTO `qb_shop_order` (`id`, `order_sn`, `shop`, `shop_uid`, `uid`, `totalmoney`, `shipping_price`, `pay_money`, `user_rmb`, `user_jf`, `create_time`, `pay_time`, `pay_status`, `pay_name`, `linkman`, `address`, `telphone`, `shipping_time`, `receive_time`, `receive_status`, `shipping_status`, `shipping_name`, `shipping_code`, `user_note`, `admin_note`) VALUES(6, '1os5nkh7bf', '3-1-2-0-0,2-3-2-2-2', 1, 1, '50.22', '0.00', '50.22', '0.00', 0, 1519633465, 0, 0, '', '', '', '', 0, 0, 0, 0, '', '', '', '');
INSERT INTO `qb_shop_order` (`id`, `order_sn`, `shop`, `shop_uid`, `uid`, `totalmoney`, `shipping_price`, `pay_money`, `user_rmb`, `user_jf`, `create_time`, `pay_time`, `pay_status`, `pay_name`, `linkman`, `address`, `telphone`, `shipping_time`, `receive_time`, `receive_status`, `shipping_status`, `shipping_name`, `shipping_code`, `user_note`, `admin_note`) VALUES(7, 'w4z3313pe5', '3-1-2-0-0,2-3-2-2-2', 1, 1, '50.22', '0.00', '50.22', '0.00', 0, 1519633593, 0, 0, '', '', '', '', 0, 0, 0, 0, '', '', '', '');
INSERT INTO `qb_shop_order` (`id`, `order_sn`, `shop`, `shop_uid`, `uid`, `totalmoney`, `shipping_price`, `pay_money`, `user_rmb`, `user_jf`, `create_time`, `pay_time`, `pay_status`, `pay_name`, `linkman`, `address`, `telphone`, `shipping_time`, `receive_time`, `receive_status`, `shipping_status`, `shipping_name`, `shipping_code`, `user_note`, `admin_note`) VALUES(8, '001ea0616d', '3-2-1-1-1,2-4-1-1-1', 1, 5, '30.22', '0.00', '30.22', '0.00', 0, 1521192321, 0, 0, '', '张三69999', '市桥103号3', '13654545454', 0, 0, 0, 0, '', '', '', '');
INSERT INTO `qb_shop_order` (`id`, `order_sn`, `shop`, `shop_uid`, `uid`, `totalmoney`, `shipping_price`, `pay_money`, `user_rmb`, `user_jf`, `create_time`, `pay_time`, `pay_status`, `pay_name`, `linkman`, `address`, `telphone`, `shipping_time`, `receive_time`, `receive_status`, `shipping_status`, `shipping_name`, `shipping_code`, `user_note`, `admin_note`) VALUES(9, 'd1f1b3ddf7', '3-2-1-1-1,2-4-1-1-1', 1, 5, '30.22', '0.00', '30.22', '0.00', 0, 1521193985, 0, 0, '', '李四88', '天河区123号', '18698545212', 0, 0, 0, 0, '', '', '', '');
INSERT INTO `qb_shop_order` (`id`, `order_sn`, `shop`, `shop_uid`, `uid`, `totalmoney`, `shipping_price`, `pay_money`, `user_rmb`, `user_jf`, `create_time`, `pay_time`, `pay_status`, `pay_name`, `linkman`, `address`, `telphone`, `shipping_time`, `receive_time`, `receive_status`, `shipping_status`, `shipping_name`, `shipping_code`, `user_note`, `admin_note`) VALUES(10, 'e78456b589', '3-2-1-1-1,2-4-1-1-1', 1, 5, '30.22', '0.00', '30.22', '0.00', 0, 1521194239, 0, 0, '', '李四88', '天河区123号', '18698545212', 0, 0, 0, 0, '', '', '', '');
INSERT INTO `qb_shop_order` (`id`, `order_sn`, `shop`, `shop_uid`, `uid`, `totalmoney`, `shipping_price`, `pay_money`, `user_rmb`, `user_jf`, `create_time`, `pay_time`, `pay_status`, `pay_name`, `linkman`, `address`, `telphone`, `shipping_time`, `receive_time`, `receive_status`, `shipping_status`, `shipping_name`, `shipping_code`, `user_note`, `admin_note`) VALUES(11, '0bb7cba27c', '3-2-1-1-1,2-4-1-1-1', 1, 5, '30.22', '0.00', '30.22', '0.00', 0, 1521195905, 0, 0, '', '李四88', '天河区123号', '18698545212', 0, 0, 0, 0, '', '', '', '');
INSERT INTO `qb_shop_order` (`id`, `order_sn`, `shop`, `shop_uid`, `uid`, `totalmoney`, `shipping_price`, `pay_money`, `user_rmb`, `user_jf`, `create_time`, `pay_time`, `pay_status`, `pay_name`, `linkman`, `address`, `telphone`, `shipping_time`, `receive_time`, `receive_status`, `shipping_status`, `shipping_name`, `shipping_code`, `user_note`, `admin_note`) VALUES(12, 'eda3dfc0dc', '3-2-1-1-1,2-4-1-1-1', 1, 5, '30.22', '0.00', '30.22', '0.00', 0, 1521196062, 0, 0, '', '李四88', '天河区123号', '18698545212', 0, 0, 0, 0, '', '', '', '');
INSERT INTO `qb_shop_order` (`id`, `order_sn`, `shop`, `shop_uid`, `uid`, `totalmoney`, `shipping_price`, `pay_money`, `user_rmb`, `user_jf`, `create_time`, `pay_time`, `pay_status`, `pay_name`, `linkman`, `address`, `telphone`, `shipping_time`, `receive_time`, `receive_status`, `shipping_status`, `shipping_name`, `shipping_code`, `user_note`, `admin_note`) VALUES(13, '3febf9e176', '3-2-1-1-1', 1, 5, '10.22', '0.00', '10.22', '0.00', 0, 1521196820, 0, 0, '', '李四88', '天河区123号', '18698545212', 0, 0, 0, 0, '', '', '', '');
INSERT INTO `qb_shop_order` (`id`, `order_sn`, `shop`, `shop_uid`, `uid`, `totalmoney`, `shipping_price`, `pay_money`, `user_rmb`, `user_jf`, `create_time`, `pay_time`, `pay_status`, `pay_name`, `linkman`, `address`, `telphone`, `shipping_time`, `receive_time`, `receive_status`, `shipping_status`, `shipping_name`, `shipping_code`, `user_note`, `admin_note`) VALUES(14, '43b8404376', '2-1-1-1-1', 1, 5, '20.00', '0.00', '20.00', '0.00', 0, 1521528492, 0, 0, '', '张三69999', '市桥103号3', '13654545454', 0, 0, 0, 0, '', '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `qb_shop_sort`
--

DROP TABLE IF EXISTS `qb_shop_sort`;
CREATE TABLE IF NOT EXISTS `qb_shop_sort` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL,
  `mid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `name` varchar(50) NOT NULL,
  `list` int(10) NOT NULL,
  `logo` varchar(50) NOT NULL COMMENT '封面图',
  `template` varchar(255) NOT NULL COMMENT '模板',
  `allowpost` varchar(100) NOT NULL COMMENT '允许发布信息的用户组',
  `allowview` varchar(100) NOT NULL COMMENT '允许浏览内容的用户组',
  `seo_title` int(100) NOT NULL COMMENT 'SEO标题',
  `seo_keywords` int(100) NOT NULL COMMENT 'SEO关键字',
  `seo_description` int(150) NOT NULL COMMENT 'SEO描述',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `pid` (`pid`),
  KEY `list` (`list`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='主栏目表' AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `qb_shop_sort`
--

INSERT INTO `qb_shop_sort` (`id`, `pid`, `mid`, `name`, `list`, `logo`, `template`, `allowpost`, `allowview`, `seo_title`, `seo_keywords`, `seo_description`) VALUES(1, 0, 1, '数码产品', 0, '', '', '', '', 0, 0, 0);
INSERT INTO `qb_shop_sort` (`id`, `pid`, `mid`, `name`, `list`, `logo`, `template`, `allowpost`, `allowview`, `seo_title`, `seo_keywords`, `seo_description`) VALUES(2, 0, 1, '家居用品', 0, '', '', '', '', 0, 0, 0);
