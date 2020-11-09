DELETE FROM `qb_hook` WHERE `name`='comment_add_end';
INSERT INTO `qb_hook` (`name`, `about`, `ifopen`, `list`) VALUES( 'comment_add_end', '评论回复接口', 1, 0);