<?php
namespace app\common\controller\member;

use app\common\controller\MemberBase;


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
     * 订单之前没付款, 现在重新付款
     * 在微信端,就用微信支付,否则就用支付宝支付
     * @param number $id 订单ID
     * @param number $havepay 1代表进入付款界面之后,再跳转回来的, 有可能支付成功,也有可能支付失败或放弃支付
     * @param number $ispay 进入付款界面之后,1是支付成功,0是支付失败或放弃支付
     */
    public function pay($id=0,$havepay=0,$ispay=0){
        $info = $this->model->get($id);
        if ($havepay==1) {
            if($ispay==1){
                if($this->model->pay($id)){
                    $this -> success('已付款，订单处理成功', 'index');
                }else{
                    $this->error('已付款，订单处理失败', 'index');
                }
            }else{
                $this->error('你并没有付款，订单未作处理', 'index');
            }            
        }
        //直接跳转支付
        post_olpay([
                //'money'=>'0.01',
                'money'=>$info['pay_money'],
                'return_url'=>url('pay',['id'=>$id,'havepay'=>1]),
                'banktype'=>in_weixin() ? 'weixin' : 'alipay' , //在微信端,就用微信支付,否则就用支付宝支付
                'numcode'=>$info['order_sn'],
                'callback_class'=>mymd5('app\\'.config('system_dirname').'\\model\\Order@pay@'.$id),
        ] , true);
        
    }
    
    /**
     * 确认收货
     * @param number $id
     */
    public function receive($id=0){
        $info = getArray($this->model->getInfo($id));
        if ($info['uid']!=$this->user['uid'] && $info['shop_uid']!=$this->user['uid']) {
            $this->error('你没权限');
        }
        $result = $this->model->update([
                'id'=>$id,
                'receive_status'=>1,
                'receive_time'=>time(),
        ]);
        if ($result) {
            $this->success('确认成功');
        }else{
            $this->error('执行失败');
        }
    }
    
    /**
     * 删除订单
     * @param unknown $id
     */
    public function delete($id){
        $info = getArray($this->model->getInfo($id));
        if ($info['uid']!=$this->user['uid']) {
            $this->error('你没权限');
        }elseif ( $info['pay_status']!=0 || ($info['few_ifpay']!=0&&$info['fewmoney']>0) ) {
            $this->error('已支付的订单不能删除');
        }
        if ($this->model->destroy($id)) {
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
    
    /**
     * 退款
     * @param number $id
     */
    public function tui_money($id=0){
        $info = getArray($this->model->get($id));
        if ($info['uid']!=$this->user['uid']) {
            $this->error('不是你的订单!');
        }elseif ($info['status']==-1) {
            $this->error('交易已关闭!');
        }elseif ($info['status']==1) {
            $this->error('交易成功不能再申请退款!');
        }elseif ($info['few_ifpay']==-1) {
            $this->error('订金还在退还当中,请耐心等待!');
        }elseif ($info['pay_status']==0) {
            $this->error('你还没付全款!');
        }elseif ( $info['pay_status']==-1 ) {
            $this->error('退款正等待商家处理中,请耐心等待!');
        }
        $this->model->update([
            'id'=>$id,
            'pay_status'=>-1,
        ]);
        $url = get_url(murl('order/show',['id'=>$id]));
        $content = '<a href="'.$url.'" target="_blank">'.$this->user['username'].' 申请退款，订单号是:'.$info['order_sn'].'</a>';
        send_msg($info['shop_uid'],$this->user['username'].'申请退款',$content);
        send_wx_msg($info['shop_uid'], $content);
        $this->success('退款申请已提交,需要商家同意才能把款项退还到你帐户余额');
    }
    
    /**
     * 取消退全款
     * @param number $id
     */
    public function cancel_money($id=0){
        $info = getArray($this->model->get($id));
        if ($info['uid']!=$this->user['uid']) {
            $this->error('不是你的订单!');
        }elseif ($info['pay_status']!=-1) {
            $this->error('该订单并没有处于退款申请中!');
        }
        $this->model->update([
            'id'=>$id,
            'pay_status'=>1,
        ]);
        $url = get_url(murl('order/show',['id'=>$id]));
        $content = '<a href="'.$url.'" target="_blank">'.$this->user['username'].' 取消退款申请了，订单号是:'.$info['order_sn'].'</a>';
        send_msg($info['shop_uid'],$this->user['username'].'申请退款',$content);
        send_wx_msg($info['shop_uid'], $content);
        $this->success('退款申请已成功取消！');
    }
    
    /**
     * 查看我的订单列表
     * @param unknown $type
     * @return mixed|string
     */
    public function index($type=null){
        $map = [
                'uid'=>$this->user['uid'],                
        ];
        
        if($type=='ispay'){
            $map['pay_status'] = 1;
        }elseif($type=='nopay'){
            $map[ 'pay_status'] = 0;
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
        if ($info['uid']!=$this->user['uid']) {
            $this->error('你没权限');
        }
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
        
        if ($info['uid']!=$this->user['uid']) {
            $this->error('你没权限');
        }
        
        $info = $this->format_info($info);
        
        $this->assign('info',$info);
        return $this->fetch();
    }
    
    /**
     * 前台或后台自定义字段处理
     * @param array $info
     * @return array
     */
    protected function format_info($info=[]){
        if (count($info['shop_db'])==1 && $info['shop_db'][0]['order_filed']) { //前台自定义字段的处理
            $f_array = fun('field@order_field_post',$info['shop_db'][0]['order_filed']);
            $this->assign('f_array',$f_array); //用户自定义表单字段,
            $order_info = fun('field@order_field_format',$info['order_field'],$f_array);
            $info = array_merge($info,$order_info);
        }else{
            $form_items = \app\common\field\Form::get_all_field(-1,$info);         //后台自定义字段
            $info = fun('field@format',$info,'','show','',$form_items);      //数据转义
        }
        return $info;
    }
    
}