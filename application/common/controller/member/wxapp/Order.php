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
    public function index($type=''){
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
        
        $listdb = $this->model->getList($map,5);
        $listdb = getArray($listdb);
        return $this->ok_js($listdb);
    }
    
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