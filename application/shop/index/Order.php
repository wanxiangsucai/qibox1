<?php
namespace app\shop\index;

use app\common\controller\index\Order AS _Order;

//下单
class Order extends _Order
{
    public function add(){
        $result = $this->check_buy();
        if ($result!==true) { //检查用户是否有库存
            $this->error($result);
        }
        return parent::add();
    }
    
    public function endpay($order_id = ''){
        return parent::endpay($order_id);
    }
    
    protected function check_buy(){
        $listdb = $this->car_model->getList($this->user['uid'],1);
        foreach ($listdb AS $uid=>$shop_array){
            foreach ($shop_array AS $rs){
                if ($rs['num']<1) {
                    return $rs['title'] . ' 该商品库存为0,无法购买';
                }elseif ($rs['_car_']['num']>$rs['num']){
                    return $rs['title'] . ' 该商品库存为'.$rs['num'].',你选购的数量不能超过它';
                }
            }
        }
        return true;
    }
}

