<?php
namespace app\index\controller\wxapp;

use app\common\controller\IndexBase;
use app\index\model\Pay AS PayModel;

//小程序或公众号支付 生成JS参数
class Pay extends IndexBase
{
    /**
     * 小程序或者是公众号的支付,获取相应的支付JS参数,就可以实现自由定义任何支付页面,不使用统一的支付页面.
     * @param string $money 支付金额
     * @param string $numcode 要在订单号前面加多三个0, 因为在这里是小程序后台调用的,前台H5已经生成过一次订单了.
     * @param string $title 产品名称
     * @param string $other 其它参数
     * @param string $callback_class 支付成功后,回调函数执行类
     * @param string $type 公众号支付,还是小程序支付
     * @return unknown
     */
    public function index($money='0.01' , $numcode='' , $title='' , $other='',$callback_class='',$type='wxapp'){
        
        if(
                ($this->webdb['weixin_appid'] && $this->webdb['weixin_appsecret'] && $this->webdb['weixin_payid'] && $this->webdb['weixin_paykey'])
                ||
                ($this->webdb['wxapp_appid'] && $this->webdb['wxapp_appsecret'] && $this->webdb['wxapp_payid'] && $this->webdb['wxapp_paykey'])
                ){            
                    if(empty(get_wxappAppid()) && $this->user['weixin_api']=='' && $this->user['wxapp_api']=='' && $this->user['wxopen_api']==''){
                        $this->err_js('你的当前帐号还没有绑定微信，不能使用微信支付');
                    }
        }elseif($type!='rmb'){
            $this->error('系统没有设置好微信支付接口,所以不能使用微信支付');
        }
   
        $numcode || $numcode = 'w'.date('ymdHis').rands(3);
        if($type=='wxapp'){
            $openId = get_wxappAppid() ?  \app\qun\model\Weixin::get_openid_by_uid($this->user['uid']) : $this->user['wxapp_api'] ;
        }elseif($type=='wxopen'){
            $openId = $this->user['wxopen_api'];
        }elseif($type=='rmb'){
            if ($this->user['rmb']<$money) {
                return $this->err_js('你的余额不足!');
            }
            $callback_class = mymd5($callback_class,'DE');
            if ($callback_class){
                $this->run_callback($callback_class);
            }
            return $this->ok_js();
        }else{
            $openId = $this->user['weixin_api'];
        }
        
        $wxapp_appid = '';
        if (get_wxappAppid()) {
            //$_info = \app\qun\model\Wxset::get_info_by_appid();
            $_info = wxapp_cfg( get_wxappAppid() );
            if($_info['status']==2){
                $wxapp_appid = get_wxappAppid();
            }
        }
        
        $array = [
            'title'=>$title?$title:'帐号充值',
            'other'=>$other?$other:'test',
            'numcode'=>$type=='wxapp'?'000'.$numcode:$numcode,  //000为的是多生成一个不重复的订单给小程序支付,同时避免出现订单重复的现象,跟公众号那里有冲突
            'money'=>$money>0?$money:'0.01',
            'wx_notify_url'=>$this->request->domain().url('pay/index',[
                'banktype'=>'weixin',
                'action'=>'back_notice',
                'back_post'=>'wap',
                'client_type'=>$type,
                'qun_wxapp_appid'=>$wxapp_appid,
            ]),
            'openId'=>$openId,
        ];
        $result = PayModel::get([ 'numcode'=>$numcode, ]);
        if ( empty($result) ) {
            
            $data = [
                'numcode'=>$numcode,
                'money'=>$array['money'],
                'posttime'=>time(),
                'uid'=>intval($this->user['uid']),
                'banktype'=>$type,
                'wxapp_appid'=>$wxapp_appid,
                'callback_class'=>mymd5(urldecode($callback_class),'DE'),     //支付成功后，后台执行的类
            ];
            PayModel::create($data);
        }else{
            PayModel::where('numcode',$numcode)->update(['banktype'=>$type]);
            $array['money'] = $result['money'];
        }
        
        
        
        //小程序可以使用其它支付接口,可以跟公众号不一样的支付接口
        if($type=='wxapp'){
            config('webdb.weixin_appid', config('webdb.wxapp_appid') );    //小程序的appid要跟支付接口绑定
            config('webdb.weixin_payid', config('webdb.wxapp_payid') );
            config('webdb.weixin_paykey', config('webdb.wxapp_paykey') );
        }elseif($type=='wxopen'){   //APP默认用公众号的支付接口
            config('webdb.weixin_appid', config('webdb.wxopen_appid') );
            //config('webdb.weixin_payid', config('webdb.wxapp_payid') );
            //config('webdb.weixin_paykey', config('webdb.wxapp_paykey') );
        }
        
        if($type=='wxopen'){    //app不能用JsApi方式支付
            return include(ROOT_PATH.'plugins/weixin/api/wxopen.php');
        }else{
            return include(ROOT_PATH.'plugins/weixin/api/wxapp.php');
        }        
    }
    
    
    /**
     * 支付成功,异步执行各种功能模块的应用 格式是 mymd5("app\\shop\\model\\Order@pay@5")
     * @param unknown $code
     */
    protected function run_callback($code){
        if (empty($code)) {
            return ;
        }
        //'app-shop-model-Order@pay@order_id|5' //弃用这种格式了
        $detail = explode('@',$code);
        $class = str_replace('-', '\\', $detail[0]);    //兼容以前用-隔开目录名的情况
        $action = $detail[1];
        $ar = explode('|',$detail[2]);
        //$_params = [$ar[0]=>$ar[1]];
        $_params = $ar;
        if(class_exists($class)&&method_exists($class,$action)){
            $obj = new $class;
            call_user_func_array([$obj, $action], $_params);
        }
    }
}
