<?php
namespace app\shop\member\wxapp;

use app\common\controller\MemberBase;
use app\shop\model\Order AS OrderModel;
use app\index\model\Pay AS PayModel;

class Order extends MemberBase
{
    protected $model;
    protected function _initialize(){
        parent::_initialize();        
        $this->model = new OrderModel();
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
        }
        
        $listdb = $this->model->getList($map,5);
        $listdb = getArray($listdb);
        return $this->ok_js($listdb);
    }
    
    public function chekpay($id=0,$numcode=''){
        $info = PayModel::get(['numcode'=>$numcode]);
        if($info){
            //现在只是调用用,这里需要做进一步的权限判断!!!!!! 
            $data = [
                    'id'=>$id,
                    'pay_status'=>1,
                    'pay_time'=>time(),                    
            ];
            $this->model->update($data);
            $this->ok_js([],'支付成功');
        }
    }
    
}