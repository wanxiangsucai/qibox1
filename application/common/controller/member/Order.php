<?php
namespace app\common\controller\member;

use app\common\controller\MemberBase;


abstract class Order extends MemberBase
{
    protected $model;
    protected function _initialize(){
        parent::_initialize();        
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model        = get_model_class($dirname,'order');
    }
    
    /**
     * 订单之前没付款, 现在重新付款
     * 在微信端,就用微信支付,否则就用支付宝支付
     * @param unknown $id 订单ID
     * @param number $havepay 之付前后判断
     */
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
                //'money'=>'0.01',
                'money'=>$info['pay_money'],
                'return_url'=>url('pay',['id'=>$id,'havepay'=>1]),
                'banktype'=>in_weixin() ? 'weixin' : 'alipay' , //在微信端,就用微信支付,否则就用支付宝支付
                'numcode'=>$info['order_sn'],
                'callback_class'=>mymd5('app-'.config('system_dirname').'-model-Order@pay@order_id|'.$id),
        ] , true);
        
    }
    
    /**
     * 删除订单
     * @param unknown $id
     */
    public function delete($id){
        if ($this->model->destroy($id)) {
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
    
    /**
     * 查看我的订单列表
     * @param unknown $type
     * @return mixed|string
     */
    public function index($type=null){
        $map=[
                'uid'=>$this->user['uid'],                
        ];
        
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
    
    /**
     * 修改一些基础信息
     * @param unknown $id
     * @return mixed|string
     */
    public function edit($id){
        $info = $this->model->getInfo($id);
        $this->assign('info',$info);
        return $this->fetch();
    }
    
    /**
     * 订单详情
     * @param unknown $id
     * @return mixed|string
     */
    public function show($id){
        $info = $this->model->getInfo($id);
        $this->assign('info',$info);
        return $this->fetch();
    }
    
}