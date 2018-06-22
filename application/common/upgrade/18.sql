ALTER TABLE `qb_alonepage` CHANGE  `ifclose`  `status` TINYINT( 1 ) NOT NULL DEFAULT  '1' COMMENT  '状态:1启用0关闭';
ALTER TABLE `qb_alonepage` ADD INDEX (  `status` );
ALTER TABLE `qb_alonepage` ADD INDEX (  `list` );