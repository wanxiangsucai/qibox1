<?php
namespace app\common\controller\member;

use app\common\controller\MemberBase;

abstract class KehuOrder extends MemberBase
{
    protected $model;
    protected function _initialize(){
        parent::_initialize();        
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model        = get_model_class($dirname,'order');
    }

    /**
     * 查看我的客户订单列表
     * @param unknown $type
     * @return mixed|string
     */
    public function index($type=null){
        $map = [
                'shop_uid'=>$this->user['uid'],                
        ];
        
        if($type=='ispay'){
            $map['pay_status'] = 1;
        }elseif($type=='nopay'){
            $map['pay_status'] = 0;
        }
        $list_data = $this->model->getList($map,10);
        $this->assign('listdb',getArray($list_data)['data']);
        $this->assign('pages',$list_data->render());
        $this->assign('type',$type);
        return $this->fetch();
    }
    
    /**
     * 删除未支付的订单
     * @param unknown $id
     */
    public function delete($id){
        $info = getArray($this->model->getInfo($id));
        if ($info['shop_uid']!=$this->user['uid']) {
            $this->error('你没权限');
        }elseif ( $info['pay_status']!=0 || $info['few_ifpay']!=0 ) {
            $this->error('已支付的订单不能删除');
        }
        if ($this->model->destroy($id)) {
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
    
    /**
     * 修改一些基础信息
     * @param unknown $id
     * @return mixed|string
     */
    public function edit($id){
        $info = $this->model->getInfo($id);
        if ($info['shop_uid']!=$this->user['uid']) {
            $this->error('你没权限');
        }
        if ($this->request->isPost()) {            
            $data = $this->request->post();
            $array = [
                    'id'=>$id,
            ];
            if($info['pay_status']==0 && $data['pay_money']>0){
                $array['pay_money'] = $data['pay_money'];   //未付款前可以修改订单价格
            }
            if($data['shipping_code']!=''){
                $array['shipping_code'] = $data['shipping_code'];
                $array['shipping_status'] = 1;  //标志已发货
                if ($data['shipping_code']!=$info['shipping_code']) {
                    $array['shipping_time'] = time();
                }                
            }else{
                $array['shipping_status'] = 0;
            }
            $this->model->update($array);
            
            if ($data['shipping_code'] && $data['shipping_code']!=$info['shipping_code']) {
                $content = "你购买的商品,已经发货了,请注意查收,<a href=\"".get_url(urls('order/show','id='.$id))."\">点击详情查看单号或序列号</a>";
                send_msg($info['uid'],'你购买的商品已发货了,注意查收',$content);
                send_wx_msg($info['uid'],$content);
            }
            $this->success('修改成功');
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
        if ($info['shop_uid']!=$this->user['uid']) {
            $this->error('你没权限');
        }
        $this->assign('info',$info);
        return $this->fetch();
    }
    
}