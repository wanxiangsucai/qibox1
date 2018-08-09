ALTER TABLE  `qb_shop_field` ADD  `input_width` VARCHAR( 7 ) NOT NULL COMMENT  '输入表单宽度',ADD  `input_height` VARCHAR( 7 ) NOT NULL COMMENT  '输入表单高度',ADD  `unit` VARCHAR( 20 ) NOT NULL COMMENT  '单位名称',ADD  `match` VARCHAR( 150 ) NOT NULL COMMENT  '表单正则匹配',ADD  `css` VARCHAR( 20 ) NOT NULL COMMENT  '表单CSS类名';

ALTER TABLE  `qb_shop_order` ADD  `shopid` MEDIUMINT( 7 ) NOT NULL COMMENT  '商品ID,扩展使用' AFTER  `shop_uid` ,ADD  `shopnum` MEDIUMINT( 7 ) NOT NULL COMMENT  '购买数量,扩展使用' AFTER  `shopid`;

ALTER TABLE  `qb_shop_field` CHANGE  `mid`  `mid` MEDIUMINT( 5 ) NOT NULL DEFAULT  '0' COMMENT  '所属模型id';
ALTER TABLE  `qb_shop_order` ADD  `mid` TINYINT( 1 ) NOT NULL DEFAULT  '-1' AFTER  `id`;

DELETE FROM  `qb_shop_field` WHERE mid='-1' AND `name` IN ('linkman','telphone','address','user_note');

INSERT INTO `qb_shop_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`) VALUES(0, 'linkman', '联系人', 'text', 'varchar(60) NOT NULL', '', '', '', 1, -1, '', '', '', '', '', 2, '', '', '', 10, 1, 1, 0, '', '', '', '', '', '');
INSERT INTO `qb_shop_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`) VALUES(0, 'telphone', '联系电话', 'text', 'varchar(60) NOT NULL', '', '', '', 1, -1, '', '', '', '', '', 2, '', '', '', 9, 1, 1, 0, '', '', '', '', '', '');
INSERT INTO `qb_shop_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`) VALUES(0, 'address', '联系地址', 'text', 'varchar(256) NOT NULL', '', '', '', 1, -1, '', '', '', '', '', 2, '', '', '', 8, 0, 0, 0, '', '', '', '', '', '');
INSERT INTO `qb_shop_field` (`id`, `name`, `title`, `type`, `field_type`, `value`, `options`, `about`, `show`, `mid`, `ajax_url`, `next_items`, `param`, `format`, `table`, `level`, `key`, `option`, `pid`, `list`, `listshow`, `ifsearch`, `ifmust`, `nav`, `input_width`, `input_height`, `unit`, `match`, `css`) VALUES(0, 'user_note', '附注留言', 'textarea', 'text NOT NULL', '', '', '', 1, -1, '', '', '', '', '', 2, '', '', '', 0, 0, 0, 0, '', '', '', '', '', '');