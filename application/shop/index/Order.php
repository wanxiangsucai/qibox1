<?php
namespace app\shop\index;

use app\common\controller\index\Order AS _Order;
use app\shop\model\Content AS ContentModel;

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
    
    /**
     * 付款之后返回的页面
     * @param string $order_id
     */
    public function endpay($order_id = ''){
        return parent::endpay($order_id);
    }
    
    /**
     * 下单后执行的
     * {@inheritDoc}
     * @see \app\common\controller\index\Order::end_add()
     */
    protected function end_add($order_ids=[],$car_ids=[],$car_db=[]){
        foreach($car_db AS $rs){
            ContentModel::addField($rs['shopid'],'order_num',true,$rs['num']);  //更新下订数量
        }
        return parent::end_add($order_ids,$car_ids,$car_db);
    }
    
    /**
     * 检查库存是否充足
     * @return string|boolean
     */
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

