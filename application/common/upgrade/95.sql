UPDATE  `qb_chatmod` SET  `status`=0 WHERE  `keywords` =  'topic' AND `pcwap`=3;
UPDATE  `qb_chatmod` SET  `pcwap`=0  WHERE  `keywords` =  'topic' AND `pcwap`!=3;
