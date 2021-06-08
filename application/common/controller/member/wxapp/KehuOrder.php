<?php
namespace app\common\controller\member\wxapp;

use app\common\controller\MemberBase;


abstract class KehuOrder extends MemberBase
{
    protected $model;
    protected function _initialize(){
        parent::_initialize();        
        preg_match_all('/([_a-z0-9]+)/i',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model        = get_model_class($dirname,'order');
    }
    
    /**
     * 发货
     * @param number $id
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function shipping($id=0){
        $info = $this->model->getInfo($id);
        if ($info['shop_uid']!=$this->user['uid']) {
            return $this->err_js('你没权限');
        }
        $data = $this->request->post();
        $array['shipping_code'] = $data['shipping_code'];
        $array['shipping_status'] = 1;  //标志已发货
        $array['shipping_time'] = time();
        $this->model->where('id',$id)->update($array);
        
        $content = "你购买的商品,已经发货了,请注意查收,<a href=\"".get_url(murl('order/show','id='.$id))."\">点击详情查看单号或序列号</a>";
        send_msg($info['uid'],'你购买的商品已发货了,注意查收',$content);
        send_wx_msg($info['uid'],$content);
        
        return $this->ok_js();
    }
}