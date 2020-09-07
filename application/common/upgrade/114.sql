ALTER TABLE  `qb_memberdata` CHANGE  `bday`  `bday` DATE NOT NULL COMMENT  '出生日期';
ALTER TABLE  `qb_memberdata` ADD  `qun_id` INT( 7 ) NOT NULL COMMENT  '圈子ID,由圈主创建的用户';
