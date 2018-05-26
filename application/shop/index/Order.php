<?php
namespace app\shop\index;

use app\common\controller\index\Order AS _Order;

//下单
class Order extends _Order
{
    public function add(){
        return parent::add();
    }
    
    public function endpay($order_id = ''){
        return parent::endpay($order_id);
    }
}

