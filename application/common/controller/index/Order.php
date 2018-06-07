<?php
namespace app\common\controller\index;

use app\common\controller\IndexBase; 
use app\common\util\Shop AS ShopFun;

/**
 * 商城下订单
 * @author Administrator
 *
 */
abstract class Order extends IndexBase
{    
    protected $order_model;
    protected $car_model;
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->order_model = get_model_class($dirname,'order');
        $this->car_model = get_model_class($dirname,'car');
    }
    
    /**
     * 付款之后返回的页面
     * @param string $order_id
     */
    public function endpay($order_id = ''){
        if($this->order_model->pay($order_id)){
            $this -> success('支付成功', '/');
        }else{
            $this->error('支付失败');
        }
    }
    
    
    /**
     * 提交订单,还没进入付款页面
     * 在微信端,就用微信支付,否则就用支付宝支付
     * @return mixed|string
     */
    public function add() {
        if($this -> request -> isPost()){
            $data = $this -> request -> post();
            
            $order_ids = [];
            $listdb = $this->car_model->getList($this->user['uid'],1);
            
            $total_money = 0;   //需要支付的总金额
            foreach ($listdb AS $uid=>$shop_array){     //取每一个商家的数据生成一个订单,不能同家不能混在同一个订单
                $data['shop_uid'] = $uid;   //店主UID
                $_shop = [];
                $money = 0;
                foreach ($shop_array AS $rs){   //某个商家的多个商品
                    $_shop[] = $rs['_car_']['shopid'] . '-' . $rs['_car_']['num']  . '-' . $rs['_car_']['type1'] . '-' .$rs['_car_']['type2'] . '-' .$rs['_car_']['type3'];
                    $money += ShopFun::get_price($rs,$rs['_car_']['type1']-1);
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
                if ($result = $this->order_model->create($data)) {
                    $order_ids[] = $result->id;
                }
            }
            
            if (!empty($order_ids)) {
                $url = '/';
                if ($data['ifolpay']==1) {
                    $order_ids = implode(',', $order_ids);
                    $url = post_olpay([
                                    'money'=>$total_money,
                                    //'money'=>'0.01',    //调试
                                    'return_url'=>url('endpay',['order_id'=>$order_ids]),
                                    'banktype'=>in_weixin() ? 'weixin' : 'alipay' , //在微信端,就用微信支付,否则就用支付宝支付
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
