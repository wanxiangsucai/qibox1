<?php
namespace app\common\controller\member;

use app\common\controller\MemberBase;
use think\Db;

abstract class KehuOrder extends MemberBase
{
    protected $model;
    protected $content_model;
    protected function _initialize(){
        parent::_initialize();        
        preg_match_all('/([_a-z0-9]+)/i',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model                = get_model_class($dirname,'order');
        $this->content_model        = get_model_class($dirname,'content');
    }
    
    /**
     * 导出订单
     * @param array $array
     */
    protected function excel($array=[],$shopid=0){        
    }

    /**
     * 查看我的客户订单列表
     * @param unknown $type
     * @return mixed|string
     */
    public function index($type=null,$aid=0,$excel=0){
        $shop_uid = 0;
        $map = [
                'shop_uid'=>$this->user['uid'],                
        ];
        if ($aid) {
            $map['shopid'] = $aid;
        }
        
        if($type=='ispay'){
            $map['pay_status'] = 1;
        }elseif($type=='nopay'){
            $map['pay_status'] = 0;
        }
        $shopdb = $this->content_model->getIndexByUid($shop_uid?:$this->user['uid']);
        
        $list_data = $this->model->getList($map,10,$excel);
        if ($excel==1) {
            $this->excel($list_data,$aid);
        }
        
        $this->assign([
            'listdb'=>getArray($list_data)['data'],
            'pages'=>$list_data->render(),
            'type'=>$type,
            'shopdb'=>$shopdb,
        ]);
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
     * 检查圈子会员组的权限
     * @param number $shopid
     * @return void|boolean
     */
    protected function check_qun_power($shopid=0){
        $info = $this->content_model->getInfoByid($shopid);
        $qid = $info['ext_id'];
        if (!modules_config('qun') || !$qid || !$this->webdb['is_qun_manage']) {
            return ;
        }
        if ($this->user['qun_group'][$qid]['type']==3) {
            return true;    //圈主默认有权限
        }
        $groups = Db::name('qun_power')->where([
            'qid'=>$qid,
            'sysname'=>config('system_dirname'),
            'type'=>'order',
        ])->value('groups');
        if($groups && in_array($this->user['qun_group'][$qid]['type'], str_array($groups))){
            return true;
        }
        return ;
    }
    
    /**
     * 权限判断
     * @param array $info
     * @return string
     */
    protected function check_power($info = []){
        if ($info['shop_uid']!=$this->user['uid'] && $this->check_qun_power($info['shopid'])!==true) {
            return '你没权限';
        }        
        return true;
    }
    
    /**
     * 订单详情
     * @param unknown $id
     * @return mixed|string
     */
    public function show($id){
        $info = $this->model->getInfo($id);
        $result = $this->check_power($info);
        if ($result!==true) {
            $this->error($result);
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
            $form_items = \app\common\field\Form::get_all_field(-1,$info);      //后台自定义字段
            $info = fun('field@format',$info,'','show','',$form_items);         //数据转义
        }
        return $info;
    }
    
}