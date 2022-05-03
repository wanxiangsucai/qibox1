ALTER TABLE `qb_rmb_infull` ADD `refund_money` DECIMAL( 8, 2 ) NOT NULL COMMENT '退款金额',ADD `transaction_id` VARCHAR( 32 ) NOT NULL COMMENT '微信或支付宝的交易单号，退款时是原来的订单号';
ALTER TABLE `qb_rmb_consume` ADD `refund` TINYINT NOT NULL COMMENT '1支持退款，2已退款';
