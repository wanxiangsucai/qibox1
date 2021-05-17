<?php
namespace app\common\controller\member\wxapp;

use app\common\controller\MemberBase;
use app\index\model\Pay AS PayModel;

abstract class Order extends MemberBase
{
    protected $model;
    protected function _initialize(){
        parent::_initialize();        
        preg_match_all('/([_a-z0-9]+)/i',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model        = get_model_class($dirname,'order');
    }
    
    /**
     * 订单列表
     * @param string $type 订单类型,已付款,未付款
     * @return \think\response\Json
     */
    public function index($type='',$rows=5){        
        return $this->ok_js( $this->get_order_data($type,$rows) );
    }
    
    /**
     * 我的订单数据列表
     * @param string $type 订单类型,已付款,未付款
     * @param unknown $rows
     * @return array|NULL[]|unknown
     */
    protected function get_order_data($type='',$rows){
        $map=[
            'uid'=>$this->user['uid'],
        ];
        
        if($type=='ispay'){
            $map['pay_status'] = 1;
        }elseif($type=='nopay'){
            $map['pay_status'] = 0;
        }elseif($type=='waitsend'){
            $map['pay_status'] = 1;
            $map['shipping_status'] = 0;
        }elseif($type=='havesend'){
            $map['shipping_status'] = 1;
        }elseif($type=='success'){
            $map['pay_status'] = 1;
            $map['receive_status'] = 1;
        }
        
        $listdb = $this->model->getList($map,$rows);
        return getArray($listdb);
    }
    
    /**
     * 我的订单数据列表另一种展示形式
     * @param string $type 订单类型,已付款,未付款
     * @param unknown $rows
     * @return array|NULL[]|unknown
     */
    public function app_index($type='',$rows=5){
        $data = [];
        $array = $this->get_order_data($type,$rows);
        foreach ($array['data'] AS $rs){
            $data[] = [
                'basic' =>[
                    'type' => 'basic',
                    'data' =>[
                        'field' =>[
                            'order_id' => $rs['id'],
                            'order_sn' => $rs['order_sn'],
                            'goods_amount' => $rs['totalmoney'],
                            'order_amount' => $rs['pay_money'],
                            'order_time' => $rs['create_time'],
                            'buyer_remark' => '',
                            'seller_remark' => '',
                            'pay_time' => '',
                            'shipping_time' => '',
                            'cancel_time' => '',
                            'refund_time' => '',
                            'complete_time' => '',
                        ],
                    ],
                ],
                'status' =>[
                    'type' => 'status',
                    'data' =>[
                        'field' =>[
                            'text' => $rs['pay_status']==1?'已支付':'待支付',
                            'note' => '',
                        ],
                        'style' =>[
                            'color' => $rs['pay_status']==1?'#fff000':'#333333',
                            'background' => $rs['pay_status']==1?'#fff000':'#e93323',
                        ],
                    ],
                ],
                'goods' =>[
                    'type' => 'goods',
                    'data' =>[
                        'list' =>$this->get_goods($rs['shop_db'],$rs),
                    ],
                ],
                'operation' =>[
                    'type' => 'operation',
                    'data' =>[
                        'list' =>$rs['pay_status']==1?[]:[
                            [
                                'label' => '立即支付',
                                'code' => 'payment',
                            ],
                            [
                                'label' => '取消订单',
                                'code' => 'cancel',
                            ],
                        ],
                    ],
                ],
            ];
        }
        return $this->ok_js([
            'total' => count($data),
            'list' =>$data,
        ]);
    }
    
    
    protected function get_goods($array=[],$order_info=[]){
        $goods = [];
        foreach ($array AS $rs){
            $goods[] = [
                'goods_sku_id' => '',
                'goods_price' => $rs['_price'],
                'goods_number' => $rs['_num'],
                'integral' => 0,
                'goods_name' => $rs['title'],
                'goods_image' => $rs['picurl'],
                'goods_sku_text' => $rs['_type1'].' '.$rs['_type2'].' '.$rs['_type3'].(function_exists('format_order_time')?format_order_time($order_info):''),
                'order_goods_id' => '',
                'is_comment' => 0,
                'order_sn' => '',
                'goods_amount' => $rs['_price'],
                'refund_status' => 0,
                'operation' =>[],
            ];
        }
        return $goods;
    }
    
    /**
     * 查看某条订单
     * @param number $id
     * @return void|\think\response\Json|void|unknown|\think\response\Json
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
    
    /**
     * 某条订单的另一种展示形式
     * @param string $order_sn
     * @param number $id
     * @return void|unknown|\think\response\Json
     */
    public function app_show($order_sn='',$id=0){
        if (!$id) {
            $id = $this->model->where('order_sn',$order_sn)->value('id');
        }
        $info = $this->model->getInfo($id);
        $data = [
            'basic' =>[
                'type' => 'basic',
                'data' =>[
                    'field' =>[
                        'order_sn' => $info['order_sn'],
                        'goods_amount' => $info['totalmoney'],
                        'order_amount' => $info['pay_money'],
                        'order_time' => $info['create_time'],
                        'buyer_remark' => '',
                        'seller_remark' => '',
                        'pay_time' => '',
                        'shipping_time' => '',
                        'cancel_time' => '',
                        'refund_time' => '',
                        'complete_time' => '',
                    ],
                ],
            ],
            'status' =>[
                'type' => 'status',
                'data' =>[
                    'field' =>[
                        'text' => $info['pay_status']?'已支付':'待支付',
                        'note' => '',
                    ],
                    'style' =>[
                        'color' => '#333333',
                        'background' => '#e93323',
                    ],
                ],
            ],
            'goods' =>[
                'type' => 'goods',
                'data' =>[
                    'list' =>$this->get_goods($info['shop_db'],$info),
                ],
            ],
            'countdown' =>[
                'type' => 'countdown',
                'data' =>[
                    'field' =>[
                        'second' => -12613,
                    ],
                ],
            ],
            'address' =>[
                'type' => 'address',
                'data' =>[
                    'icon' => '',
                    'field' =>[
                        'contact' => $info['linkman'],
                        'contact_number' => $info['telphone'],
                        'gender' => 1,
                        'province' => '',
                        'city' => '',
                        'district' => '',
                        'address' =>  $info['address'],
                        'house_number' => '',
                        'longitude' => '',
                        'latitude' => '',
                        'province_name' => '',
                        'city_name' => '',
                        'district_name' => '',
                        'street_name' => '',
                        'full_address' => $info['address'],
                    ],
                ],
            ],
            'logistics' =>[
                'type' => 'logistics',
                'show' => 0,
                'data' =>[
                    'field' =>[
                        'text' => '暂无物流记录',
                        'time' => '2021-05-12 13:21:25',
                    ],
                ],
            ],
            'charges' =>[
                'type' => 'charges',
                'data' =>[
                    'list' =>[
                        [
                            'label' => $info['totalmoney'],
                            'text' => $info['totalmoney'],
                            'highlight' => 0,
                        ],
//                         [
//                             'label' => '运费',
//                             'text' => '+¥0.00',
//                             'highlight' => 0,
//                         ],
                        [
                            'label' => '优惠券',
                            'text' => '-¥0.00',
                            'highlight' => 0,
                        ],
                        [
                            'label' => '积分抵扣',
                            'text' => '-¥0.00',
                            'highlight' => 0,
                        ],
                        [
                            'label' => '实付款',
                            'text' => $info['pay_money'],
                            'highlight' => 1,
                        ],
                    ],
                ],
            ],
            'operation' =>[
                'type' => 'operation',
                'data' =>[
                    'list' =>$info['pay_status']?[]:[
                        [
                            'label' => '立即支付',
                            'code' => 'payment',
                        ],
                        [
                            'label' => '取消订单',
                            'code' => 'cancel',
                        ],
                    ],
                ],
            ],
            'info' =>[
                'type' => 'info',
                'data' =>[
                    'list' =>[
                        [
                            'label' => '订单编号',
                            'text' => $info['order_sn'],
                            'highlight' => 0,
                            'copy' => 0,
                        ],
                        [
                            'label' => '下单时间',
                            'text' => $info['create_time'],
                            'highlight' => 0,
                            'copy' => 0,
                        ],
//                         [
//                             'label' => '支付方式',
//                             'text' => '在线支付',
//                         ],
//                         [
//                             'label' => '支付流水',
//                             'text' => '',
//                         ],
                    ],
                ],
            ],
        ];
        return $this->ok_js($data);
    }
    
    public function chekpay($id=0,$numcode=''){
        $info = PayModel::get(['numcode'=>$numcode]);
//         if($info['ifpay']==1){
//             //现在只是调用用,这里需要做进一步的权限判断!!!!!! 
//             $data = [
//                     'id'=>$id,
//                     'pay_status'=>1,
//                     'pay_time'=>time(),                    
//             ];
//             $this->model->update($data);
//             $this->ok_js([],'支付成功');
//         }
    }
    
}