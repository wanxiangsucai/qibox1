ALTER TABLE  `qb_shop_content1` CHANGE  `comment`  `replynum` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '评论数';
ALTER TABLE  `qb_shop_content1` ADD  `ext_sys` SMALLINT( 5 ) NOT NULL ,ADD  `ext_id` INT( 8 ) NOT NULL;
ALTER TABLE  `qb_shop_content1` ADD INDEX (  `ext_id` ,  `ext_sys` );


ALTER TABLE  `qb_cms_content1` CHANGE  `comment`  `replynum` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '评论数';
ALTER TABLE  `qb_cms_content1` ADD  `ext_sys` SMALLINT( 5 ) NOT NULL ,ADD  `ext_id` INT( 8 ) NOT NULL;
ALTER TABLE  `qb_cms_content1` ADD INDEX (  `ext_id` ,  `ext_sys` );

ALTER TABLE  `qb_cms_content2` CHANGE  `comment`  `replynum` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '评论数';
ALTER TABLE  `qb_cms_content2` ADD  `ext_sys` SMALLINT( 5 ) NOT NULL ,ADD  `ext_id` INT( 8 ) NOT NULL;
ALTER TABLE  `qb_cms_content2` ADD INDEX (  `ext_id` ,  `ext_sys` );

ALTER TABLE  `qb_cms_content3` CHANGE  `comment`  `replynum` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '评论数';
ALTER TABLE  `qb_cms_content3` ADD  `ext_sys` SMALLINT( 5 ) NOT NULL ,ADD  `ext_id` INT( 8 ) NOT NULL;
ALTER TABLE  `qb_cms_content3` ADD INDEX (  `ext_id` ,  `ext_sys` );