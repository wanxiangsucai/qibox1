ALTER TABLE  `qb_msg` ADD  `qun_id` MEDIUMINT( 7 ) NOT NULL COMMENT  '群聊的圈子ID';
ALTER TABLE  `qb_msg` ADD INDEX (  `qun_id` ) COMMENT  '';
ALTER TABLE  `qb_msg` ADD  `visit_time` INT( 10 ) NOT NULL COMMENT  '最后访问圈子群聊的时间';
