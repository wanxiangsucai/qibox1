ALTER TABLE `qb_module_buyer` ADD `qid` INT( 8 ) NOT NULL COMMENT '归属圈子ID' AFTER `id` ;
ALTER TABLE `qb_module_buyer` ADD INDEX (`qid`);
