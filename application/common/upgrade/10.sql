ALTER TABLE `qb_msg` DROP INDEX  `touid` ,ADD INDEX  `touid` (  `touid` ,  `ifread` );