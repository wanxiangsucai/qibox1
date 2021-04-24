ALTER TABLE  `qb_config` ADD  `is_open` TINYINT NOT NULL COMMENT  '是否公开给接口调用，密钥就不要公开';
ALTER TABLE  `qb_config` ADD INDEX (  `is_open` ) COMMENT  '';
UPDATE  `qb_config` SET is_open=1 WHERE  `c_key` IN ( 'webname','wxapp_appid','MoneyName','MoneyDW','logo','hiden_index_php','weixin_type','mp_code_img','wxapp_subscribe_template_id','forbid_normal_reg','yzImgReg','reg_email_num','reg_phone_num','reg_weixin_num','show_nickname','edit_username_money','must_yz_phone','service_email','service_tel','service_qq','service_wxcode','seo_title','seo_keyword','seo_description','web_open','close_why');
