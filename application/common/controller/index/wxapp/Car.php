<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase;
use app\member\model\Address AS AddressModel;

/**
 * 小程序购物车
 * @author Administrator
 *
 */
class Car extends IndexBase
{
    protected $model;
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z0-9]+)/i',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'car');
    }
    
    /**
     * 列出我的购物车为1的时候,列出选中的商品
     * @param number $type 为1的时候,列出选中的商品
     * @return \think\response\Json
     */
    public function index($type=''){
        $listdb = $this->get_cart_data($type?1:null);
        return $this->ok_js($listdb);
    }
    
    
    /**
     * 获取购物车数据
     * @param number $type 为1的时候,列出选中的商品
     * @return string[][]|void[][]|unknown[][][]|unknown[][]
     */
    protected function get_cart_data($type=null){
        $car_array = $this->model->getList($this->user['uid'],$type);
        $listdb = [];
        foreach($car_array AS $seller_uid=>$array){
            $shops = [];
            foreach($array AS $rs){
                $shops[] = $rs;
            }
            $listdb[] = [
                'uid'=>$seller_uid, //商铺卖家资料
                'icon'=>get_user_icon($seller_uid),
                'shops'=>$shops,    //用户购买了该商家的所有商品
            ];
        }
        return $listdb;
    }
    
    
    
    /**
     * 商品详情页,查询购物车状态
     * @param number $id 商品ID
     * @return \think\response\Json
     */
    public function getbyid($id=0){
        if (empty($this->user)) {
            return $this->err_js('你还没登录');
        }else{
            $mytotal = $this->model->where('uid',$this->user['uid'])->sum('num');  //我的购物车商品数
            if($mytotal){
                $info = getArray( $this->model->get(['shopid'=>$id]) );
                $type1 = $info['type1'] - 1;
                $type2 = $info['type2'] - 1;
                $type3 = $info['type3'] - 1;                
            }
            $data = [
                    'type1'=>$type1<0?0:$type1,
                    'type2'=>$type2<0?0:$type2,
                    'type3'=>$type3<0?0:$type3,
                    'num'=>$info['num']<1?1:$info['num'],
                    'mytotal'=>$mytotal,
            ];
        }
        return $this->ok_js($data);
    }
    
    /**
     * 购物车的相关操作
     * @param number $shopid 商品ID
     * @param string $type  操作类型
     * @param number $num   数量
     * @param number $type1 商品属性1
     * @param number $type2 商品属性2
     * @param number $type3 商品属性3
     * @return string
     */
    private function act($shopid=0,$type='',$num=0,$type1=0,$type2=0,$type3=0){
        if (!$shopid) {
            return 'fail';
        }elseif (!$this->user) {
            return 'fail';
        }
        
        $info = $this -> model -> where(['shopid'=>$shopid,'uid'=>$this->user['uid']]) -> find() ;

        if(!$info){    //购物车没有该商品的话,就直接加进去
            $num<1 && $num=1;
            $data = [
                    'shopid'=>$shopid,
                    'uid'=>$this->user['uid'],
                    'type1'=>$type1,
                    'type2'=>$type2,
                    'type3'=>$type3,
                    'num'=>$num,
            ];
            if ($this -> model -> create($data)) {
                return 'ok';
            } else {
                return 'fail';
            }
        }else{
            if($type=='plus'){     //购物车页面简单的加减数据
                $_num = $info['num']+intval($num);  //$num可以是负数
                if($_num<1){
                    $_num = 0;
                }
                $data = [
                        'id'=>$info['id'],
                        'num'=>$_num,
                ];
            }elseif($type=='change_num'){   //直接修改购买数量
                $data = [
                        'id'=>$info['id'],
                        'num'=> intval($num),
                ];
            }elseif($type=='choose'){   //是否选中 下单
                $data = [
                        'id'=>$info['id'],
                        'ifchoose'=> input('ck')==1 ? 1 : 0,
                ];
            }else{
                $data = [
                        'id'=>$info['id'],
                        'type1'=>$type1,
                        'type2'=>$type2,
                        'type3'=>$type3,
                        'num'=>$num,
                ];
            }
            
            if ($this -> model -> update($data)) {
                return 'ok';
            } else {
                return 'fail';
            }
        }
    }
    
    
    /**
     * 踢除一个商品
     * @param unknown $id 商品ID
     * @return \think\response\Json
     */
    public function delete_one($id=0){
        if ($this -> model ->where(['shopid'=>$id,'uid'=>$this->user['uid']]) -> delete()) {
            return $this->ok_js([],'删除成功');
        } else {
            return $this->err_js('删除失败');
        }
    }
    
    /**
     * 清空购物车
     * @return unknown
     */
    public function clear(){
        if($this -> model -> destroy(['uid'=>$this->user['uid']])){
            return $this->ok_js([],'清除成功');
        } else {
            return $this->err_js('清空失败');
        }
    }
    
    /**
     * 勾选商品
     * @param number $id 商品ID
     * @param number $choose 选择状态 0 或 1
     * @return \think\response\Json
     */
    public function check_one($id=0,$choose=0){
        if($this -> model ->where(['uid'=>$this->user['uid'],'shopid'=>$id])-> update(['ifchoose'=>$choose])){
            return $this->ok_js([],'选中成功');
        } else {
            return $this->err_js('选中失败');
        }
    }
    
    /**
     * 购物车的相关操作
     * @param number $id 商品ID
     * @param string $type  操作类型
     * @param number $total 数量
     * @return \think\response\Json|\app\shop\index\wxapp\unknown
     */
    public function change($id=0,$type='',$total=1){
        $code = 1;
        $msg = '操作失败';
        if($type=='change_num'){
            if($this->act($id,'change_num',$total)=='ok'){
                return $this->ok_js([],'修改成功');
            }
        }elseif($type=='del'){
            return $this->delete_one($id);
        }elseif($type=='clear'){
            return $this->clear();
        }elseif($type=='choose'){
            return $this->check_one($id,input('choose'));
        }
        
        return $this->err_js($msg); 
    }
    
    /**
     * 商品详情页 把商品 加入购物车
     * @param number $id 商品ID
     * @param number $num 商品数量
     * @param number $type1 属性1
     * @param number $type2 属性2
     * @param number $type3 属性3
     * @return \think\response\Json
     */
    public function add($id = 0 , $num = 1 , $type1 = 0 , $type2 = 0 , $type3 = 0){
        $msg = '加入失败';        
        if(empty($this->user)){
            $code = 1;
            $msg = '请先登录';
        }
        
        if($this->act($id,'add',$num,$type1,$type2,$type3)=='ok'){
            return $this->ok_js([],'操作成功');
        }
        
        return $this->err_js($msg);
    }
    
    
    /**
     * 我的购物车另一种数据展现形式
     * @param unknown $type 为1的时候,列出选中的商品
     * @return void|unknown|\think\response\Json
     */
    public function uniapp_cart($type=null){
        $array = $this->get_cart_data($type?1:null);
        $total = $this->total_money($array);
        $total = sprintf("%.2f",$total).'';
        $data = [
            'items' =>[
                'type' => 'items',
                'data' =>[
                    'list' =>$this->get_goods($array),
                ],
            ],
            'submit' =>[
                'type' => 'submit',
                'data' =>[
                    'field' =>[
                        'note' => '',
                        'is_all_checked' => $this->total_nocheck($array)?0:1,
                        'is_can_submit' => $this->total_check($array),
                        'total_price' =>$total,
                        'total_goods' => $this->total_num($array),
                    ],
                    'style' =>[
                        'background' => $this->total_check($array)?'#d81e06':'#CCCCCC',
                        'fontSize' => 40,
                    ],
                ],
                'show' => $this->total_nocheck($array)+$this->total_check($array),
            ],
        ];
        return $this->ok_js($data);
    }
    
    /**
     * 订单中获取默认地址
     * @return string[]|string[]|number[]|unknown[]|NULL[]
     */
    protected function getAddress(){
        $info = getArray(AddressModel::where('uid',$this->user['uid'])->order('often desc,id desc')->find());
        if (!$info) {
            return ['address_id' =>''];
        }
        return [
            'id' => $info['id'],
            'address_id' => $info['id'],
            'user_id' => '',
            'contact' => $info['user'],
            'contact_number' => $info['telphone'],
            'gender' => $info['sex'],
            'province' => '',
            'city' => '',
            'district' => '',
            'street' => '',
            'address' => '',
            'house_number' => '',
            'longitude' => '',
            'latitude' => '',
            'is_default' => 1,
            'status' => 1,
            'is_delete' => 0,
            'province_name' => '',
            'city_name' => '',
            'district_name' => '',
            'street_name' => '',
            'full_address' => $info['address'],
        ];
    }
    
    protected function get_charges($total_money=0,$array=[]){
        $data = [
            [
                'label' => '商品总金额',
                'text' => $total_money,
            ],
            [
                'label' => '优惠券',
                'text' => '-¥0.00',
            ],
            [
                'label' => '积分抵扣',
                'text' => '-¥0.00',
            ],
        ];
        return $data;
    }
    
    /**
     * 购物车提交前展示的形式
     * @param array $array
     * @return array[]|array[][]|number[][]|string[][][]|number[][][]|unknown[][][]|\think\response\Json[][][]|string[][][][]|number[][][][]|unknown[][][][]
     */
    public function uniapp_order_cart(){
        $address_info = $this->getAddress();
        $array = $this->get_cart_data(1);        
        $goods = $this->get_goods($array);
        $total_money = 0;
        foreach($goods AS $rs){
            $total_money += $rs['goods_number']*$rs['goods_price'];
        }
        $total_money = sprintf("%.2f",$total_money).'';
        $data = [
            'goods' =>['list' =>$goods],
            'address' =>[
                'field' =>$address_info,
                'form' =>['address_id' => $address_info['address_id']],
                'show' => 1,
            ],
            'coupon' =>[
                'show' => 0,
                'form' =>['user_coupon_id' => ''],
                'list' =>[],
                'field' =>['text' => '无可用优惠券','discount_amount' => 0,],
            ],
            'integral' =>[
                'field' =>[
                    'user' => 0,
                    'note' => '暂无积分可用',
                    'discount' => 0,
                    'discount_amount' => 0,
                    'integral' => 0,
                ],
                'switch' =>[
                    'disabled' => 1,
                    'checked' => 0,
                    'color' => '#f30',
                ],
                'form' =>['integral' => 0],
                'show' => 0,
            ],
            'charges' =>$this->get_charges($total_money,$array),
            'delivery_time' =>[
                'show' => 0,
                'form' =>['delivery_time' => ''],
                'field' =>['text' => '尽快送达'],
            ],
            'delivery_method' =>[''=>[]],
            'outlets' =>[],
            'submit' =>[
                'field' =>[
                    'note' => '',
                    'text' => '',
                    'order_amount' => $total_money,
                    'integral' => 0,
                ],
                'style' =>['background' => '#d81e06'],
            ],
        ];
        return $this->ok_js($data);
    }
    
    protected function get_goods($array=[]){
        $data = [];
        foreach($array AS $qs){
            foreach($qs['shops'] AS $rs){
                $data[] = [
                    'cart_id' => $rs['_car_']['id'],
                    'goods_id' => $rs['id'],
                    'goods_sku_key' => $rs['_car_']['type1'].','.$rs['_car_']['type2'].','.$rs['_car_']['type3'],
                    'goods_number' => $rs['_car_']['num'],
                    'is_checked' => $rs['_car_']['ifchoose'],
                    'goods_name' => $rs['title'],
                    'goods_image' => $rs['picurl'],
                    'shop_price' => $rs['_price'],
                    'goods_sku_text' => $rs['_type1'].' '.$rs['_type2'].' '.$rs['_type3'],
                    'goods_sku_id' => '153610691889303554',
                    'reason' => '',
                    'is_invalid' => 0,  //1是失效
                    'goods_price' => $rs['_price'],
                    'integral_discount' => 0,
                    'integral' => 0,
                ];
            }
        }
        return $data;
    }
    
    protected function total_money($array=[]){
        $total = 0;
        foreach($array AS $qs){
            foreach($qs['shops'] AS $rs){
                if ($rs['_car_']['ifchoose']) {
                    $total += $rs['_price']*$rs['_car_']['num'];
                }
            }
        }
        return $total;
    }
    
    protected function total_num($array=[]){
        $total = 0;
        foreach($array AS $qs){
            foreach($qs['shops'] AS $rs){
                if ($rs['_car_']['ifchoose']) {
                    $total += $rs['_car_']['num'];
                }
            }
        }
        return $total;
    }
    
    protected function total_check($array=[]){
        $total = 0;
        foreach($array AS $qs){
            foreach($qs['shops'] AS $rs){
                if ($rs['_car_']['ifchoose']) {
                    $total++;
                }
            }
        }
        return $total;
    }
    
    protected function total_nocheck($array=[]){
        $total = 0;
        foreach($array AS $qs){
            foreach($qs['shops'] AS $rs){
                if (!$rs['_car_']['ifchoose']) {
                    $total++;
                }
            }
        }
        return $total;
    }
}













