<?php
namespace app\appstore\index;

use app\common\controller\IndexBase; 
use app\appstore\model\Order as OrderModel;
use app\appstore\model\Car as CarModel;

//下订单
class Order extends IndexBase
{    
    //付款成功
    public function endpay($order_id = ''){
        if(OrderModel::pay($order_id)){
            $this -> success('支付成功', '/');
        }else{
            $this->error('支付失败');
        }
    }
    
    
    //提交订单
    public function add() {
        if($this -> request -> isPost()){
            $data = $this -> request -> post();
            
            $order_ids = [];
            $listdb = CarModel::getList($this->user['uid'],1);
            
            $total_money = 0;   //需要支付的总金额
            foreach ($listdb AS $uid=>$shop_array){     //取每一个商家的数据生成一个订单,不能同家不能混在同一个订单
                $data['shop_uid'] = $uid;   //店主UID
                $_shop = [];
                $money = 0;
                foreach ($shop_array AS $rs){   //某个商家的多个商品
                    $_shop[] = $rs['_car_']['shopid'] . '-' . $rs['_car_']['num']  . '-' . $rs['_car_']['type1'] . '-' .$rs['_car_']['type2'] . '-' .$rs['_car_']['type3'];
                    $money += get_shop_price($rs,$rs['_car_']['type1']);
                }
                $data['shop'] = implode(',', $_shop);
                $data['order_sn'] = rands(10);      //订单号
                $data['totalmoney'] = $data['pay_money'] = $money; 
                $total_money +=$money; 
                if (!empty($this -> validate)) {// 验证表单                    
                    $result = $this -> validate($data, $this -> validate);
                    if (true !== $result) $this -> error($result);
                }
                $data['uid'] = $this -> user['uid'];
                $data['create_time'] = time();
                if ($result = OrderModel::create($data)) {
                    $order_ids[] = $result->id;
                }
            }
            
            if (!empty($order_ids)) {
                $url = '/';
                if ($data['ifolpay']==1) {
                    $order_ids = implode(',', $order_ids);
                    $url = post_olpay([
                                    //'money'=>$total_money,
                                    //'money'=>'0.01',    //调试
                                    'return_url'=>url('endpay',['order_id'=>$order_ids]),
                                    'banktype'=>'alipay',
                                    'numcode'=>$data['order_sn'],
                                    'callback_class'=>mymd5('app\\'.config('system_dirname').'\\model\\Order@pay@'.$order_ids),
                            ]);
                }
                $this -> success('订单提交成功', $url);
            } else {
                $this -> error('订单提交失败');
            }
        }
        return $this ->fetch();
    } 
    
    
}
