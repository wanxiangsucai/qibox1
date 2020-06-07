ALTER TABLE  `qb_friend` CHANGE  `suid`  `suid` INT( 7 ) NOT NULL COMMENT  '被关注者的uid,偶像UID';
ALTER TABLE  `qb_friend` CHANGE  `uid`  `uid` INT( 7 ) NOT NULL COMMENT  '关注者uid，当前登录用户的uid';
ALTER TABLE  `qb_friend` CHANGE  `type`  `type` TINYINT( 1 ) NOT NULL COMMENT  '1是粉丝,-1黑名单,2是好友(未必是双向好友,他是我的好友,但我可能是他的黑名单或者他还没关注我)';