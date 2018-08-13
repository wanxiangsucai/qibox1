ALTER TABLE  `qb_admin_menu` COMMENT =  '管理员常用菜单及会员个性菜单';
ALTER TABLE  `qb_admin_menu` CHANGE  `type`  `type` TINYINT( 1 ) NOT NULL COMMENT  '0为管理员常用菜单,1为会员中心菜单,2存在的话,就为PC版会员中心菜单';