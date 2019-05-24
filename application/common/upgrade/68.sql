ALTER TABLE  `qb_msg` ADD  `ext_sys` INT( 7 ) NOT NULL COMMENT  '系统模型ID',ADD  `ext_id` INT( 7 ) NOT NULL COMMENT  '内容ID';
ALTER TABLE  `qb_msg` ADD INDEX (  `ext_sys` ,  `ext_id` );
