
ALTER TABLE  `qb_shop_field` ADD  `script` TEXT NOT NULL COMMENT  'JS脚本',ADD  `trigger` VARCHAR( 255 ) NOT NULL COMMENT  '选择某一项后,联动触发显示其它字段';

UPDATE `qb_shop_field` SET `about` =  '只有这一项可以定义价格,属性|价格用竖线隔开,比如:大份|20' WHERE `name` = 'type1';
UPDATE `qb_shop_field` SET `about` =  '<script>if($("#atc_myfid").children().length<1)$("#form_group_myfid").remove();</script>' WHERE `name` = 'myfid';

INSERT INTO `qb_shop_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`) VALUES(0, 'market_price', '市场原价', 'money', 'decimal(10,2) unsigned NOT NULL', '', '', '', 1, 1, '', '', '', '', '', 2, '', '', '', 99, 0, 0, 0, '', '', '', '', '', '', '', '');
INSERT INTO `qb_shop_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`, `script`, `trigger`) VALUES(0, 'num', '库存量', 'number', 'mediumint(7) NOT NULL DEFAULT ''0''', '500', '', '', 1, 1, '', '', '', '', '', 2, '', '', '', 97, 0, 0, 0, '', '', '', '', '', '', '', '');

ALTER TABLE  `qb_shop_content1` ADD  `num` MEDIUMINT( 6 ) NOT NULL DEFAULT  '500' COMMENT  '库存量';
ALTER TABLE  `qb_shop_content1` ADD  `market_price` DECIMAL( 10, 2 ) NOT NULL COMMENT  '市场原价';
UPDATE  `qb_shop_content1` SET  `num` =20;