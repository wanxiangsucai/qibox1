<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase;
use app\member\model\Address AS AddressModel;

/**
 * 小程序 用户下单
 * @author Administrator
 *
 */
class Order extends IndexBase
{
    protected $model;
    protected $car_model;
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z0-9]+)/i',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'order');
        $this->car_model = get_model_class($dirname,'car');
    }
    
    /**
     * 用户下单
     * @param number $address_id 用户地址ID
     * @return \think\response\Json
     */
    public function add($address_id=0,$id=0){        
        $map = [
            'uid'=>$this->user['uid'],
        ];
        if($address_id){
            $map['id'] = $address_id;
        }
        $info = getArray(AddressModel::where($map)->order('often desc,id desc')->find());     //用户的地址   
        $data = [
                'linkman'   => $info['user']?:'',
                'telphone'  => $info['telphone']?:'',
                'address'   => $info['address']?:'',
        ];
        $data['uid']            = $this -> user['uid'];
        $data['create_time']    = time();
        $data['order_day']      = input('order_day')?:'';
        $data['order_tid']      = input('order_tid')?:'';
        
        $post_data = $this->request->post();
        $check_field = ['order_field','user_note','linkman','telphone','address'];
        $form_items = \app\common\field\Form::get_all_field(-1);      //后台自定义字段
        foreach($form_items AS $rs){
            $check_field[] = $rs['name'];
        }        
        foreach($post_data AS $key=>$value){
            if(!in_array($key, $check_field)){
                unset($post_data[$key]);    //过滤不安全的字段
            }
        }
        
        $data = array_merge($data,$post_data);  //方便用户自定义表单项重新替换 数据库的 linkman  telphone address
        $data['order_field'] = $post_data['order_field']?:'';
        $data['user_note'] = $post_data['user_note']?:'';
        
        $order_ids = [];
        $listdb = $this->car_model->getList($this->user['uid'],1);
        $total_money = 0;   //需要支付的总金额
        foreach ($listdb AS $uid=>$shop_array){     //取每一个商家的数据生成一个订单,不能同家不能混在同一个订单
            $data['shop_uid'] = $uid;   //店主UID
            $_shop = [];
            $money = 0;
            foreach ($shop_array AS $rs){   //某个商家的多个商品
                $_shop[] = $rs['_car_']['shopid'] . '-' . $rs['_car_']['num']  . '-' . $rs['_car_']['type1'] . '-' .$rs['_car_']['type2'] . '-' .$rs['_car_']['type3'];
                $money += $rs['_price']*$rs['_num'];
            }
            $data['shop'] = implode(',', $_shop);
            $data['order_sn'] = 's'.date('ymdHis').rands(3);      //订单号
            $data['totalmoney'] = $data['pay_money'] = $money;
            $total_money +=$money;            
            if (($result = $this->model->create($data))!=false) {
                $order_ids[] = $result->id;
            }
        }
        if($order_ids){
            $this->car_model->where(['uid'=>$this->user['uid'],'ifchoose'=>1])->delete();
            return $this->ok_js([
                'order_ids'         =>  $order_ids,
                'totalmoney'        =>  $total_money,
                'callback_class'    =>  mymd5('app\\'.config('system_dirname').'\\model\\Order@pay@'.implode(',', $order_ids))
            ],'下单成功');
        }else{
            return $this->err_js('下单失败');
        }
    }
    
    public function index(){
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













