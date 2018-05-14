<?php
namespace app\shop\index\wxapp;

use app\common\controller\IndexBase;
use app\shop\model\Car as CarModel;
use app\member\model\Address AS AddressModel;
use app\shop\model\Order as OrderModel;

//小程序 用户下单
class Order extends IndexBase
{
    protected $model;
    protected function _initialize(){
        parent::_initialize();
        $this->model = new OrderModel();
    }
    
    /**
     * 用户下单
     * @param number $address_id 用户地址ID
     * @return \think\response\Json
     */
    public function add($address_id=0){        
        $map = [
                'uid'=>$this->user['uid'],
                'id'=>$address_id,
        ];
        $info = getArray(AddressModel::where($map)->order('often desc,id desc')->find());     //用户的地址   
        $data = [
                'linkman'=>$info['user'],
                'telphone'=>$info['telphone'],
                'address'=>$info['address'],
        ];
        
        $order_ids = [];
        $listdb = CarModel::getList($this->user['uid'],1);
        
        $total_money = 0;   //需要支付的总金额
        foreach ($listdb AS $uid=>$shop_array){     //取每一个商家的数据生成一个订单,不能同家不能混在同一个订单
            $data['shop_uid'] = $uid;   //店主UID
            $_shop = [];
            $money = 0;
            foreach ($shop_array AS $rs){   //某个商家的多个商品
                $_shop[] = $rs['_car_']['shopid'] . '-' . $rs['_car_']['num']  . '-' . $rs['_car_']['type1'] . '-' .$rs['_car_']['type2'] . '-' .$rs['_car_']['type3'];
                //$money += get_shop_price($rs,$rs['_car_']['type1']);
                $money += $rs['_price']*$rs['_num'];
            }
            $data['shop'] = implode(',', $_shop);
            $data['order_sn'] = rands(10);      //订单号
            $data['totalmoney'] = $data['pay_money'] = $money;
            $total_money +=$money;
            $data['uid'] = $this -> user['uid'];
            $data['create_time'] = time();
            if (($result = OrderModel::create($data))!=false) {
                $order_ids[] = $result->id;
            }
        }
        if($order_ids){
            CarModel::where(['uid'=>$this->user['uid'],'ifchoose'=>1])->delete();
            return $this->ok_js($order_ids,'下单成功');
        }else{
            return $this->err_js('下单失败');
        }
        
//         $goods= implode(',', $order_ids);
//         $array = [
//                 'meta' =>[
//                         'code' => 0,
//                         'message' => '下单成功',
//                 ],
//                 'data' =>[
//                         '_id' => $goods,
//                 ],
//         ];
//         return json($array);
    }
    
    public function index(){       
        //echo json_encode($data,JSON_UNESCAPED_UNICODE)."\n";
        //return json($data);
    }
    
    /**
     * 用户的具体某个订单
     * @param number $id
     * @return \think\response\Json
     */
    public function show($id=0){
        $info = $this->model->getInfo($id);
        
        if($info && $info['uid']!=$this->user['uid']){
            return $this->err_js('你不能查看别人的信息!');
        }
        
        if($info){
            return $this->ok_js($info);
        }else{
            return $this->err_js('数据不存在!');
        }
    }
}













