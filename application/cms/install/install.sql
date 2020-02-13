INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, -1, 'SEO标题', 'mseo_title', '', 'text', '', 0, '', '', 100, 4);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, -1, 'SEO优化关键字keywords', 'mseo_keyword', '', 'text', '', 0, '', '', 99, 4);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, -1, 'SEO优化描述description', 'mseo_description', '', 'text', '', 0, '', '', 98, 4);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, -1, '是否开启当前模块', 'is_open_modlue', '1', 'radio', '1|开启\r\n0|关闭', 0, '', '', 97, 4);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, -1, '允许发布内容的用户组', 'can_post_group', '', 'checkbox', 'app\\common\\model\\Group@getTitleList@[{"id":["<>",2]}]', 0, '', '', 96, 4);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, -1, '发布内容自动通过审核的用户组', 'post_auto_pass_group', '', 'checkbox', 'app\\common\\model\\Group@getTitleList@[{"id":["<>",2]}]', 0, '', '', 95, 4);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, -1, '内容被设为精华奖励积分个数', 'com_info_add_money', '', 'text', '', 0, '', '', 94, 4);

INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(0, -1, '可以免费浏览收费内容的用户组', 'group_view_pay', '3', 'usergroup2', '', 0, '', '', 0, 1);



-- --------------------------------------------------------

--
-- 表的结构 `qb_cms_category`
--

DROP TABLE IF EXISTS `qb_cms_category`;
CREATE TABLE IF NOT EXISTS `qb_cms_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `list` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `list` (`list`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='辅栏目' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `qb_cms_content`
--

DROP TABLE IF EXISTS `qb_cms_content`;
CREATE TABLE IF NOT EXISTS `qb_cms_content` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `mid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `uid` int(8) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='内容索引表' AUTO_INCREMENT=20 ;

--
-- 转存表中的数据 `qb_cms_content`
--

INSERT INTO `qb_cms_content` (`id`, `mid`, `uid`) VALUES
(12, 1, 1),
(13, 1, 1),
(14, 1, 1),
(15, 1, 1),
(16, 1, 1),
(17, 1, 1),
(18, 2, 1),
(19, 2, 1);

-- --------------------------------------------------------

--
-- 表的结构 `qb_cms_content1`
--

DROP TABLE IF EXISTS `qb_cms_content1`;
CREATE TABLE IF NOT EXISTS `qb_cms_content1` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='文章模型模型表' AUTO_INCREMENT=36 ;

--
-- 转存表中的数据 `qb_cms_content1`
--

