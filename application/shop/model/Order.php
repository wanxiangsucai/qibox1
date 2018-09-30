<?php
namespace app\shop\model;

use app\common\model\Order AS _Order;


class Order extends _Order
{
    /**
     * 支付成功,资金变动 , 也可以增加消息通知
     * @param array $order_info 订单信息,不是商品信息
     */
    protected static function success_pay($order_info=[]){
        parent::success_pay($order_info);
        
        //下面做更新库存处理  避免用户恶意下单减少库存,所以只有付款后,才减少库存
        $detail = explode(',',$order_info['shop']);
        foreach($detail AS $value){
            list($shopid,$num) = explode('-',$value);
            $num || $num=1;
            Content::addField($shopid,'num',false,$num);    //更新库存
        }
    }
}