ALTER TABLE  `qb_msg` CHANGE  `touid`  `touid` BIGINT( 12 ) NOT NULL DEFAULT  '0' COMMENT  '接收者的帐户uid';
ALTER TABLE  `qb_msg` CHANGE  `uid`  `uid` BIGINT( 12 ) NOT NULL DEFAULT  '0' COMMENT  '发送者的UID';