INSERT INTO `qb_cms_content1` (`id`, `mid`, `fid`, `title`, `ispic`, `uid`, `view`, `status`, `agree`, `replynum`, `create_time`, `update_time`, `list`, `picurl`, `content`, `province_id`, `city_id`, `zone_id`, `street_id`, `ext_sys`, `ext_id`) VALUES
(12, 1, 10, '曾是阿里旗下的猛将，马云挥泪斩“马谡”，将他送入五年牢狱', 1, 1, 35, 1, 0, 0, 1517533498, 1519974088, 0, 'uploads/images/20180302/acc5222477f61f1791a5f1c3e1029a33.jpg', '<p style="text-align: left;">细数中国商业史，在某个时刻被命运抛入深谷，或一夜之间锒铛入狱者不在少数，但能经历成功——失败——再成功三部曲者寥寥无几。马云曾梦想着阿里“猛将如云,美女如潮”,但人多了最难管。阿里巴巴是个大江湖，马云按员工入职先后定工牌编号。阎利珉和他带领的聚划算员工，都是80后新人，这些员工是整个阿里巴巴集团和旗下子公司最拼命、最辛苦、最具有创新能力的一群人。</p><p style="text-align:center"><img src="http://p1.pstatp.com/large/46de0005a4f54342df06" alt="曾是阿里旗下的猛将，马云挥泪斩“马谡”，将他送入五年牢狱"/></p><p>阎利珉2002年毕业于四川大学，拥有八年的互联网软件和电子商务的从业经验，2006年加入阿里巴巴。2009年其所在业务板块并入淘宝，其后便在阿里实现了”火箭式上升”，主要依靠的是两大业绩：一是推动了淘江湖的建立，第二发起了聚划算项目，这两个项目都被阿里巴巴内部评为&quot;金旺旺奖&quot;。</p><p>阎利珉在加入阿里巴巴之后，一开始是做产品经理，后来转做运营，这种大跨度的转型也让他的能力越来越全面。阿里内部人士评价称，阿里能出阎利珉这样的人不多，真正能做事拿结果的创新型专业人才。</p><p><img src="http://p3.pstatp.com/large/46e000055cb666bbaaf9" alt="曾是阿里旗下的猛将，马云挥泪斩“马谡”，将他送入五年牢狱"/></p><p>自聚划算平台成立后担任平台总监，2011年10月聚划算从淘宝独立出来后，阎利珉出任聚划算总经理。<br/></p><p>背靠淘宝系资源，聚划算发展迅猛。根据团800在2013年6月份的统计数据，聚划算（本地单）成交额6733万元，聚划算（商品单）成交额1.64亿元。业内人士表示，对于参加聚划算平台活动的淘宝网店主来说，能够进入聚划算平台亮相，意味着巨大的流量和购买量的保证。</p><p>但随着聚划算业务的迅速扩张，团队的管理和制度的规范却相对滞后。自2011年年中起，有关聚划算在招商过程中存在不规范、甚至有小二谋取不当利益的举报就接连出现。</p><p><img src="http://p1.pstatp.com/large/46e30002f441db0b1009" alt="曾是阿里旗下的猛将，马云挥泪斩“马谡”，将他送入五年牢狱"/></p><p>阿里巴巴方面介绍，在调查过程中发现，聚划算在制度规范和团队管理上存在很大漏洞，是导致出现上述情况的重要原因。作为聚划算的负责人，阎利珉负有重要管理责任，必须对此负责。阿里巴巴对聚划算“放养”式的管理，带来业绩腾飞，也成为滋生腐败的温床。成为王被捧，败为寇受辱。阎利珉和其团队成了风箱中的老鼠。</p><p>阎利珉爱帮忙，凡有求于他，尽量满足，但不能满足的心生抱怨。聚划算团队中，员工也摸准他的脾气，即便背后和商家达成隐秘利益交换，也有办法通过说法、正常的工作流程让这位年轻老板“批准”。</p><p><img src="http://p3.pstatp.com/large/46e1000548e3fea75179" alt="曾是阿里旗下的猛将，马云挥泪斩“马谡”，将他送入五年牢狱"/></p><p>他先是被阿里巴巴集团宣布免去了总经理职务，后因涉嫌非国家工作人员受贿罪被杭州警方刑事拘留，再后来就是一纸有期徒刑7年的判决书。现如今，曾经风光无限的聚划算早已被并入天猫，而已经走出人生最低谷的阎利珉正准备重新出发，不管怎样，重启不易，祝福之。</p><p><br/></p>', 0, 0, 0, 0, 0, 0),
(13, 1, 12, '死在公司倒闭之前的茅侃侃，和一批从高处坠落的明星创业者们', 0, 1, 12, 1, 0, 0, 1517533576, 1517566429, 0, '', '<p style="text-align: left;">茅侃侃之死</p><p style="text-align: left;">2006年，四个80后创业者登上了罗永浩主持的《对话》节目。央视舞台上的聚光灯让他们一夜爆红。名气随着搜索率一路飙升，让茅侃侃，李想、高燃、戴志康这四个二十出头的男孩兴奋不已。</p><p style="text-align: left;">二十一世纪初，那是个充满可能性的时期，非典之后，万物开始复苏。大家还不认识什么马化腾，张朝阳，年度经纪人物榜上挂着的还是郭广昌，王宪章。</p><p style="text-align: left;">彼时，茅侃侃创立的MaJoy，头顶真人CS业内第一的帽子一路开挂，轰轰烈烈朝着融资的方向驶去；李想的汽车之家，刚挤进国内汽车网站访问量前五名；高燃的P2P流媒体平台Mysee赶上了互联网视频元年的快车，顺风顺水；戴志康的“Discuz！”成为了国内领先的网络社区软件。</p><p style="text-align:center"><img src="http://p1.pstatp.com/large/5b54000271990c0ae1b5" alt="死在公司倒闭之前的茅侃侃，和一批从高处坠落的明星创业者们"/></p><p>茅侃侃</p><p>那时候的茅侃侃是媒体的重点关注对象，媒体一边大肆强调他的初中文凭，一边赞扬着他的才华，把这场不易的创业之旅标榜为一场华丽的逆袭。</p><p>谁也想不到，2018的开年，茅侃侃三个字再次出现在热搜和头条里，带来的却是他煤气自杀的消息。</p><p><img src="http://p1.pstatp.com/large/5b5700000b3005f20359" alt="死在公司倒闭之前的茅侃侃，和一批从高处坠落的明星创业者们"/></p><p>回头再去观望当初被冠以“京城IT四少”的他们：李想带着汽车之家，在2013年12月，敲响了纽交所的大钟，如今已是上亿身价；高燃被免去公司CEO之为之后，MySee也于不久后倒闭。戴志康的“Discuz！”于2010年成功变现，但在把“儿子”交付腾讯之后，老戴曾三个月未曾出门，轻度抑郁。这些曾被捧为80后明星的创业者们，大多正在以各种方式回归平淡。</p><p>茅侃侃是不愿倒下的那个，2013年，创业十年之际，茅侃侃选择了出任GTV（游戏竞技频道）的副总裁，2015年，茅侃侃出任其和万家文化成立的合资公司，万家电竞的CEO。31岁的这年，他希望还能搏一把。但正是这一把，把他带入了无尽的深渊。</p><p>2017，赵薇收购万家文化的闹剧结束，万家电竞成为了牺牲品之一，同年8月，万家文化卖身祥源集团，祥源不愿意接受万家电竞背后的6000万债务。茅侃侃开始了抵押房车的生活。10月，万家电竞员工因为工资拖欠，集体去了朝阳区劳动局进行劳动仲裁。</p><p>2017年的最后一天，茅侃侃发了一条朋友圈：2017失去了所有的所有。这些所有，不仅代表了创业路上的困境，也代表了资本圈的无法控制的风险。他到那个时候都没有放弃努力，付出一切代价苦苦支撑。</p><p>但仅仅一个多月之后，就传来了他自杀的消息。</p><p>有人说他不堪资金压力放弃挣扎，也有人言之凿凿说他被股权交易的黑手玩弄致死，但我想不管怎样，压死骆驼的最后一根稻草，应该是他最终认命凭一己之力真的无法挽救一腔心血的那刻。</p><p>于无声处，茅侃侃带着遗憾归入创业大军溃退的洪流之中，留下一片唏嘘和惋惜。</p><p>被捧上天又摔的无比惨的明星创业者们</p><p>茅侃侃之死，之所以获得那么多人关注，很大原因是因为，他曾经被媒体捧得很高，他的成功失败都在放大镜下一览无遗。茅侃侃不是唯一一个被捧上创业天才宝座的人，也不是唯一一个从聚光灯处摔落的人。有太多被或主动或被动打上追光灯的创业者，在逆流而行的路途中死于船漏，死于巨浪，最终湮灭于路人目送中。</p><p>前些时间，小蓝被叫停，留下一地鸡毛。最终，小蓝单车被滴滴接管。小蓝单车曾是被开启复用最多的共享单车，一度超过膜拜和OFO，如今却只能依附于滴滴发展。但这对于小蓝单车创始人李刚来说，这场创业，从某种程度上来说，到底还是失败了。</p><p><img src="http://p3.pstatp.com/large/5b53000289ddf233db1e" alt="死在公司倒闭之前的茅侃侃，和一批从高处坠落的明星创业者们"/></p><p>小蓝单车创始人：李刚</p><p>我想对于创业者来说，最痛苦的不是倒在血泊中，而是你曾在鲜花掌声中意气风发，如今却只能臣服于强者。李刚在滴滴托管小蓝单车之后坦言：那时候压力很大，可我没有勇气走那最坏的一条路，小蓝单车危机中，我看到了人性中善良的一面，我选择相信希望。</p><p>李刚是幸运的，他没有拧开煤气罐，事情也没有糟到一发不可收拾。</p><p>比起80后，被资本偏爱的90后，创业之路要顺得多。如徐小平一类的投资人，希望通过投资强劲的新鲜血液，获得主流网民的支持。创业大潮如火如荼，方兴未艾，投资人的支持，加上媒体的吹捧，资本开始疯狂押注90后创业者，甚至过分在乎他们的影响力而忽视创业产品和商业模式，这为这一批创业者的覆灭埋下了伏笔。</p><p>2016年，90后创业者尹桑宣布公司解散。尹桑曾是90后创业大军的代表者。他曾赴美读书，又被宾利商学院全奖录取，先前的几次创业也都以成功收尾。2012年，他选择大二辍学回国开发APP“一起唱”，企图颠覆线下KTV。“一起唱”先后获得IDG三次融资，曾入选福布斯。那时的尹桑也被封为90后高材生创业典范，被无数人看好，但没几年，尹桑就遗憾宣布公司因投资人延缓投资，现金流告急，不得不面临员工遣散公司倒闭的结局。</p><p><img src="http://p3.pstatp.com/large/5b550001e81040450cfe" alt="死在公司倒闭之前的茅侃侃，和一批从高处坠落的明星创业者们"/></p><p>尹桑</p><p>不同于茅侃侃的努力无果戛然而止，尹桑的结束倒更像是一场明哲保身触地溜走的滑稽戏。在尹桑发布声明之后，公司员工在知乎上将尹桑推上了风口浪尖。员工集体指责尹桑满篇情怀，丝毫不提员工补偿和工资的裁员公告信，质问尹桑为何不在年前召集员工宣布该消息而是通过邮件遣散员工，更对尹桑解散和禁言员工群的行为十分不满。</p><p>一个匿名的用户po出了尹桑在Facebook上计划年后夏威夷旅游的动态，以及尹桑在发完遣散信还在德州扑克牌桌上的消息。他称自己与尹桑相熟，但并不确认他是否是真的没钱了。</p><p>发布公告信没几天，公司人事给员工发了正式邮件，希望员工主动离职。主动离职信一写，意味着第一员工拿不到N+1的补偿金，第二也拿不到失业保险，无法劳动仲裁。比起茅侃侃变卖家产补齐员工工资，尹桑冷冰冰的一纸算计，于一起创业打拼的团队心里，恐颇有卸磨杀驴的寒心之感。</p><p>尹桑的退场，真的算不上是一场体面的全身而退。</p><p>同样不算体面而退的创业者，还有很多。1990年出生的美女学霸马佳佳，是90后创业者中的绝对话题人物。她毕业于中国传媒大学，传言还是云南省高考语文状元，她是《非诚勿扰》女嘉宾，中欧商学院创业演讲者，两会期间媒体代言人，徐小平曾评价她说，她身上“有中国企业家缺乏的直击心灵的力量”。但也有人指责她逻辑混乱，只会炒作，商业天赋不高。</p><p>太多的光环加身于这个胆大出格的女生身上，她带着情趣用品商店“泡否”在一路非议中横冲直撞，霸占了某一时期的创业板头条。而仅仅2年过去，热度消失的马佳佳，被人发现人气店“泡否”已经倒闭，泡否科技融资状态也在2014年年初再没变化过。</p><p><img src="http://p3.pstatp.com/large/5b550001e8d669dc8e90" alt="死在公司倒闭之前的茅侃侃，和一批从高处坠落的明星创业者们"/></p><p>马佳佳</p><p>2017年10月，网易上线“春风”情趣用品品牌时，曾邀请过马佳佳参与直播。再次回到公众视线的马佳佳微博简介已经变成了”少女实验室“品牌的创始人，她不承认自己的创业之路已断，但没有资本临幸，仅靠标榜“性、美女、成人用品、营销天才”光环的话题女王马佳佳，鲜有媒体愿意再给与她关注度。</p><p>除了马佳佳，被捧杀高坠的还有王凯歆。或许时至今日，很多人都忘了这个名字，但提起神奇百货，还是有人会发出“噢~”的一声，恍然大悟的同时报以轻蔑一笑。王凯歆，1998年出生，创业时候才15岁，17岁就获得天使轮投资，18岁拿到千万A轮，真的是件让人很骄傲的事情。但这个最初做做小生意起家的孩子，在面临千万投资和嗷嗷待哺的团队时，还是显现出了致命的缺陷：经验不足且品质堪忧。</p><p>拿到千万投资的那一年，神奇百货因为盲目扩张、大幅增员，现金流告急。投资人在这个时候发现了王凯歆公司存在数据造假的问题，这还不是最重要的，最致命的是，王凯歆拿着投资人的钱请保姆，买奢侈品，还威胁员工保密，种种心思无一花在公司经营之上。最终，王凯歆被爆出侵吞公款600万，投资人忍无可忍，将其列入死亡名单之中。</p><p>2017年7月，王凯歆被指其新做的保健品微商项目涉嫌诈骗，一天之内，其公众号被迅速封杀。8月，她还为“华茵”健康饮品宣传，但8月之后，再未有过创业相关的消息。</p><p>小小年纪的王凯歆，算是近几年创业者中，谢幕姿势最难看的一位了。</p><p>和生命赛跑的创业之路</p><p>创业之路是残酷的，账上没钱是每个创业者的噩梦。无论是刚刚离去的茅侃侃，还是以上90后中的谁谁谁，这批曾被媒体推上风口浪尖的创业明星们，承受着不同他人的压力，这是省不去的代价。</p><p>在80后创业者身上，我看到了更多的是谨慎和稳重，中产的压力逼着他们无法停下脚步，“被收割”的恐惧支撑着他们必须上岸的决心，他们的试错成本太高了。自杀和猝死的新闻不断传来，但从来没成为他们止步不前的理由。在茅侃侃去世之前，厦门维信科技董事长黄国斌因故去世，时年32岁，重庆游戏界元老，手游开发公司老总冒朝华突然脑溢血，时年37岁。</p><p>在90后创业者身上，我看到更多的是勇气和张扬，以及怎么都要折腾到最后一秒的执着，这不是贬义的评价。无论是因为自身能力不足停止发展也好，负隅顽抗，坚守未遂也好，他们都曾是一个优秀而光荣的创业者，曾在高处享受过注目礼的成功者。</p><p>创业是一场征程，生在这最好也是最坏的时代，如果因为怕有朝一日的高坠，而放弃登高，那才真是比失败落幕还要让人不屑的愚蠢。</p><p><br/></p>', 0, 0, 0, 0, 0, 0),
(14, 1, 12, '茅侃侃昨日在家中自杀 前合作伙伴：太遗憾', 0, 1, 17, 1, 0, 0, 1517533674, 1517566412, 0, 'uploads/images/20180302/acc5222477f61f1791a5f1c3e1029a33.jpg', '<p style="text-align: left;">法制晚报·看法新闻（记者 张蕊 李阳煜）今日上午，有媒体曝出<a href="http://money.163.com/keywords/0/3/00380030540e/1.html" title="80后" target="_blank">80后</a><a href="http://money.163.com/keywords/5/1/521b4e1a/1.html" title="创业" target="_blank">创业</a>代表人物<a href="http://money.163.com/keywords/8/0/83054f834f83/1.html" title="茅侃侃" target="_blank">茅侃侃</a><a href="http://money.163.com/keywords/8/e/81ea6740/1.html" title="自杀" target="_blank">自杀</a>身亡。法制晚报·看法新闻记者经核实，茅侃侃自杀一事属实。</p><p style="text-align:center"><img alt="茅侃侃昨日在家中自杀 前合作伙伴：太遗憾" src="http://cms-bucket.nosdn.127.net/catchpic/c/ca/ca88a9b263f1746f96ccaa058bb135b0.jpg?imageView&thumbnail=550x0"/></p><p>茅侃侃资料图</p><p>据跟茅侃侃公司有交集的人士称，茅侃侃的公司近期遇到很大的经营困难。法制晚报·看法新闻记者采访了一位茅侃侃曾经的合伙人，据其介绍，茅侃侃有时会为了开发“找灵感”而短期失联，对于其自杀身亡，“遗憾，他还是太年轻了”。</p><p>在多家公司担任法人、董事 其中一家公司员工证实自杀</p><p>茅侃侃出生于1983年，被称为80后创业代表人物，曾被媒体和大众狂热追捧。2004年底，茅侃侃开始运营MaJoy，他把网络游戏搬到线下、模仿其后台数据运行，但用实景、由玩家实际扮演。此后，茅侃侃又先后做了移动医疗领域的APP以及提供实时路况信息APP“哪儿堵”；2013年，茅侃侃加入GTV，踏入电竞圈；2015年，茅侃侃与万家文化成立合资公司万家电竞，并出任CEO。</p><p>法制晚报·看法新闻记者查询发现，茅侃侃目前仍是北京万好万家<a href="http://money.163.com/keywords/7/3/75355b507ade6280/1.html" title="电子竞技" target="_blank">电子竞技</a>传媒有限公司和海南万好万家电子竞技传媒有限公司的法人，同时也是多家公司的股东、董事。</p><p>今天上午，法制晚报·看法新闻记者致电其担任法人的北京万好万家电子竞技传媒有限公司和海南万好万家电子竞技传媒有限公司，电话均无人接听。</p><p>而在茅侃侃为董事的北京鸣鹤鸣和文化传媒有限公司中，公司工作人员向记者证实了茅侃侃的死讯。“在北京家中自杀，昨天的事情。”工作人员称，家人发现茅侃侃的时候，其已经去世，没有遗书。</p><p>对于茅侃侃自杀的原因，该人士称不知情。但记者从一名曾经和茅侃侃公司有关交集的相关人士处获悉，茅侃侃的公司经营困难，“前段时间和他们公司的员工聊天时，曾听对方说他们公司目前处于很困难的阶段。”但这究竟是不是导致茅侃侃自杀的原因，目前还没有答案。</p><p><img alt="茅侃侃昨日在家中自杀 前合作伙伴：太遗憾" src="http://cms-bucket.nosdn.127.net/catchpic/c/c6/c6c06abc63db6aba53853bfe9d0ad243.jpg?imageView&thumbnail=550x0"/></p><p>茅侃侃担任董事的一家公司，并没有员工上班，门口贴满小广告 摄/法制晚报·看法新闻记者 李阳煜</p><p>1月25日上午，记者来到位于朝阳区建国路北岸1292三间房创意生活园区的北京鸣鹤鸣和文化传媒有限公司，多次敲门无人应答。其邻居称，该栋楼内有公司也有住户，邻里之间很少交往，偶尔见到有人出入该公司所在处，但从未交流过。记者注意到，该楼层内大多数房间较小，部分用作单身公寓用户租住。</p><p>负责片区送货的某快递公司快递员称，每次给这家公司送快递都是一个女生接电话，“让把快递送到快递站，有人自己去取”。园区物业查询得知，公司所在房间面积有25平米，对于该公司是否租用此地办公，物业称没有去房间看过。</p><p>曾经的合作者：太遗憾，还是太年轻</p><p>10年前，Majoy的茅侃侃、泡泡网的李想、康盛创想的戴志康、Mysee的高燃，4个出生在80年代，20岁出头便有公司，成了当时80后年轻人的创业偶像。</p><p>这时候的茅侃侃应该正值青春年少，踌躇满志。</p><p>许昌（化名）是茅侃侃曾经的合作伙伴，2007年的一个晚上，他在一档电视节目中看到和李想坐在一起侃侃而谈的茅侃侃，“当时电视节目把他定性为怪才，奇才。”看着年纪不大，但一脸沉着的茅侃侃，许昌想，是不是可以和他一起合作开个公司呢？</p><p>第二天，许昌就开始托人找茅侃侃的联系方式，辗转找了很多人，终于联系到了茅侃侃。</p><p>第一次见茅侃侃，许昌觉得和电视上不太一样，“怎么说呢，电视中，他还是比较善谈的，但是私下里其实并不太喜欢说话。”许昌说自己当时想开发一款关于大学生就业指导的软件，就和茅侃侃聊了自己的想法。并说自己想和茅侃侃一起开公司来做这个事情，没想到茅侃侃很爽快的就同意了，“他当时就说，好啊。”</p><p>这让许昌多少有些意外，要知道当时的茅侃侃在互联网界还是挺有名的，接下来的一切就顺理成章了，许昌注册了公司，自己和茅侃侃都是股东，记者注意到，茅侃侃在这家公司的职位是总经理，占45%的股权，“他当时没有钱，注册公司都是我出的钱。”许昌告诉法制晚报·看法新闻记者说，之所以给茅侃侃股份，就是觉得有点儿股份，能让他踏实在公司呆着做事。</p><p>但之后的一切让许昌有些始料未及，按照许昌的说法，他主外，茅侃侃主内，“我当时都联系好了不少单位，就等软件开发出来就可以合作了，但东西迟迟出不了，我也着急。”许昌说，茅侃侃和他的合作最后只持续了一年多，“最后软件一直没开发出来，项目就不了了之了。”</p><p>在许昌的印象当中，茅侃侃是个比较随意的人，这不仅表现在穿着上，性格上、工作上亦是如此，“有时候，他要找灵感，就会一连十几天，二十天不出现，也联系不上。”第一次遇到这样的情况，许昌还紧张了很长时间，但后来，渐渐也就习惯了，许昌将这总结为性格所致。</p><p><br/></p><p><a target="_self"></a></p><p>许昌告诉法制晚报·看法新闻记者，茅侃侃没有上过大学，是自学成才，这一度让许昌特别佩服，所以在当时开公司的时候，许昌也很少过问软件开发的进度，“我也不懂，偶尔问一次，他就说正在弄。”</p><p>那时候的许昌是相信茅侃侃的，因为他相信茅侃侃“怪才”、“奇才”的称谓，“他确实还是比较有才的，但是太年轻了。”</p><p>2009年，软件一直没有开发出来，许昌觉得两人无法继续合作下去了，“忘了当时什么情况，反正就是不联系了。”</p><p>这些年来，许昌没有再关注过茅侃侃，只是偶尔看到他的消息。</p><p>得知茅侃侃自杀的消息，许昌特别震惊，对此他觉得很遗憾，“还是太年轻了。”</p><p><br/></p>', 0, 0, 0, 0, 0, 0),
(15, 1, 10, '茅侃侃之死：成年人的生活里没有容易二字', 1, 1, 14, 1, 0, 0, 1517533725, 1519974065, 0, 'uploads/images/20180302/f4cb941062607e7ece08699e2955eaf3.jpg,uploads/images/20180302/d27a7a45c3fcd543a5a5dc4b1cb22d16.jpg', '<p style="text-align: left;">昨天，有一个80后的创业偶像自杀死了。说实话，作为一个90后，我真的没有听过他，倒是我那个80后的总监一下子就激动的从座位上面炸了起来，大嚎了一句：天啦，茅侃侃居然死了。</p><p style="text-align: left;">出于好奇和尊重，我去百度了这个人的故事。他被誉为第一批 80 后创业者、京城 IT 四少之一。他从小学五年级开始玩电脑，14岁开始在《大众软件》等杂志发表数篇文章。</p><p style="text-align: left;">高一辍学后，他把全部的精力投入到了计算机和英语的学习中，在两个月内的时间里，拿下微软MCP（微软认证专家）、MCSE（微软认证系统工程师）、MCDBA（微软认证数据库管理员）三项认证。</p><p style="text-align: left;">当年在全亚洲18岁拿下三本证书的，只有两人。2006年，在换了一打工作后开始创业的茅侃侃，接受了《中国企业家》的采访，并登上封面，标题为《生于八零年代》。</p><p style="text-align: left;">当时尚在央视工作的罗振宇深受触动，于是邀请了茅侃侃、李想、高燃、戴志康四位80后创业者参加央视的《对谈》节目。节目播出后，第二天，“茅侃侃”一词的搜索，从1000名外直线上升至81名。<br/></p><p style="text-align:center"><img src="http://p1.pstatp.com/large/5b560001bab72d3272ff" alt="茅侃侃之死：成年人的生活里没有容易二字"/></p><p>在“京城IT四少”中，茅侃侃是最小的，他的个人经历和当时的普世价值，冲突也最大。他每月的花销也比其他3位似乎要多一些。“不多，两三万吧。”侃侃说得很轻松，那时他穿着时尚，烫卷的黑发，自曝爱好是逛夜店。</p><p>在网上的照片我看到了这样的一个形象：染着一头黄毛，加上两条大花臂，特别扎眼——看上去“很社会”，一点也不像是那个我想象中的创业家。</p><p>“他可是我中学时的偶像啊，居然那么年轻就自杀了。”我总监一脸的惋惜和无奈。</p><p>而我想起了另外一个人，前不久自杀的韩国艺人金钟铉。据说他是一个人跑到酒店，给姐姐发了条“我好累”的信息，随后点燃了准备好的炭，被人发现送到医院抢救无效后死亡，年仅27岁。而仅仅在十天前，他刚刚结束自己的个人演唱会。</p><p><img src="http://p3.pstatp.com/large/5b5000063314559c49e3" alt="茅侃侃之死：成年人的生活里没有容易二字"/></p><p>直到他的遗书曝光，在很多人意料之中，他的离开是长期遭受抑郁症的困扰。</p><p>在遗书里面有一段话格外让人心痛：</p><blockquote><p>“我从里面开始出了故障。</p><p>一点点啃噬着我的抑郁最终将我吞噬。</p><p>我无法战胜它。</p><p>我厌恶我自己。</p><p>断开的记忆抓住我，不管怎么对自己说要打起来精神来，也找不到答案。”</p></blockquote><p>他们，一个创业大神，一个韩国偶像，在我们普通人的眼里，都是神一样的存在，对我们来说，他们根本不用像我们一样天天思考着茶米油盐，有钱有名有人脉有前途。人生走到这个阶段，已经是完美了。</p><p>可是他们却选择了在三十而立的时间段，选择了离开。</p><p>如果是你，你真的有那样的勇气站上那个最高点吗？你一边在埋怨人生，一边在厌恶社会，可是你有没有想过，当你还年轻的时候，你的确有尝试一切的资本，但是当你不在年轻的时候，你还能做什么呢？</p><p><img src="http://p1.pstatp.com/large/5b53000278c1231becc5" alt="茅侃侃之死：成年人的生活里没有容易二字"/></p><p>我今年25岁，我刚毕业的时候，工资只有2500，在深圳这座城市，我住着500块的床位，和一群做清洁工的阿姨在一个房间，阿姨们每天晚上都很晚睡觉，呼噜声还打得像雷一样，我基本每天都睡不好。每天下班就去超市，买特价的面包，作为第二天的早餐，吃5快钱一份的炒粉。这不是电视剧，这就是我们的生活。</p><p>有一天我回宿舍的时候发现房间被锁住了，开了很久都打不开，后来房东来了，说我的床位到期了，有一个人下午来看房子就立刻租出去了。我拿着我的行李在门口呆住了，那一个瞬间我不知道该怎么办。我以为以前看的偶像剧那种狗血的剧情都是假的，没想到会发生在我自己的身上。</p><p>后来，我找了一个青旅，把身上所有的人都拿了出来，租了半个月。那天晚上，我一个人在回家的那条路上的一个天桥上面，哭了很久。我不知道能找谁，也不想找谁，一个人最彻底的崩溃，就是这样，悄无声息地，毫无生机地默默流泪。</p><p>这让我想起来，以前看过一个热门微博：“现代人的崩溃是一种默不作声的崩溃。看起来很正常，会说笑、会打闹、会社交，表面平静，实际上心里的糟心事已经积累到一定程度了。</p><p>不会摔门砸东西，不会流眼泪或歇斯底里。但可能某一秒突然就积累到极致了，也不说话，也不真的崩溃，也不太想活，也不敢去死。”</p><p>曾经有张动图流传很广，在日本地铁里，有一个男生，坐在那里啃着面包，强忍着委屈，眼泪似乎就要夺眶而出。谁也不知道他发生了什么，但那份心酸，每个人都理解。</p><p><img src="http://p1.pstatp.com/large/5b550001cff6a3d1bca6" alt="茅侃侃之死：成年人的生活里没有容易二字"/></p><p>谁身后都有一堆不可说的故事。但他的那身打扮，给他定位了一个体面的身份。这个身份，让他除了忍住不哭，毫无办法。</p><p>《这个杀手不太冷》里有一句特别著名的台词，马蒂尔德问：“生活是一直这么艰辛，还是只有童年如此。”里昂说：“一直如此。”</p><p><img src="http://p3.pstatp.com/large/5b560001bee30924499e" alt="茅侃侃之死：成年人的生活里没有容易二字"/></p><h1>每个人的生活，从来都是不容易的。</h1><p>三十而立，这将是一道坎。</p><p>这个时间点上，你的职业规划，和你前半生学到的技能，往往会决定着今后数十年一直到你退休那一天的职业方向。</p><p>这个年纪，大部分人都已结婚生子，都已承担起支撑整个家庭的责任。你职场中的每一个决定，都不会再像你年轻时那样轻狂。你的生活如履薄冰，战战兢兢。</p><p>你我都失败不起。</p><p><img src="http://p1.pstatp.com/large/5b54000263c2eacdbfc8" alt="茅侃侃之死：成年人的生活里没有容易二字"/></p><p>成年人的生活，没有容易二字。不容易在，你明明知道，真正的自己，早就被这些社会身份包装到被遗弃，甚至埋葬。但你却没有退路，没有第二个选择，你只能哭着爬着把那些被人寄予厚望的身份扮演下去。</p><p>可每个人，无论爬得多辛苦，演得多艰难，内心深处，还都会有一个微弱到快熄灭的声音，不停地拷问自己：生活的意义到底是什么？是身在红尘的体验，还是看破红尘的顿悟。山的那头，到底有什么？我们只有爬过去才知道。</p><p>我特别喜欢一首歌《What’ up》有句歌词：25年的人生就这样过去了，我仍要努力去翻越那希望的高山，为了让人生有意义。</p><p>我不想说，泥沼总会过去，星辰大海在向你招手。甚至，我都不觉得吃苦是有必要的。但是，找到生活的意义的时刻，恰恰是在，当你熬过去，撑下去后，可以用上帝视角去审视当年那个奋斗到呲牙列嘴的自己的时候。</p><p>人生就真的像爬山一样。山脚下的我们，就是小时候，天真无邪，井底之蛙。爬到半山腰才发现，体力费光，下山已经没有路，还恐高不敢回头看；往上爬，手脚并用都不见得能再挪一步。但是能爬过去的，就是那些，能挺住的人。</p><p>在压力面前，有些人能承受九分，但像我们普通人，光是活着就快花光了所有力气。</p><p>死者为大，愿茅侃侃安息，愿所有的你们都能过的好。</p><p><br/></p>', 0, 0, 0, 0, 0, 0),
(16, 1, 11, '马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！', 1, 1, 19, 1, 0, 0, 1517533788, 1520411501, 0, 'uploads/images/20180302/e04c0b963510abd7a6a75770ac73cfe5.jpg', '<p>大家都知道，阿里巴巴“18罗汉”是阿里草创时的18位创始人，他们当时大都默默无名，跟着屌丝马云创业，一起把阿里做到了今天的辉煌。</p><p>蔡崇信，放下年薪70万美元的工作，跟着马云领月薪500，现在是阿里执行副主席；</p><p><img src="http://p1.pstatp.com/large/5e710003f37705c10d44" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>彭蕾，当年的HR，现在是蚂蚁金服董事长；</p><p><img src="http://p3.pstatp.com/large/5e6f00032791bd8a9271" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>童文红，当年的前台小妹，帮忙订咖啡、做客服，现在是阿里集团资深副总裁兼菜鸟首席运营官；</p><p><img src="http://p1.pstatp.com/large/5e710003f373496ee250" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>除此之外，还有孙彤宇、吴泳铭、盛一飞等人，后来也成为阿里集团的骨干。</p><p>然而，最近马云透露，原来当年那批创始人，曾经被开掉一个！</p><p>到底是谁被开掉了，为什么开掉的？这里面又看出马云什么样的用人哲学？</p><p><img src="http://p3.pstatp.com/large/5b5c0004c48c8ea342bb" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>“阿里只有一个人可以叽叽哇哇”</p><p>在湖畔大学上，马云曾经提过这个人，不说名字，只说他当时的表现：</p><p>他是马云从硅谷带回来的，人也聪明，技术也算不错，在美国待了七八年，他总喜欢叽叽歪歪，干不干就蔫了，问他有什么主意，他也说不出。</p><p>马云抱怨：</p><p>“这个人是我们当时最厉害的，又是18个创始人之一，但永远不做决定，永远叽叽歪歪。”</p><p><img src="http://p3.pstatp.com/large/5e710003f3740fcb9c21" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>后来有人开玩笑：阿里巴巴只能有一个人叽叽哇哇，那就是马云！</p><p>马云本来还想“拯救”他，有一天和他谈事情，一直和他谈到凌晨两点，终于说服他应该怎么办，马云才去睡觉。</p><p>谁知道三点钟不到，时任阿里COO关明生打来电话：“我已经把他开除了！”</p><p>马云惊呆了，我刚和他谈好，你就开除他了？关明生说：“这样对我们好，也对他好，没必要浪费时间。”</p><p><img src="http://p3.pstatp.com/large/5e700003274b9abeed11" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>视频在这里：</p><p>当时，马云还有点不理解，但后来一看，开除他实在太对了！</p><p>阿里“18罗汉”现在很出名，但只是阿里成功后大家编的高帽，当时这群创始人只是“乌合之众”。</p><p>吴晓波就曾很不客气地指出：</p><blockquote><p>“当时只要愿意跟着马云干，愿意每个月只领600块钱工资，就可以成为创始人。”</p></blockquote><p><img src="http://p1.pstatp.com/large/5b5a0005426bf9cd5eb6" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>所以，大家工作都不上心，老员工各自为政，能自己解决就自己解决；新员工来了也不知道干什么，就在工位上假装工作，甚至能假装一周。</p><p>当马云连最出名、官最大的那个创始人都开除了，其他人一下子打起精神了：“真的会开除啊？”</p><p>公司的氛围一下子积极起来，再加上公司架构的完善，阿里巴巴才总算有了一个公司的样子。</p><p><img src="http://p3.pstatp.com/large/5b590005541f701bf437" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>虽说这次开除是关明生主导的，但好好给马云上了一课：</p><p>合适的人就要放在合适的岗位，不合适就赶紧换，拖得越久越麻烦。</p><p>后来几次人员调动，都显示出了马云的用人手段。</p><p>2006年，上任仅40天的雅虎中国总经理谢文辞职，据报道，他为人过于强硬，没法融合雅虎中国内部的派系，所以只能离开；</p><p><img src="http://p3.pstatp.com/large/5e710003f372196c273b" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>谢文</p><p>2007年，阿里二号人物，淘宝创始人孙彤宇被宣布“离岗进修”，他是创业老臣，马云工号是“1”，他的工号是“2”，地位举足轻重。</p><p>据说，他是因为和马云对淘宝的未来的设想不合而离开。</p><p><img src="http://p1.pstatp.com/large/5b5c0004c48d6bc15a96" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>孙彤宇</p><p>2011年，阿里CEO卫哲、COO李旭晖辞职，原因是前一年阿里查出平台有0.8%的“中国供应商”客户涉嫌欺诈，这两人引咎辞职。</p><p>马云发邮件直言：“我们必须刮骨疗毒。”</p><p><img src="http://p3.pstatp.com/large/5e710003f375af6c7026" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>卫哲</p><p>后来，马云曾在电视节目上这么说：</p><p>“我曾经开除过阿里的创始人，课间有人问我，你们在大年三十开除人，的确是有点残忍，我说，开除人，要心好，要刀快。</p><p>有时候开除一个人，对他也是帮助。你离过婚，破过产，进过监狱，还能乐观的面对，这样的人一辈子就没有白活。”</p><p><img src="http://p3.pstatp.com/large/5b590005541e001c699b" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>马云的7大用人原则</p><p>老板们开公司，总爱感叹：千金易得，一将难求。</p><p>员工们找工作，总爱抱怨：怀才不遇，天赋埋没。</p><p>一边想要人才，一边想被赏识，两者之间的复杂关系可见一斑。</p><p>所谓用人，其实只是用在所长，避其所短。那么，作为中国最知名的企业，马云和阿里巴巴究竟是怎么用人的？</p><p>读过了与马云和阿里巴巴相关的大量资料，我们给你总结出七条：</p><p>1、人才不是招来的，而是培养出来的</p><p>上面也说了，阿里巴巴的创始人们，其实都是“乌合之众”，许多都是因为在市场上找不到工作，才被马云招到阿里巴巴。</p><p>但这样一群人，却为阿里巴巴打下了最初的根基，后来他们也在阿里集团身居高位。</p><p>这就是马云的理念：用人的最高境界就是提升人。</p><p>2002年互联网寒冬，阿里巴巴陷入绝境，但马云依然狠砸100万元，为员工办了两个培训班。</p><p><img src="http://p9.pstatp.com/large/5e710003f37650608f86" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>100万元，对当时的阿里来说是一笔不小的开支，但马云坚持：</p><p>“人是最关键的产品，所以，我们要在三年内锻炼我们的队伍。我们盼望着三年内培养出最优秀的互联网员工。”</p><p>马云不仅砸钱请人培训，还自己带着高管去讲课：他和关明生讲价值观、彭蕾讲阿里巴巴的历史、李旭晖和孙彤宇讲产品和销售技巧。</p><p>在这样的培训下，阿里巴巴才从游击队变成正规军，员工也跟着企业一起成长。</p><p>2、少招应届生，多招社会人</p><p>从2015年起，阿里取消了招聘应届毕业生的要求，每年最多不超过300人，这是阿里不同于其他任何大企业的用人法则。</p><p>马云觉得，应届毕业生初入职场，只是一张白纸，虽然标准化、容易管理，但3到5年就会成为管理者，15年后都会成为一样的人。</p><p>与此相比，阿里更喜欢社招，他们更有经验、更有想法，虽然不容易管理，不过碰撞就能出火花。</p><p><img src="http://p3.pstatp.com/large/5e6f000327933119ca57" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>3、永远不找最完美的人</p><p>马云曾说：</p><blockquote><p>“我永远不选最好的员工，只选最合适的员工。选最好的员工是个灾难。</p><p>我喜欢这样的人：他会说，1、I am a man （我是个普通人），我有缺点，但我想努力；2、我有梦想。我讨厌人说 This is a job ...（这只是工作），智商高的人情商一般都低。”</p></blockquote><p>因为员工不完美，你就能指导他、帮助他，让他的成长，带动公司的成长，实现双赢。</p><p>4、知人善用，合适的人放在合适的位置</p><p>马云作为阿里巴巴的创始人，清楚地知道自己的位置，他不需要懂管理、业务、财务，他需要调兵遣将，把合适的人放在合适的位置上。</p><p>马云不懂管理，就请来通用高管关明生担任COO；不懂财务，就找来国际专家蔡崇信当CEO；不懂技术，就从雅虎挖来吴炯担任CTO...</p><p>知道自己和员工的长处和短处，每一个人放在合适的位置上，才能把大家的潜能最大化。</p><p><img src="http://p3.pstatp.com/large/5b5900055420eb358b22" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>5、不要野狗和小白兔</p><p>什么是野狗？业绩很好，每年都能销售出很多产品，但价值观很差，不讲究团队精神，也不讲究质量、服务。</p><p>什么是小白兔？人特别善良，特别热情，但业绩好不起来了。</p><p>这两种人，马云说，一定不能手软，一定要杀。野狗对团队的伤害很大，小白兔根本创造不出价值。</p><p>不过，小白兔离开公司三个月后，还有机会再回来，只要他能把业绩搞上，野狗就没有这个机会了。</p><p><img src="http://p3.pstatp.com/large/5e700003274c851a2312" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>6、用人要疑、疑人要用</p><p>有些人讲究“用人不疑”，有什么工作只订个目标，让手下去做，也不过问过程。</p><p>马云说，这样做是不对的，用人也要疑。</p><p>譬如你在年初给手下定了目标，年末要达到多少目标，到年中时，你也要检查一下进度，这不是不信任，而是监督、鞭策。这就是“用人要疑”。</p><p>如果他的进度不如人意，向你解释了原因，你心存疑惑，但也要让他继续下去，这就是“疑人要用”。</p><p>这才是真正的信任，哪怕你也摸不准他的做法是对是错，也会用他，让他去做。为什么允许自己失败，却不允许别人失败呢？这就是“用人要疑，疑人要用”。</p><p><img src="http://p1.pstatp.com/large/5b5a0005426c26c8a0f2" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>7、保持乐观心态</p><p>很多老板遇到员工出错，就会大发雷霆，事情没解决，自己还伤身体。</p><p>马云觉得，凡是要放轻松，多点经历，就会平淡地看待这个世界，都有好、都有坏，了解人性是什么、世界是什么。</p><p>员工总会出错，关键是如果帮助他成长、修补制度，让类似事情不再发生；</p><p>公司总会遭受挫折，关键是跌倒了如何爬起来，继续前进。</p><p>心态一定要乐观，如果不乐观，人就很容易走偏，公司的氛围也会很糟糕。</p><p><img src="http://p3.pstatp.com/large/5b5c0004c48b59a598cc" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>用人八字诀：</p><p>“惜马用牛赶猪打狗”</p><p>企业用人，其实是重中之重，但怎么用人，各位企业家就头疼了。</p><p>其实，所谓“人”，虽然千差万别，但拿动物做比喻，无外乎“马、牛、猪、狗”四类，因此，有古语云：“惜马用牛赶猪打狗。”</p><p>精髓就是：用其所长，避其所短。</p><p>马云也说：</p><p>“我脸上的每部分拆开来看还可以，但是合起来就那么难看，有的人每个部位都不漂亮，但合在一起就很漂亮了。”</p><p><img src="http://p1.pstatp.com/large/5b59000554212e7bdc67" alt="马云：18罗汉当年曾被开掉一个！马云用人7大原则，老板必看！"/></p><p>找准每个人的位置，让大家都能发挥自己的最大潜质，把一个人的小能量，汇集成所有人的大能量，公司才能迎来长足的进步！</p><p>所以，如果你是企业家，在抱怨底下的人太糟糕时，请扪心自问，你把人用对了吗？你把人放对地方了吗？</p><p>再好的千里马，放在不识货的人手里，也只能终于困在马房之中，郁郁不得志。千里马常有，而伯乐不常有。老板们想要得到人才，自己先做个伯乐吧！</p><p><br/></p>', 0, 0, 0, 0, 0, 0);
INSERT INTO `qb_cms_content1` (`id`, `mid`, `fid`, `title`, `ispic`, `uid`, `view`, `status`, `agree`, `replynum`, `create_time`, `update_time`, `list`, `picurl`, `content`, `province_id`, `city_id`, `zone_id`, `street_id`, `ext_sys`, `ext_id`) VALUES
(17, 1, 9, '李小龙的儿子李国豪究竟是被谁谋杀的？这三个人嫌疑最大！', 1, 1, 80, 1, 0, 0, 1517533867, 1519973979, 0, 'uploads/images/20180302/1cb8ca8badefa49403b56d02365b5246.JPG,uploads/images/20180302/1271aa70880157872ecd4c1cac3f6eca.jpg', '<p style="text-align:center"><img src="http://p1.pstatp.com/large/5e7c000294983680cce9" alt="李小龙的儿子李国豪究竟是被谁谋杀的？这三个人嫌疑最大！"/></p><p>一代功夫巨星李小龙于1973年7月20日死在好友丁佩家中，死前无任何征兆，李小龙的突然死亡让所有人震惊，尽管众说纷纭，但是李小龙的死至今还是一个谜团，其好友丁佩几十年来也遭受了巨大的压力，指责和谩骂如影随形，让她每一天都过的心惊胆战，正当大家都已经对李小龙的死逐渐淡忘的时候，又一爆炸性的消息瞬间出现在了世界各大媒体报刊上，时隔二十年之后，李小龙的儿子李国豪同样突然死亡，而且死的不明不白，这一切还要从李国豪当时正在拍摄的电影《乌鸦》说起。</p><p><img src="http://p3.pstatp.com/large/5e780003390d1a44e98c" alt="李小龙的儿子李国豪究竟是被谁谋杀的？这三个人嫌疑最大！"/></p><p>当时，李国豪正在美国北卡罗莱纳州威尔明顿市的电影片场拍摄《乌鸦》，这是一部枪战剧，而李国豪所饰演的角色在电影中被人一枪毙命，但让李国豪没想到的是他自己本人真的死在了剧组的道具枪下，枪声一响，李国豪应声而倒，鲜血四溅，当大家发现异常时，所有人都吓坏了，于是大家七手八脚的将李国豪抬到医院，可是由于抢救不及时加上受伤太重，李国豪最终抢救无效死亡。</p><p><img src="http://p3.pstatp.com/large/5e830000d9d2a5ae4524" alt="李小龙的儿子李国豪究竟是被谁谋杀的？这三个人嫌疑最大！"/></p><p>李国豪的死和当年李小龙的死马上就被大家联系到了一起，天下难道还会有如此巧合的事情吗？或者这就是命运的安排？一时之间李小龙当年的死因又被挖掘出来，成了当时最大的谜团，后来警方对剧组的道具枪进行了检查，发现其他子弹都是空子弹，而只有射向李国豪身体的那一刻子弹是实弹，于是警方顺藤摸瓜，找出了杀害李国豪嫌疑最大的三个人，他们分别是演员麦西，在剧中正是他向李国豪开枪，很自然他被当成了杀害李国豪的第一嫌疑人，但是麦西却大喊自己冤枉，因为自己在演出之前并没有接触到枪支，他是按照剧情开枪的，他认为其中装的是空子弹，而枪是开拍前剧组道具总管交给他的。所以道具总管很自然的就成了第二嫌疑人。</p><p><img src="http://p3.pstatp.com/large/5e7b0002f8f9d108e83c" alt="李小龙的儿子李国豪究竟是被谁谋杀的？这三个人嫌疑最大！"/></p><p>剧组道具总管同样摸不着头脑，因为在剧组中，他只管保管枪支，而装弹药这种活是由特技人员来干的，所以特技人员就是第三嫌疑人，但特技人员说自己装的都是空子弹，装完后交给了道具总管，按照程序来说，道具总管在收到枪之后应该进行检查，但是由于当时道具总管的粗心，这支枪没被检查就用上了，美国警方排查来排查去，觉得这三个人任何一个都可能暗杀李国豪，到最后也没能理出个头绪，而这场谋杀案就这样不了了之了，其实这三人不一定非要提前将实弹装入枪支，只要其中任何一人准备一颗实弹，李国豪就必死无疑，所有人都忽略了这一点，可能连李国豪自己都没有想到他的仇人会用这种方式来谋杀自己。</p><p><img src="http://p3.pstatp.com/large/5e7a00032b53b07369d0" alt="李小龙的儿子李国豪究竟是被谁谋杀的？这三个人嫌疑最大！"/></p><p>后来警方放过了这三名嫌疑人，当然也放弃了对真凶的追捕，李国豪的死亡就此成为了一个新的谜团，但实际上凶手肯定就是这三人其中的一个，因为剧组在拍戏之前只有这三人接触过枪支，可是因为证据不足，所以只能不了了之，李国豪去世的时候仅仅二十八岁，比他的父亲还年轻，他本来可以成为和他父亲一样优秀的电影演员，但是却因为一次组织周密的谋杀失去了生命，实在是非常遗憾，二十多年前，李国豪曾经在李小龙的葬礼上指着父亲的遗照叫喊“电影，电影”，他以为父亲的死是虚假的，只是拍摄的一个电影镜头而已，但没想到自己二十年之后死在了电影中，这难道真是冥冥中注定吗</p><p><br/></p>', 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `qb_cms_content2`
--

DROP TABLE IF EXISTS `qb_cms_content2`;
CREATE TABLE IF NOT EXISTS `qb_cms_content2` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='图片模型模型表' AUTO_INCREMENT=43 ;

--
-- 转存表中的数据 `qb_cms_content2`
--

INSERT INTO `qb_cms_content2` (`id`, `mid`, `fid`, `title`, `ispic`, `picurl`, `uid`, `view`, `status`, `agree`, `replynum`, `content`, `create_time`, `update_time`, `list`, `pics`, `province_id`, `city_id`, `zone_id`, `street_id`, `ext_sys`, `ext_id`) VALUES
(18, 2, 14, '李国豪正在美国北卡罗莱纳州威尔明顿市的电影片场拍摄《乌鸦》', 0, '', 1, 67, 1, 0, 0, '李国豪的死和当年李小龙的死马上就被大家联系到了一起，天下难道还会有如此巧合的事情吗？或者这就是命运的安排？一时之间李小龙当年的死因又被挖掘出来，成了当时最大的谜团，后来警方对剧组的道具枪进行了检查，发现其他子弹都是空子弹，而只有射向李国豪身体的那一刻子弹是实弹，于是警方顺藤摸瓜，找出了杀害李国豪嫌疑最大的三个人，他们分别是演员麦西，在剧中正是他向李国豪开枪，很自然他被当成了杀害李国豪的第一嫌疑人，但是麦西却大喊自己冤枉，因为自己在演出之前并没有接触到枪支，他是按照剧情开枪的，他认为其中装的是空子弹，而枪是开拍前剧组道具总管交给他的。所以道具总管很自然的就成了第二嫌疑人。', 1517534109, 1522116690, 0, '[{"title":"004.jpg","url":"","picurl":"uploads/images/20180302/2334683a87b83f94f7106d31fc6ce0be.jpg"},{"title":"2.jpeg","url":"","picurl":"uploads/images/20180312/5518ce3a2f1c8eb669146b67bffc7a56.jpeg"},{"title":"3.jpeg","url":"","picurl":"uploads/images/20180312/efebd1b5ec8979134a8da76c539b8f05.jpeg"}]', 0, 0, 0, 0, 0, 30),
(19, 2, 14, '赵薇夫妇沦陷，满脸憔悴，昔日好友悉数离开，只有他俩力挺！', 0, '', 1, 150, 1, 0, 0, '1999年，赵薇发行首张专辑《Double双》、《天使旅行箱》，凭专辑获2005年MTV亚洲大奖中国地区最受欢迎歌手和2008年最佳女歌手等奖项。2001年，赵薇开始投身大银幕。', 1517556671, 1523951822, 0, '[{"title":"1a2aabf066b644be8336521049acef89.jpg","url":"","picurl":"uploads/images/20180302/ed46f4c7a30f7036bdb4182c2e91ce85.jpg"},{"title":"35.jpg","url":"","picurl":"uploads/images/20180302/09eb406c7b68b32f4f1a08d91e0405a4.jpg"},{"title":"35C0B9830E8D885E4EDAB66A83300DD392BA32E7_800_800.jpg","url":"","picurl":"uploads/images/20180302/4e130207d16de8e9325372e10f24b1c6.jpg"},{"title":"36B3CFF456C7BAB023B0B9E4B41113A0076BC4D6_800_800.jpg","url":"","picurl":"uploads/images/20180302/bbdad5bba94b25b1eb45004c3a946b06.jpg"},{"title":"53C7B7256FC241FB8A41F3CAD014DCE6200608031155.jpg","url":"","picurl":"uploads/images/20180302/2fca054b5a1adda5a4867a3dab39339c.jpg"},{"title":"60.JPG","url":"","picurl":"uploads/images/20180302/47ec268298550dbaf723f63f9c8068f8.JPG"}]', 0, 0, 0, 0, 0, 30);

-- --------------------------------------------------------

--
-- 表的结构 `qb_cms_content3`
--

DROP TABLE IF EXISTS `qb_cms_content3`;
CREATE TABLE IF NOT EXISTS `qb_cms_content3` (
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
  KEY `ext_id` (`ext_id`,`ext_sys`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='视频模型模型表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `qb_cms_field`
--


DROP TABLE IF EXISTS `qb_cms_field`;
CREATE TABLE IF NOT EXISTS `qb_cms_field` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT COMMENT '字段名称',
  `name` varchar(32) NOT NULL,
  `title` varchar(256) NOT NULL DEFAULT '' COMMENT '字段标题',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '字段类型',
  `field_type` varchar(128) NOT NULL DEFAULT '' COMMENT '字段定义',
  `value` text COMMENT '默认值',
  `options` text COMMENT '额外选项',
  `about` varchar(256) NOT NULL DEFAULT '' COMMENT '提示说明',
  `show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `mid` mediumint(5) NOT NULL DEFAULT '0' COMMENT '所属模型id',
  `ajax_url` varchar(256) NOT NULL DEFAULT '' COMMENT '联动下拉框ajax地址',
  `next_items` varchar(256) NOT NULL DEFAULT '' COMMENT '联动下拉框的下级下拉框名，多个以逗号隔开',
  `param` varchar(32) NOT NULL DEFAULT '' COMMENT '联动下拉框请求参数名',
  `format` varchar(32) NOT NULL DEFAULT '' COMMENT '格式，用于格式文本',
  `table` varchar(32) NOT NULL DEFAULT '' COMMENT '表名，只用于快速联动类型',
  `level` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '联动级别，只用于快速联动类型',
  `key` varchar(32) NOT NULL DEFAULT '' COMMENT '键字段，只用于快速联动类型',
  `option` varchar(32) NOT NULL DEFAULT '' COMMENT '值字段，只用于快速联动类型',
  `pid` varchar(32) NOT NULL DEFAULT '' COMMENT '父级id字段，只用于快速联动类型',
  `list` int(10) NOT NULL DEFAULT '100' COMMENT '排序',
  `listshow` tinyint(1) NOT NULL COMMENT '是否在列表显示',
  `ifsearch` tinyint(1) NOT NULL COMMENT '是否作为搜索字段',
  `ifmust` tinyint(1) NOT NULL COMMENT '是否必填项',
  `nav` varchar(30) NOT NULL COMMENT '分组名称',
  `input_width` varchar(7) NOT NULL COMMENT '输入表单宽度',
  `input_height` varchar(7) NOT NULL COMMENT '输入表单高度',
  `unit` varchar(20) NOT NULL COMMENT '单位名称',
  `match` varchar(150) NOT NULL COMMENT '表单正则匹配',
  `css` varchar(20) NOT NULL COMMENT '表单CSS类名',
  `script` text NOT NULL COMMENT 'JS脚本',
  `trigger` varchar(255) NOT NULL COMMENT '选择某一项后,联动触发显示其它字段',
  `range_opt` text NOT NULL COMMENT '范围选择,比如价格范围',
  `group_view` varchar(255) NOT NULL COMMENT '允许哪些用户组查看',
  `index_hide` tinyint(1) NOT NULL COMMENT '是否前台不显示并不做转义处理',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='模型字段表' AUTO_INCREMENT=57 ;

--
-- 转存表中的数据 `qb_cms_field`
--

INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES
(10, 'title', '标题', 'text', 'varchar(256) NOT NULL', NULL, NULL, '', 0, 1, '', '', '', '', '', 2, '', '', '', 100, 1, 1, 1, ''),
(11, 'picurl', '组图', 'images', 'text NOT NULL', '', '', '', 0, 1, '', '', '', '', '', 2, '', '', '', 100, 0, 0, 0, ''),
(12, 'content', '文章内容', 'ueditor', 'text NOT NULL', '', '', '', 0, 1, '', '', '', '', '', 2, '', '', '', 100, 0, 0, 0, ''),
(19, 'title', '图片主题', 'text', 'varchar(256) NOT NULL', '', '', '', 0, 2, '', '', '', '', '', 2, '', '', '', 100, 1, 1, 1, ''),
(20, 'picurl', '封面图', 'hidden', 'varchar(32) NOT NULL', '', '', '', 0, 2, '', '', '', '', '', 2, '', '', '', 98, 0, 0, 0, ''),
(21, 'content', '图片介绍', 'textarea', 'text NOT NULL', '', '', '', 0, 2, '', '', '', '', '', 2, '', '', '', 90, 0, 0, 0, ''),
(22, 'title', '标题', 'text', 'varchar(256) NOT NULL', NULL, NULL, '', 0, 3, '', '', '', '', '', 2, '', '', '', 100, 1, 1, 1, ''),
(23, 'picurl', '封面图', 'image', 'varchar(128) NOT NULL', '', '', '', 0, 3, '', '', '', '', '', 2, '', '', '', 99, 0, 0, 0, ''),
(24, 'content', '内容介绍', 'ueditor', 'text NOT NULL', '', '', '', 0, 3, '', '', '', '', '', 2, '', '', '', 90, 0, 0, 0, ''),
(25, 'pics', '组图带介绍', 'images2', 'text NOT NULL', '', '', '', 0, 2, '', '', '', '', '', 2, '', '', '', 97, 0, 0, 0, ''),
(26, 'mv_url', '视频地址', 'file', 'varchar(128) NOT NULL', '', '', '', 0, 3, '', '', '', '', '', 2, '', '', '', 98, 0, 0, 0, '');

-- --------------------------------------------------------

--
-- 表的结构 `qb_cms_info`
--

DROP TABLE IF EXISTS `qb_cms_info`;
CREATE TABLE IF NOT EXISTS `qb_cms_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) NOT NULL COMMENT '内容ID',
  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '辅栏目ID',
  `list` int(10) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`id`),
  KEY `mid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='内容索引表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `qb_cms_module`
--

DROP TABLE IF EXISTS `qb_cms_module`;
CREATE TABLE IF NOT EXISTS `qb_cms_module` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(32) NOT NULL DEFAULT '' COMMENT '区分符关键字',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '模型标题',
  `layout` varchar(50) NOT NULL COMMENT '模板路径',
  `icon` varchar(64) NOT NULL,
  `list` int(10) NOT NULL DEFAULT '100' COMMENT '排序',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='模型表' AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `qb_cms_module`
--

INSERT INTO `qb_cms_module` (`id`, `keyword`, `title`, `layout`, `icon`, `list`, `create_time`, `status`) VALUES
(1, '', '文章模型', '', '', 100, 1515221331, 0),
(2, '', '图片模型', '', '', 100, 1515236691, 0),
(3, '', '视频模型', '', '', 100, 1515236720, 0);

-- --------------------------------------------------------

--
-- 表的结构 `qb_cms_sort`
--

DROP TABLE IF EXISTS `qb_cms_sort`;
CREATE TABLE IF NOT EXISTS `qb_cms_sort` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='主栏目表' AUTO_INCREMENT=15 ;

--
-- 转存表中的数据 `qb_cms_sort`
--

INSERT INTO `qb_cms_sort` (`id`, `pid`, `mid`, `name`, `list`, `logo`, `template`, `allowpost`, `allowview`, `seo_title`, `seo_keywords`, `seo_description`) VALUES
(9, 0, 1, '娱乐新闻', 10, '', '', '', '', '', '', ''),
(10, 9, 1, '香港娱乐新闻', 0, '', '', '', '', '', '', ''),
(11, 9, 1, '大陆娱乐新闻', 0, '', '', '', '', '', '', ''),
(12, 9, 1, '广东娱乐', 0, '', '', '', '', '', '', ''),
(14, 0, 2, '图片专栏', 9, '', '', '', '', '', '', '');

DROP TABLE IF EXISTS `qb_cms_mysort`;
CREATE TABLE IF NOT EXISTS `qb_cms_mysort` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户的UID',
  `name` varchar(80) NOT NULL COMMENT '分类名称',
  `list` int(10) NOT NULL,
  `logo` varchar(50) NOT NULL COMMENT '封面图',
  `ext_sys` smallint(4) NOT NULL COMMENT '扩展字段,关联的系统',
  `ext_id` mediumint(7) NOT NULL COMMENT '扩展字段,关联的系统ID',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `list` (`list`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户自定义分类' AUTO_INCREMENT=1 ;


INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'myfid', '我的分类', 'select', 'int(7) NOT NULL DEFAULT ''0''', '', 'cms_mysort@id,name@uid', '<script>if($("#atc_myfid").children().length<1)$("#form_group_myfid").hide();</script>', 1, 1, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '');
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'myfid', '我的分类', 'select', 'int(7) NOT NULL DEFAULT ''0''', '', 'cms_mysort@id,name@uid', '<script>if($("#atc_myfid").children().length<1)$("#form_group_myfid").hide();</script>', 1, 2, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '');

ALTER TABLE  `qb_cms_content1` ADD  `myfid` MEDIUMINT( 7 ) NOT NULL COMMENT  '我的分类';
ALTER TABLE  `qb_cms_content1` ADD INDEX (  `myfid` );

ALTER TABLE  `qb_cms_content2` ADD  `myfid` MEDIUMINT( 7 ) NOT NULL COMMENT  '我的分类';
ALTER TABLE  `qb_cms_content2` ADD INDEX (  `myfid` );



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

INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'price', '付费阅读(元)', 'money', 'decimal(10,2) unsigned NOT NULL', '', '', '', 1, 1, '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', 1);
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'price', '付费阅读(元)', 'money', 'decimal(10,2) unsigned NOT NULL', '', '', '', 1, 2, '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', 1);
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'price', '付费阅读(元)', 'money', 'decimal(10,2) unsigned NOT NULL', '', '', '', 1, 3, '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', 1);



INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'description', '简介(SEO描述)', 'textarea', 'varchar(255) NOT NULL', '', '', '', 1, 1, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', 1);
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'description', '简介(SEO描述)', 'textarea', 'varchar(255) NOT NULL', '', '', '', 1, 2, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', 1);
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'description', '简介(SEO描述)', 'textarea', 'varchar(255) NOT NULL', '', '', '', 1, 3, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', 1);

ALTER TABLE  `qb_cms_content1` ADD  `description` VARCHAR( 255 ) NOT NULL COMMENT  '简介(SEO描述)';
ALTER TABLE  `qb_cms_content2` ADD  `description` VARCHAR( 255 ) NOT NULL COMMENT  '简介(SEO描述)';
ALTER TABLE  `qb_cms_content3` ADD  `description` VARCHAR( 255 ) NOT NULL COMMENT  '简介(SEO描述)';











INSERT INTO `qb_cms_module` (`id`, `keyword`, `title`, `layout`, `icon`, `list`, `create_time`, `status`) VALUES(4, 'voice', '音频模型', '', '', 100, 1561445881, 0);


DROP TABLE IF EXISTS `qb_cms_content4`;
CREATE TABLE IF NOT EXISTS `qb_cms_content4` (
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
  `voice_url` varchar(128) NOT NULL COMMENT '音频地址',
  `province_id` mediumint(5) NOT NULL COMMENT '省会ID',
  `city_id` mediumint(5) NOT NULL COMMENT '城市ID',
  `zone_id` mediumint(5) NOT NULL COMMENT '县级市或所在区ID',
  `street_id` mediumint(5) NOT NULL COMMENT '乡镇或区域街道ID',
  `ext_sys` mediumint(5) NOT NULL COMMENT '扩展字段,关联的系统',
  `ext_id` int(8) NOT NULL COMMENT '扩展字段,供其它调用',
  `keywords` varchar(128) NOT NULL COMMENT 'SEO关键字',
  `price` decimal(10,2) NOT NULL COMMENT '收费阅读',
  `description` varchar(255) NOT NULL COMMENT '简介(SEO描述)',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `fid` (`fid`),
  KEY `view` (`view`),
  KEY `status` (`status`),
  KEY `list` (`list`),
  KEY `ispic` (`ispic`),
  KEY `province_id` (`province_id`),
  KEY `city_id` (`city_id`),
  KEY `ext_id` (`ext_id`,`ext_sys`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='音频模型内容主表' AUTO_INCREMENT=1 ;



INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'title', '标题', 'text', 'varchar(256) NOT NULL', NULL, NULL, '', 0, 4, '', '', '', '', '', 2, '', '', '', 100, 1, 1, 1, '', '', '', '', '', '', '', '', '', '', 0);
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'picurl', '封面图', 'image', 'varchar(128) NOT NULL', '', '', '', 0, 4, '', '', '', '', '', 2, '', '', '', 90, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', 0);
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'content', '内容介绍', 'ueditor', 'text NOT NULL', '', '', '', 0, 4, '', '', '', '', '', 2, '', '', '', -1, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', 0);
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'voice_url', '音频地址', 'file', 'varchar(128) NOT NULL', '', '', '', 0, 4, '', '', '', '', '', 2, '', '', '', 80, 0, 0, 1, '', '', '', '', '', '', '', '', '', '', 1);
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'keywords', 'SEO关键字', 'text', 'varchar(128) NOT NULL', '', '', '', 1, 4, '', '', '', '', '', 2, '', '', '', 98, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', 1);
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'price', '付费阅读(元)', 'money', 'decimal(10,2) unsigned NOT NULL', '', '', '', 1, 4, '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', 1);
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'description', '简介(SEO描述)', 'textarea', 'varchar(255) NOT NULL', '', '', '', 1, 4, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', 1);


ALTER TABLE  `qb_cms_content4` CHANGE  `voice_url`  `voice_url` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '音频地址';
UPDATE `qb_cms_field` SET  `type` =  'files2',`field_type` =  'text NOT NULL'  WHERE `name` = 'voice_url';

ALTER TABLE  `qb_cms_content3` CHANGE  `mv_url`  `mv_url` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '视频地址';
UPDATE `qb_cms_field` SET  `type` =  'files2',`field_type` =  'text NOT NULL'  WHERE `name` = 'mv_url';



DROP TABLE IF EXISTS `qb_cms_fav`;
CREATE TABLE IF NOT EXISTS `qb_cms_fav` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mid` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `type` tinyint(1) NOT NULL COMMENT '0是浏览记录,1是收藏',
  `aid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  `sid` mediumint(4) NOT NULL COMMENT '章节ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `list` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序值',
  `paytime` smallint(5) NOT NULL COMMENT '已播放到第几秒',
  PRIMARY KEY (`id`),
  KEY `list` (`list`),
  KEY `aid` (`aid`),
  KEY `uid` (`uid`),
  KEY `mid` (`mid`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='浏览记录及收藏' AUTO_INCREMENT=1 ;


ALTER TABLE  `qb_cms_buyer` ADD  `sid` SMALLINT( 5 ) NOT NULL DEFAULT  '-1' COMMENT  '章节ID,注意:0是第一节,-1代表所有章节' AFTER  `aid`;
ALTER TABLE  `qb_cms_buyer` DROP INDEX  `aid` ,ADD INDEX  `aid` (  `aid` ,  `sid` );
ALTER TABLE  `qb_cms_fav` CHANGE  `paytime`  `playtime` SMALLINT( 5 ) NOT NULL COMMENT  '已播放到第几秒';



DROP TABLE IF EXISTS `qb_cms_fx`;
CREATE TABLE IF NOT EXISTS `qb_cms_fx` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `aid` int(7) NOT NULL COMMENT '内容主题ID',
  `sid` mediumint(4) NOT NULL COMMENT '章节ID',
  `uid` int(7) NOT NULL COMMENT '新进来的访问UID',
  `introducer_1` int(7) NOT NULL COMMENT '分享者,即介绍人的UID',
  `create_time` int(10) NOT NULL COMMENT '推荐时间',
  `ifbuy` tinyint(1) NOT NULL COMMENT '是否付款成功购买',
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`),
  KEY `introducer_1` (`introducer_1`),
  KEY `uid` (`uid`),
  KEY `ifbuy` (`ifbuy`),
  KEY `create_time` (`create_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户分享的记录' AUTO_INCREMENT=1 ;

 

DROP TABLE IF EXISTS `qb_cms_fxlog`;
CREATE TABLE IF NOT EXISTS `qb_cms_fxlog` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `aid` int(7) NOT NULL COMMENT '内容主题ID',
  `sid` mediumint(4) NOT NULL COMMENT '章节ID',
  `uid` int(7) NOT NULL COMMENT '当前购买内容主题用户UID',
  `introducer_uid` int(7) NOT NULL COMMENT '推荐人UID',
  `introducer_step` tinyint(1) NOT NULL DEFAULT '1' COMMENT '属于第几级推荐人',
  `create_time` int(10) NOT NULL COMMENT '交易成功时间',
  `money` decimal(10,2) NOT NULL COMMENT '分销获利金额',
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`),
  KEY `uid` (`uid`),
  KEY `introducer_uid` (`introducer_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='付款交易成功后的分销收益日志' AUTO_INCREMENT=1 ;

INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'fx1', '直接推荐人收益', 'money', 'decimal(5,2) unsigned NOT NULL', '', '', '注意:0.01代表1%的分成，并不是0.01元，以此类推', 1, 1, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '分销设置');
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'fx2', '间接推荐人收益', 'money', 'decimal(5,2) unsigned NOT NULL', '', '', '注意:0.01代表1%的分成，并不是0.01元，以此类推', 1, 1, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '分销设置');
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'fx3', '三级推荐人收益', 'money', 'decimal(5,2) unsigned NOT NULL', '', '', '注意:0.01代表1%的分成，并不是0.01元，以此类推', 1, 1, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '分销设置');

INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'fx1', '直接推荐人收益', 'money', 'decimal(5,2) unsigned NOT NULL', '', '', '注意:0.01代表1%的分成，并不是0.01元，以此类推', 1, 2, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '分销设置');
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'fx2', '间接推荐人收益', 'money', 'decimal(5,2) unsigned NOT NULL', '', '', '注意:0.01代表1%的分成，并不是0.01元，以此类推', 1, 2, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '分销设置');
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'fx3', '三级推荐人收益', 'money', 'decimal(5,2) unsigned NOT NULL', '', '', '注意:0.01代表1%的分成，并不是0.01元，以此类推', 1, 2, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '分销设置');

INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'fx1', '直接推荐人收益', 'money', 'decimal(5,2) unsigned NOT NULL', '', '', '注意:0.01代表1%的分成，并不是0.01元，以此类推', 1, 3, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '分销设置');
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'fx2', '间接推荐人收益', 'money', 'decimal(5,2) unsigned NOT NULL', '', '', '注意:0.01代表1%的分成，并不是0.01元，以此类推', 1, 3, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '分销设置');
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'fx3', '三级推荐人收益', 'money', 'decimal(5,2) unsigned NOT NULL', '', '', '注意:0.01代表1%的分成，并不是0.01元，以此类推', 1, 3, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '分销设置');

INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'fx1', '直接推荐人收益', 'money', 'decimal(5,2) unsigned NOT NULL', '', '', '注意:0.01代表1%的分成，并不是0.01元，以此类推', 1, 4, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '分销设置');
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'fx2', '间接推荐人收益', 'money', 'decimal(5,2) unsigned NOT NULL', '', '', '注意:0.01代表1%的分成，并不是0.01元，以此类推', 1, 4, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '分销设置');
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`) VALUES(0, 'fx3', '三级推荐人收益', 'money', 'decimal(5,2) unsigned NOT NULL', '', '', '注意:0.01代表1%的分成，并不是0.01元，以此类推', 1, 4, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '分销设置');


