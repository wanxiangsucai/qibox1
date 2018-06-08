<?php
namespace app\appstore\member;

use app\appstore\model\Order AS OrderModel;
class Order extends Content
{
    protected $model;
    protected function _initialize(){
        parent::_initialize();        
        $this->model = new OrderModel();
    }
    
    //订单重新付款
    public function pay($id,$havepay=0){
        $info = $this->model->get($id);
        if ($havepay==1) {
            if($this->model->pay($id)){
                $this -> success('支付成功', 'index');
            }else{
                $this->error('支付失败');
            }
        }        
        //直接跳转支付
        post_olpay([
                'money'=>'0.01',
                'return_url'=>url('pay',['id'=>$id,'havepay'=>1]),
                'banktype'=>'alipay',
                'numcode'=>$info['order_sn'],
                'callback_class'=>mymd5('app\\'.config('system_dirname').'\\model\\Order@pay@'.$id),
        ] , true);
        
    }
    
    public function delete($id){
        if ($this->model->destroy($id)) {
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
    
    //订单列表
    public function index($type=null){
        $map=[];
        if($type=='ispay'){
            $map=[ 'pay_status'=>1 ];
        }elseif($type=='nopay'){
            $map=[ 'pay_status'=>0 ];
        }
        $list_data = $this->model->getList($map,10);       
        $this->assign('listdb',getArray($list_data)['data']);
        $this->assign('pages',$list_data->render());
        $this->assign('type',$type);
        return $this->fetch();
    }
    
    public function edit($id){
        $info = $this->model->getInfo($id);
        $this->assign('info',$info);
        return $this->fetch();
    }
    
    //订单详情
    public function show($id){
        $info = $this->model->getInfo($id);
        $this->assign('info',$info);
        return $this->fetch();
    }
}