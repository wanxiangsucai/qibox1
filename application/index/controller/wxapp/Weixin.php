<?php
namespace app\index\controller\wxapp;


use app\common\controller\IndexBase;


class Weixin extends IndexBase{
    /**
     * 获取微信JSDK配置参数
     * @param string $url
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function getconfig($url=''){
        if(config('webdb.weixin_type')<2 || config('webdb.weixin_appid')=='' || config('webdb.weixin_appsecret')==''){
            return $this->err_js('没有配置认证服务号');
        }
        if(!in_weixin()){
            return $this->err_js('不在微信中');
        }
        $jssdk = new \app\common\util\Weixin_share(config('webdb.weixin_appid'),config('webdb.weixin_appsecret'));
        $array = $jssdk->GetSignPackage($url);
        unset($array["rawString"]);
        return $this->ok_js($array);
    }
    
    /**
     * 同步更新 access_token
     * @param string $code 最新的access_token，为避免被恶意提交,已加密
     */
    public function sys_token($code=''){
        if (!$code) {
            return ;
        }
        //$type access_token类型,为空的话就是通用的access_token,设置为jsdk的话,就是jsdk要用到的token
        list($time,$type,$access_token) = explode("\t", mymd5($code,'DE',$this->webdb['wxmp_share_key']));
        if (!$access_token) {
            return ;
        }
        if ($type=='jsdk') {
            cache('weixin_jsdk_ticket'.substr(wx_getAccessToken(),0,5), $access_token, 1800);
        }else{
            cache('weixin_access_token',$access_token,1800);
        }
        return 'ok';
    }
}
