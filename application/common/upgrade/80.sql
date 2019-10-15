ALTER TABLE  `qb_msg` ADD  `update_time` INT( 10 ) NOT NULL COMMENT  '排序值';
ALTER TABLE  `qb_msg` ADD INDEX (  `update_time` ) COMMENT  '';
ALTER TABLE  `qb_msg` ADD INDEX (  `visit_time` ) COMMENT  '';
