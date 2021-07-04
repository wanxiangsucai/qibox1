<?php
namespace app\common\controller\member\wxapp;

//use app\common\controller\MemberBase;
use app\index\model\Pay AS PayModel;

abstract class Order extends KehuOrder
{
    
//     protected $model;
//     protected function _initialize(){
//         parent::_initialize();        
//         preg_match_all('/([_a-z0-9]+)/i',get_called_class(),$array);
//         $dirname = $array[0][1];
//         $this->model        = get_model_class($dirname,'order');
//     }
    
    /**
     * 订单列表
     * @param string $type 订单类型,已付款,未付款
     * @return \think\response\Json
     */
    public function index($type='',$rows=5){        
        return $this->ok_js( $this->get_order_data($type,$rows) );
    }
    
    /**
     * 获取订单数据列表
     * @param string $type 订单类型,已付款,未付款
     * @param number $rows
     * @param array $map 
     * @return array|NULL[]|unknown
     */
    protected function get_order_data($type = '' , $rows = 10 , $map = []){
        if (!$map) {
            $map = [
                'uid'=>$this->user['uid'],
            ];
        }
        
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
                'basic'     =>  $this->order_base_field($rs),
                'status'    =>  $this->order_status($rs),
                'goods'     =>  $this->get_goods($rs['shop_db'],$rs),
                'operation' =>  $this->order_menu($rs),
            ];
        }
        return $this->ok_js([
            'total' => $array['total'],
            'pages' => $array['last_page'],
            'list' =>$data,
        ]);
    }
    
    /**
     * 客户的的订单数据列表另一种展示形式
     * @param string $type 订单类型,已付款,未付款
     * @param unknown $rows
     * @return array|NULL[]|unknown
     */
    public function kehu_index($type='',$rows=5){
        $data = [];
        $array = $this->get_order_data($type,$rows,['shop_uid'=>$this->user['uid']]);
        foreach ($array['data'] AS $rs){
            $data[] = [
                'basic'     =>  $this->order_base_field($rs),
                'status'    =>  $this->order_status($rs),
                'goods'     =>  $this->get_goods($rs['shop_db'],$rs),
                'operation' =>  $this->orderkehu_menu($rs),
            ];
        }
        return $this->ok_js([
            'total' => $array['total'],
            'pages' => $array['last_page'],
            'list' =>$data,
        ]);
    }
    
    /**
     * 获取每个订单中的每一个商品，可能有多个商品。
     * @param array $array
     * @param array $order_info
     * @return string[]|string[][][][]|number[][][][]|array[][][][]|unknown[][][][]
     */
    protected function get_goods($array=[],$order_info=[]){
        $goods = [];
        foreach ($array AS $rs){
            $data = [
                'goods_id' => $rs['id'],
                'id' => $rs['id'],
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
            unset($rs['sncode'],$rs['password']);
            $goods[] = array_merge($rs,$data);
        }
        return [
            'type' => 'goods',
            'data' =>[
                'list' =>$goods,
            ],
        ];
    }
    
    /**
     * 查看某条订单
     * @param number $id
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function show($id=0){
        $info = $this->model->getInfo($id);
        
        if($info && $info['uid']!=$this->user['uid'] && $info['shop_uid']!=$this->user['uid']){
            return $this->err_js('你不能查看别人的信息!');
        }
        
        if($info){
            return $this->ok_js($info);
        }else{
            return $this->err_js('数据不存在!');
        }
    }
    
    
    protected function order_base_field($info=[]){
        $callback_class = mymd5('app\\'.config('system_dirname').'\\model\\Order@pay@'.$info['id']);
        if($info['fewmoney']>0&&$info['few_ifpay']==0){
            $callback_class = mymd5('app\\'.config('system_dirname').'\\model\\Order@payfew@'.$info['id']);
        }
        $data = [
                'order_sn' => $info['order_sn'],
                'order_id' => $info['id'],
                'pay_status' => $info['pay_status'],
                'shipping_status' => $info['shipping_status'],
                'receive_status' => $info['receive_status'],
                'goods_amount' => $info['totalmoney'],
                'order_amount' => $info['pay_money'],
                'pay_money' => $info['pay_money'],
                'fewmoney' => $info['fewmoney']?:0,
                'order_time' => $info['create_time'],
                'uid' => $info['uid'],
                'linkman' => $info['linkman'],
                'buyer_remark' => '',
                'seller_remark' => '',
                'pay_time' => '',
                'shipping_time' => '',
                'cancel_time' => '',
                'refund_time' => '',
                'complete_time' => '',
                'callback_class'=>$callback_class,
        ];
        return [
            'type' => 'basic',
            'data' =>[
                'field' =>array_merge($info,$data),
            ],
        ];
    }
    
    protected function order_status($info=[]){
        if($info['fewmoney']>0){
            if($info['pay_status']!=0){
                $msg = '已付全款';
                $color = 'red';
            }elseif($info['few_ifpay']!=0){
                $msg = '已付订金,未付尾款';
                $color = 'blue';
            }else{
                $msg = '未付款';
                $color = '#333333';
            }
        }else{
            $msg = $info['pay_status']?'已支付':'待支付';
            $color = $info['pay_status']?'#f70202':'#333333';
        }
        return [
            'type' => 'status',
            'data' =>[
                'field' =>[
                        'text' => $msg,
                        'note' => '',
                ],
                'style' =>[
                        'color' => $color,
                        'background' => '#e93323',
                ],
            ],
        ];
    }
    
    protected function order_address($info=[]){
        return [
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
        ];
    }
    
    protected function order_wuliu($info=[]){
        return [
            'type' => 'logistics',
            'show' => 0,
            'data' =>[
                'field' =>[
                    'text' => '暂无物流记录',
                    'time' => '2021-05-12 13:21:25',
                ],
            ],
        ];
    }
    
    protected function order_base_info($info=[]){
        $order_field = [
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
        ];
        if($info['order_field']){   //前台单个商品自定义字段
            $f_array = fun('field@order_field_post',$info['shop_db'][0]['order_filed']);            
            $order_info = fun('field@order_field_format',$info['order_field'],$f_array);
            foreach($f_array AS $rs){
                if($order_info[$rs['name']]){
                    $order_field[] = [
                        'label' => $rs['title'],
                        'text' => del_html($order_info[$rs['name']]),
                    ];
                }                
            }
        }else{
            $form_items = \app\common\field\Form::get_all_field(-1,$info);      //后台自定义字段
            $info = fun('field@format',$info,'','show','',$form_items);         //数据转义
            foreach($form_items AS $rs){
                if($info[$rs['name']] && !in_array($rs['name'], ['linkman','telphone','address','ifolpay'])){
                    $order_field[] = [
                        'label' => $rs['title'],
                        'text' => del_html($info[$rs['name']]),
                    ];
                }
            }
        }
        return [
            'type' => 'info',
            'data' =>[
                'list' =>$order_field,
            ],
        ];
    }
    
    protected function order_des($info=[]){
        return [
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
        ];
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
        if($info && $info['uid']!=$this->user['uid'] && $info['shop_uid']!=$this->user['uid']){
            return $this->err_js('你不能查看别人的信息!');
        }
        $data = [
            'basic'     =>  $this->order_base_field($info),
            'status'    =>  $this->order_status($info),
            'goods'     =>  $this->get_goods($info['shop_db'],$info),
            'operation' =>  $this->order_menu($info),
            'address'   =>  $this->order_address($info),
            'logistics' =>  $this->order_wuliu($info),
            'info'      =>  $this->order_base_info($info),
            'charges'   =>  $this->order_des($info),
            'countdown' =>  [
                'type' => 'countdown',
                'data' =>[
                    'field' =>[
                        'second' => -12613,
                    ],
                ],
            ],
        ];
        return $this->ok_js($data);
    }
    
    /**
     * 查看客户的订单
     * @param string $order_sn
     * @param number $id
     * @return void|unknown|\think\response\Json
     */
    public function keuhu_show($order_sn='',$id=0){
        if (!$id) {
            $id = $this->model->where('order_sn',$order_sn)->value('id');
        }
        $info = $this->model->getInfo($id);
        if($info && $info['uid']!=$this->user['uid'] && $info['shop_uid']!=$this->user['uid']){
            return $this->err_js('你不能查看别人的信息!');
        }
        $data = [
            'basic'     =>  $this->order_base_field($info),
            'status'    =>  $this->order_status($info),
            'goods'     =>  $this->get_goods($info['shop_db'],$info),
            'operation' =>  $this->orderkehu_menu($info),
            'address'   =>  $this->order_address($info),
            'logistics' =>  $this->order_wuliu($info),
            'info'      =>  $this->order_base_info($info),
            'charges'   =>  $this->order_des($info),
            'countdown' =>  [
                'type' => 'countdown',
                'data' =>[
                    'field' =>[
                        'second' => -12613,
                    ],
                ],
            ],
        ];
        return $this->ok_js($data);
    }
    
    /**
     * 顾客的操作菜单
     * @param array $info
     * @return string[]|string[][][][]
     */
    protected function order_menu($info=[]){
        if($info['pay_status']==0){
            if($info['fewmoney']>0){
                if($info['few_ifpay']==0){
                    $array = [
                            [
                                    'label' => '支付订金',
                                    'code' => 'payment',
                            ],
                            [
                                    'label' => '取消订单',
                                    'code' => 'delete',
                            ],
                    ];
                }else{
                    $array = [
                            [
                                    'label' => '支付尾款',
                                    'code' => 'payment',
                            ],
                            [
                                    'label' => '申请退订',
                                    'code' => 'tuifew',
                            ],
                    ];
                }     
            }else{
                $array = [
                        [
                                'label' => '立即支付',
                                'code' => 'payment',
                        ],
                        [
                                'label' => '取消订单',
                                'code' => 'delete',
                        ],
                ];
            }            
        }elseif($info['pay_status']==1 && $info['shipping_status']==0){
            $array = [
                [
                    'label' => '申请退款',
                    'code' => 'tuimoney',
                ],
            ];
        }elseif($info['pay_status']==1 && $info['shipping_status']==1){
            $array = [
                [
                    'label' => '申请退货',
                    'code' => 'tuimoney',
                ],
                [
                    'label' => '签收',
                    'code' => 'receive',
                ],
            ];
        }elseif($info['pay_status']==-1){
            $array = [
                [
                    'label' => '放弃退款',
                    'code' => 'cancelmoney',
                ],
            ];
        }
        return [
            'type' => 'operation',
            'data' =>[
                'list' =>($info['pay_money']>0||$info['fewmoney']>0)?$array:[],
            ],
        ];
    }
    
    /**
     * 商家的操作菜单
     * @param array $info
     * @return string[]|string[][][][]
     */
    protected function orderkehu_menu($info=[]){
        if($info['pay_status']==0 && $info['few_ifpay']==0){
            $array = [
                [
                    'label' => '删除订单',
                    'code' => 'deletekehu',
                ],
            ];
        }elseif($info['pay_status']==1 && $info['shipping_status']==0){
            $array = [
                [
                    'label' => '发货',
                    'code' => 'shipping',
                ],
            ];
        }elseif($info['pay_status']==-1){
            $array = [
                [
                    'label' => '同意退款',
                    'code' => 'allowmoney',
                ],
            ];
        }elseif($info['shipping_status']==1){
            $array = [
                [
                    'label' => '已发货',
                    'code' => 'shipping',
                ],
            ];
        }
        return [
            'type' => 'operation',
            'data' =>[
                'list' =>$array,
            ],
        ];
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