ALTER TABLE  `qb_memberdata` ADD  `idcardpic` VARCHAR( 100 ) NOT NULL AFTER  `idcard`;
ALTER TABLE  `qb_memberdata` CHANGE  `truename`  `truename` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '真实姓名';
ALTER TABLE  `qb_memberdata` CHANGE  `idcardpic`  `idcardpic` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '证件扫描件';
ALTER TABLE  `qb_memberdata` CHANGE  `introduce`  `introduce` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '签名或自我介绍';

INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(639, 8, '注册是否启用邮箱获取验证码', 'reg_email_num', '0', 'radio', '0|禁用\r\n1|启用', 1, '', '务必先配置好邮箱接口', 102, 0);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(640, 8, '注册是否启用手机短信获取验证码', 'reg_phone_num', '1', 'radio', '0|禁用\r\n1|启用', 1, '', '务必先配置好短信接口', 101, 0);
INSERT INTO `qb_config` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`) VALUES(641, 8, '是否启用微信公众号获取验证码', 'reg_weixin_num', '0', 'radio', '0|禁用\r\n1|启用', 1, '', '用户需要先关注公众号,然后回复验证码,才能收到验证码', 100, 0);



ALTER TABLE  `qb_cms_sort` CHANGE  `seo_title`  `seo_title` VARCHAR( 255 ) NOT NULL COMMENT  'SEO标题';
ALTER TABLE  `qb_cms_sort` CHANGE  `seo_keywords`  `seo_keywords` VARCHAR( 255 ) NOT NULL COMMENT  'SEO关键字';
ALTER TABLE  `qb_cms_sort` CHANGE  `seo_description`  `seo_description` VARCHAR( 255 ) NOT NULL COMMENT  'SEO描述';
update `qb_cms_sort` set `seo_title`='',`seo_keywords`='',`seo_description`='';

ALTER TABLE  `qb_shop_sort` CHANGE  `seo_title`  `seo_title` VARCHAR( 255 ) NOT NULL COMMENT  'SEO标题';
ALTER TABLE  `qb_shop_sort` CHANGE  `seo_keywords`  `seo_keywords` VARCHAR( 255 ) NOT NULL COMMENT  'SEO关键字';
ALTER TABLE  `qb_shop_sort` CHANGE  `seo_description`  `seo_description` VARCHAR( 255 ) NOT NULL COMMENT  'SEO描述';
update `qb_shop_sort` set `seo_title`='',`seo_keywords`='',`seo_description`='';


ALTER TABLE  `qb_bbs_sort` CHANGE  `seo_title`  `seo_title` VARCHAR( 255 ) NOT NULL COMMENT  'SEO标题';
ALTER TABLE  `qb_bbs_sort` CHANGE  `seo_keywords`  `seo_keywords` VARCHAR( 255 ) NOT NULL COMMENT  'SEO关键字';
ALTER TABLE  `qb_bbs_sort` CHANGE  `seo_description`  `seo_description` VARCHAR( 255 ) NOT NULL COMMENT  'SEO描述';
update `qb_bbs_sort` set `seo_title`='',`seo_keywords`='',`seo_description`='';


ALTER TABLE  `qb_form_sort` CHANGE  `seo_title`  `seo_title` VARCHAR( 255 ) NOT NULL COMMENT  'SEO标题';
ALTER TABLE  `qb_form_sort` CHANGE  `seo_keywords`  `seo_keywords` VARCHAR( 255 ) NOT NULL COMMENT  'SEO关键字';
ALTER TABLE  `qb_form_sort` CHANGE  `seo_description`  `seo_description` VARCHAR( 255 ) NOT NULL COMMENT  'SEO描述';
update `qb_form_sort` set `seo_title`='',`seo_keywords`='',`seo_description`='';

ALTER TABLE  qb_hongbao_sort CHANGE  `seo_title`  `seo_title` VARCHAR( 255 ) NOT NULL COMMENT  'SEO标题';
ALTER TABLE  qb_hongbao_sort CHANGE  `seo_keywords`  `seo_keywords` VARCHAR( 255 ) NOT NULL COMMENT  'SEO关键字';
ALTER TABLE  qb_hongbao_sort CHANGE  `seo_description`  `seo_description` VARCHAR( 255 ) NOT NULL COMMENT  'SEO描述';
update qb_hongbao_sort set `seo_title`='',`seo_keywords`='',`seo_description`='';

ALTER TABLE  qb_voicehb_sort CHANGE  `seo_title`  `seo_title` VARCHAR( 255 ) NOT NULL COMMENT  'SEO标题';
ALTER TABLE  qb_voicehb_sort CHANGE  `seo_keywords`  `seo_keywords` VARCHAR( 255 ) NOT NULL COMMENT  'SEO关键字';
ALTER TABLE  qb_voicehb_sort CHANGE  `seo_description`  `seo_description` VARCHAR( 255 ) NOT NULL COMMENT  'SEO描述';
update qb_voicehb_sort set `seo_title`='',`seo_keywords`='',`seo_description`='';

ALTER TABLE  qb_exam_sort CHANGE  `seo_title`  `seo_title` VARCHAR( 255 ) NOT NULL COMMENT  'SEO标题';
ALTER TABLE  qb_exam_sort CHANGE  `seo_keywords`  `seo_keywords` VARCHAR( 255 ) NOT NULL COMMENT  'SEO关键字';
ALTER TABLE  qb_exam_sort CHANGE  `seo_description`  `seo_description` VARCHAR( 255 ) NOT NULL COMMENT  'SEO描述';
update qb_exam_sort set `seo_title`='',`seo_keywords`='',`seo_description`='';

ALTER TABLE  qb_appstore_sort CHANGE  `seo_title`  `seo_title` VARCHAR( 255 ) NOT NULL COMMENT  'SEO标题';
ALTER TABLE  qb_appstore_sort CHANGE  `seo_keywords`  `seo_keywords` VARCHAR( 255 ) NOT NULL COMMENT  'SEO关键字';
ALTER TABLE  qb_appstore_sort CHANGE  `seo_description`  `seo_description` VARCHAR( 255 ) NOT NULL COMMENT  'SEO描述';
update qb_appstore_sort set `seo_title`='',`seo_keywords`='',`seo_description`='';