ALTER TABLE  `qb_memberdata` ADD  `dou` MEDIUMINT NOT NULL COMMENT  '金豆(另一种形式的积分)' AFTER  `money`;
ALTER TABLE  `qb_memberdata` ADD INDEX (  `money` );
ALTER TABLE  `qb_memberdata` ADD INDEX (  `rmb` );
ALTER TABLE  `qb_memberdata` ADD INDEX (  `dou` );