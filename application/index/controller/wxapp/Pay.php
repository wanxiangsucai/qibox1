<?php
namespace app\index\controller\wxapp;

use app\common\controller\IndexBase;
use app\index\model\Pay AS PayModel;

//小程序 用户登录与退出
class Pay extends IndexBase
{
    public function index($money='0.01' , $numcode='' , $title='' , $other='',$callback_class='',$type='wxapp'){
        
        if(!$this->webdb['weixin_appid'] || !$this->webdb['weixin_appsecret'] || !$this->webdb['weixin_payid'] || !$this->webdb['weixin_paykey']){
            $this->err_js('系统没有设置好微信支付接口,所以不能使用微信支付');
        }elseif($this->user['weixin_api']=='' && $this->user['wxapp_api']==''){
            $this->err_js('你的当前帐号还没有绑定微信，不能使用微信支付');
        }elseif(!in_weixin()){
            //$this->err_js('当前页面只能在小程序中访问！');
        }
        $numcode || $numcode = rands(10);
        $openId = $type=='wxapp' ? $this->user['wxapp_api'] :  $this->user['weixin_api'] ;
        $array = [
                'title'=>$title?$title:'购买服务',
                'other'=>$other?$other:'test',
                'numcode'=>'000'.$numcode,  //000避免出现订单重复的现象,跟公众号那里有冲突
                'money'=>$money?$money:'0.01',
                'wx_notify_url'=>$this->request->domain().url('pay/index',['banktype'=>'weixin','action'=>'back_notice','back_post'=>'wap']),
                'openId'=>$openId,
        ];
        
        if ( empty( PayModel::get([ 'numcode'=>$numcode, ]) )) {
            $data = [
                    'numcode'=>$numcode,
                    'money'=>$array['money'],
                    'posttime'=>time(),
                    'uid'=>intval($this->user['uid']),
                    'banktype'=>'weixin_app',
                    'callback_class'=>mymd5(urldecode($callback_class),'DE'),     //支付成功后，后台执行的类
            ];
            
            PayModel::create($data);
        }else{
            PayModel::where('numcode',$numcode)->update(['banktype'=>'weixin_app']);
        }
        
        
        
        //小程序可以使用其它支付接口,可以跟公众号不一样的支付接口
        if($type=='wxapp'){
            config('webdb.weixin_appid', config('webdb.wxapp_appid') );    //小程序的appid要跟支付接口绑定
            config('webdb.weixin_payid', config('webdb.wxapp_payid') );
            config('webdb.weixin_paykey', config('webdb.wxapp_paykey') );
        }        
        
        return include(ROOT_PATH.'plugins/weixin/api/wxapp.php');
    }
}
