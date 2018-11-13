<?php
namespace app\index\pay;
use app\index\controller\Pay;
use app\index\model\Pay AS PayModel;

class Weixin extends Pay{
    
    public function checkpay(){
        $numcode = input('numcode');
        $info = getArray(PayModel::get(['numcode'=>$numcode]));
        if($info['ifpay']==1){
            return $this->ok_js([],'支付成功');
        }elseif(!$info){
            return $this->err_js('订单不存在');
        }else{
            return $this->err_js('订单未支付'); 
        }
    }
    
    protected function weixin_pay_inpc($array){
        //print_r($array) ;exit;
        $data=[
                'money'=>$array['money'],
                'return_url'=>$this->request->domain(),
                'banktype'=>'weixin',
                'numcode'=>$array['numcode'],
        ];
        $url = post_olpay($data, false);
        $url = get_url($url);
        $qrcode = get_qrcode($url);
        $this->assign('qrcode',$qrcode);
        $this->assign('numcode',$array['numcode']);
        $this->assign('return_url',input('return_url'));
        return $this->fetch('weixin_pay_inpc');
    }
    
    //跳到付款页面,准备付款
    public function gotopay(){
        if(
                ($this->webdb['weixin_appid'] && $this->webdb['weixin_appsecret'] && $this->webdb['weixin_payid'] && $this->webdb['weixin_paykey'])
                ||
                ($this->webdb['wxapp_appid'] && $this->webdb['wxapp_appsecret'] && $this->webdb['wxapp_payid'] && $this->webdb['wxapp_paykey'])
        ){
            if(!$this->user){
                if( in_weixin() ){
                    weixin_login($url='');
                }else{
                    $this->error('请先登录!');
                }
            }else{
                //可以进一步改进,强制微信登录,绑定微信
                //$this->error('你的当前帐号还没有绑定微信，不能使用微信支付');
            }
        }else{
            $this->error('系统没有设置好微信支付接口,所以不能使用微信支付');
        }
        
        if(!in_weixin()){
            $array = $this->olpay_send();
            //$this->error('当前页面只能用手机微信访问！');
            return $this->weixin_pay_inpc($array);
        }else{
            if (input('client_type')=='') {
                return $this->fetch('choose_mp_wxapp');
            }
            $array = $this->olpay_send();
            
            if (input('client_type')=='wxapp') {
                $this->assign('array',$array);
                return $this->fetch('wxapp_pay');
            }else{                
                include(ROOT_PATH.'plugins/weixin/api/jsapi.php');
            }
        }
    }
    
    
    protected function send_config(){
        
        //微信接口，结尾加参数无效
        $array['wx_notify_url'] = $this->request->domain().url('pay/index',['banktype'=>'weixin','action'=>'back_notice','back_post'=>'wap']);
        $array['wx_return_url'] = $this->request->domain().url('pay/index',['banktype'=>'weixin','action'=>'pay_end_return']);
        $array['bankname'] = '微信支付';
        
        return $array;
    }
    
    //付款完毕，跳转回来时执行的动作，用户看得到的操作界面
    public function pay_end_return(){
        $ispay = input('ispay');
        $numcode = input('numcode');
        
        if($ispay=='ok'){
            $result = $this->have_pay($numcode,false);  //这里不能做操作，仅做检查,因为这个页面用户可以伪造
            if($result==1){
                $this->success('支付成功！订单已生效',$this->return_url); 
            }elseif($result==-1){
                $this->success('订单丢失，请联系管理员，请截图保留该订单号'.$numcode,$this->return_url);
            }elseif($result===0){
                $this->success('订单还在处理中，请稍候！',$this->return_url);
            }           
        }else{
            $this->success('你并没有付款，订单不生效！',$this->return_url);
        }
    }
    
    //付款成功后，微信后台通知，前台看不到的界面，只能用日志追踪
    public function back_notice(){
        global $pay_end_data;
        $pay_end_data = '';
        if (input('client_type')=='wxapp') {    //小程序支付的情况
            config('webdb.weixin_appid', config('webdb.wxapp_appid') );    //小程序的appid要跟支付接口绑定
            config('webdb.weixin_payid', config('webdb.wxapp_payid') );
            config('webdb.weixin_paykey', config('webdb.wxapp_paykey') );
        }
        include(ROOT_PATH.'plugins/weixin/api/notify.php');
        
        if($pay_end_data['out_trade_no']){  //支付成功，才能得到这个订单号
            //$pay_end_data['attach']
            $result = $this->have_pay(str_replace('000','',$pay_end_data['out_trade_no']));   //000避免出现订单重复的现象,跟公众号那里有冲突
            if($result==-1){
                return '订单不存在';
            }elseif($result==1){
                return '已经支付过了';
            }elseif($result=='ok'){
                return 'ok';    //支付成功，这里所有的动作，前台都是不可见，只能单独写日志追踪
            }
        }
        return 'fail';
    }
    
}