ALTER TABLE  `qb_cms_content1` ADD  `fx1` DECIMAL( 5, 2 ) NOT NULL COMMENT  '直接推荐人收益';
ALTER TABLE  `qb_cms_content1` ADD  `fx2` DECIMAL( 5, 2 ) NOT NULL COMMENT  '间接推荐人收益';
ALTER TABLE  `qb_cms_content1` ADD  `fx3` DECIMAL( 5, 2 ) NOT NULL COMMENT  '三级推荐人收益';

ALTER TABLE  `qb_cms_content2` ADD  `fx1` DECIMAL( 5, 2 ) NOT NULL COMMENT  '直接推荐人收益';
ALTER TABLE  `qb_cms_content2` ADD  `fx2` DECIMAL( 5, 2 ) NOT NULL COMMENT  '间接推荐人收益';
ALTER TABLE  `qb_cms_content2` ADD  `fx3` DECIMAL( 5, 2 ) NOT NULL COMMENT  '三级推荐人收益';

ALTER TABLE  `qb_cms_content3` ADD  `fx1` DECIMAL( 5, 2 ) NOT NULL COMMENT  '直接推荐人收益';
ALTER TABLE  `qb_cms_content3` ADD  `fx2` DECIMAL( 5, 2 ) NOT NULL COMMENT  '间接推荐人收益';
ALTER TABLE  `qb_cms_content3` ADD  `fx3` DECIMAL( 5, 2 ) NOT NULL COMMENT  '三级推荐人收益';

