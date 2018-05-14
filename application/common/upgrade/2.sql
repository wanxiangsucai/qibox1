ALTER TABLE  `qb_hook_plugin` CHANGE  `hook_id`  `hook_key` VARCHAR( 50 ) NOT NULL COMMENT  '所归属的接口关键字',
CHANGE  `plugin_id`  `plugin_key` VARCHAR( 50 ) NOT NULL COMMENT  '所归属的插件关键字,也即目录名';

ALTER TABLE  `qb_hook_plugin` ADD  `author` VARCHAR( 80 ) NOT NULL COMMENT  '开发者' AFTER  `list` ,
ADD  `author_url` VARCHAR( 120 ) NOT NULL COMMENT  '开发者网站' AFTER  `author`;
