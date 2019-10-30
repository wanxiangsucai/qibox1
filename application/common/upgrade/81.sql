ALTER TABLE  `qb_memberdata` ADD  `unionid` VARCHAR( 32 ) NOT NULL COMMENT  '微信开放平台统一登录ID' AFTER  `nickname` ;
ALTER TABLE  `qb_memberdata` ADD INDEX (  `unionid` ) COMMENT  '';
ALTER TABLE  `qb_memberdata` ADD  `wxopen_api` VARCHAR( 32 ) NOT NULL COMMENT  '微信开放平台移动应用登录openid' AFTER  `wxapp_api` ;