ALTER TABLE  `qb_cms_content4` ADD  `fx1` DECIMAL( 5, 2 ) NOT NULL COMMENT  '直接推荐人收益';
ALTER TABLE  `qb_cms_content4` ADD  `fx2` DECIMAL( 5, 2 ) NOT NULL COMMENT  '间接推荐人收益';
ALTER TABLE  `qb_cms_content4` ADD  `fx3` DECIMAL( 5, 2 ) NOT NULL COMMENT  '三级推荐人收益';


UPDATE  `qb_cms_field` SET  `index_hide` =  '1' WHERE  `name` = 'fx1';
UPDATE  `qb_cms_field` SET  `index_hide` =  '1' WHERE  `name` = 'fx2';
UPDATE  `qb_cms_field` SET  `index_hide` =  '1' WHERE  `name` = 'fx3';


DROP TABLE IF EXISTS `qb_cms_yhlog`;
CREATE TABLE IF NOT EXISTS `qb_cms_yhlog` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `uid` int(8) NOT NULL COMMENT '领取者UID',
  `aid` int(8) NOT NULL COMMENT '主题ID',
  `yid` int(8) NOT NULL COMMENT '优惠券ID',
  `create_time` int(10) NOT NULL COMMENT '领取日期',
  `money` decimal(8,2) NOT NULL COMMENT '优惠价格，可以是价格，也可以是百分比',
  `ifuse` tinyint(1) NOT NULL COMMENT '使用与否',
  `end_time` int(10) NOT NULL COMMENT '截止有效日期',
  `use_time` int(10) NOT NULL COMMENT '使用时间',
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`),
  KEY `uid` (`uid`),
  KEY `yid` (`yid`),
  KEY `end_time` (`end_time`),
  KEY `ifuse` (`ifuse`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户领取的优惠券' AUTO_INCREMENT=1 ;



DROP TABLE IF EXISTS `qb_cms_youhui`;
CREATE TABLE IF NOT EXISTS `qb_cms_youhui` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `uid` int(8) NOT NULL COMMENT '分销创建者',
  `aid` int(8) NOT NULL COMMENT '主题ID',
  `create_time` int(10) NOT NULL COMMENT '创建日期',
  `money` decimal(8,2) NOT NULL COMMENT '优惠价格，可以是价格，也可以是百分比',
  `max_num` mediumint(5) NOT NULL COMMENT '总数多少份',
  `end_time` int(10) NOT NULL COMMENT '截止有效日期',
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='分销用户创建的优惠券' AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `qb_cms_pages`;
CREATE TABLE IF NOT EXISTS `qb_cms_pages` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(7) NOT NULL COMMENT '用户UID',
  `aid` int(8) NOT NULL COMMENT '主题ID',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` mediumtext NOT NULL COMMENT '文章内容',
  `list` int(10) NOT NULL COMMENT '排序值',
  `create_time` int(10) NOT NULL COMMENT '添加日期',
  `money` varchar(5) NOT NULL COMMENT '售价RMB，字段不能有默认值0，0.00',
  PRIMARY KEY (`id`),
  KEY `list` (`list`),
  KEY `aid` (`aid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='文章模型分页数据' AUTO_INCREMENT=30 ;


ALTER TABLE  `qb_cms_content` ADD  `view` MEDIUMINT( 7 ) NOT NULL COMMENT  '浏览量';
ALTER TABLE  `qb_cms_content` ADD INDEX (  `view` );
ALTER TABLE  `qb_cms_content` ADD  `status` TINYINT( 2 ) NOT NULL COMMENT  '状态：0未审 1已审 2推荐';
ALTER TABLE  `qb_cms_content` ADD INDEX (  `status` );
UPDATE `qb_cms_content` SET  `status` =1;
ALTER TABLE  `qb_cms_content` ADD  `list` INT( 10 ) NOT NULL COMMENT  '可控排序';
ALTER TABLE  `qb_cms_content` ADD INDEX (  `list` );
ALTER TABLE  `qb_cms_content` ADD  `ext_id` MEDIUMINT( 7 ) NOT NULL COMMENT  '关联其它模型的内容ID',ADD  `ext_sys` SMALLINT( 4 ) NOT NULL COMMENT  '关联其它模型的频道ID';
ALTER TABLE  `qb_cms_content` ADD INDEX (  `ext_id` ,  `ext_sys` );



ALTER TABLE  `qb_cms_content3` ADD  `zhibo_status` TINYINT( 1 ) NOT NULL COMMENT  '直播状态,1直播预告,2直播进行中,3直播已结束',ADD  `size_type` TINYINT( 1 ) NOT NULL COMMENT  '0是横屏,1是竖屏';
ALTER TABLE  `qb_cms_content3` ADD  `start_time` INT( 10 ) NOT NULL COMMENT  '直播开始时间',ADD  `stop_time` INT( 10 ) NOT NULL COMMENT  '直播结束时间';
ALTER TABLE  `qb_cms_content3` ADD INDEX (  `zhibo_status` );



INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'zhibo_status', '直播状态', 'radio', 'tinyint(1) NOT NULL DEFAULT ''0''', '', '0|非直播内容\r\n1|直播预告\r\n2|直播进行中\r\n3|直播已结束', '', 1, 3, '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0, '直播选项', '', '', '', '', '', '', '', '', '', 1);
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'size_type', '横屏或竖屏', 'radio', 'tinyint(1) NOT NULL DEFAULT ''0''', '0', '0|横屏\r\n1|是竖屏', '', 1, 3, '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0, '直播选项', '', '', '', '', '', '', '', '', '', 1);
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'start_time', '直播开始时间', 'datetime', 'int(10) NOT NULL DEFAULT ''0''', '', '', '', 1, 3, '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0, '直播选项', '', '', '', '', '', '', '', '', '', 1);
INSERT INTO `qb_cms_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`, `range_opt`, `group_view`, `index_hide`) VALUES(0, 'stop_time', '直播结束时间', 'datetime', 'int(10) NOT NULL DEFAULT ''0''', '', '', '', 1, 3, '', '', '', '', '', 0, '', '', '', 0, 0, 0, 0, '直播选项', '', '', '', '', '', '', '', '', '', 1);
