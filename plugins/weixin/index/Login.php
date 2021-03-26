<?php
namespace plugins\weixin\index;
use app\common\controller\IndexBase;
use app\common\model\User AS UserModel;



class Login extends IndexBase
{
    /**
     * 微信登录
     * @param string $fromurl 返回的地址
     * @param string $type 设置为bind的时候,就是绑定帐号
     * @param string $sid 设置为bind的时候的用户字串
     */
    public function index($fromurl='',$type='',$sid=''){        
        if ($fromurl && preg_match("/^http/i", $fromurl) && get_site_key($fromurl)) {   //子站点请求的登录
        }else{
            if($type=='bind'){
                if(cache('bind_'.$sid)==''){
                    $this->error('信息有误!');
                }
            }elseif($this->user && !defined('LABEL_SET') ){
                $this->error('你已经登录了',$fromurl?:get_url('member'),'',1);
            }elseif(!in_weixin()){
                $this->assign('fromurl',urlencode($fromurl));
                return $this->fetch();
            }elseif (in_wxapp() && $this->webdb['weixin_type']!=3) {  //没有认证服务号的话，就选择小程序登录
                $fromurl = $fromurl ? get_url($fromurl) : $this->request->domain();
                $url = '/pages/login/index?url='.urlencode(
                    '/pages/wap/iframe/index?url='.urlencode(
                        $fromurl .(strstr($fromurl,'?')?'&':'?').'token='
                        )
                    );
                $this->assign('url',$url);
                return $this->fetch('wxapp');
            }
        }        
        
        $state = input('state');
        $code = input('code');
        $kcode = input('kcode');
        
        if($state==1||$kcode){
            
            $fromurl = urldecode(get_cookie('From_url'));
            
            if ($kcode && $this->webdb['wxlogin_url'] && $this->webdb['wxlogin_key']) { //请求了第三方站点实现微信登录
                list($time,$openid,$unionid) = explode("\t", mymd5($kcode,'DE',$this->webdb['wxlogin_key']));
                if(!$time||!$openid){
                    $this->error('资料有误!');
                }elseif (time()-$time>600) {
                    $this->error('登录超时了!');
                }
            }else{
                if(!$code){
                    $this->error('code 值不存在！');
                }
                $string = file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.config('webdb.weixin_appid').'&secret='.config('webdb.weixin_appsecret').'&code='.$code.'&grant_type=authorization_code');
                $array = json_decode($string,true);
                $openid = $array['openid'];
                $unionid = $array['unionid'];
                if(!$openid){
                    if($string == ''){
                        $this->error('获取微信接口内容失败，请确认你的服务器已打开 extension=php_openssl.dll ');
                    }
                    $this->error('openid 值不存在！错误详情如下：'.$string);
                }
                if ($fromurl && preg_match("/^http/i", $fromurl) && get_site_key($fromurl)) {   //子站点请求的登录
                    $str = mymd5(time()."\t".$openid."\t".$unionid,'EN',get_site_key($fromurl));
                    header('location:'.$fromurl.(strstr($fromurl,'?')?'&':'?').'kcode='.$str);
                    exit;
                }
            }
            
            
            $rs = UserModel::get_info(['weixin_api'=>$openid]);
            if (empty($rs) && $unionid) {
                $rs = UserModel::get_info(['unionid'=>$unionid]);
            }
            
            if($type=='bind'){  //绑定帐号
                
                $uid = cache('bind_'.$sid);                
                if($uid && get_user($uid)){
                    
                    if($rs){    //解绑以前的帐号
                        $array = [
                                'uid'=>$rs['uid'],
                                'weixin_api'=>'',
                        ];
                        UserModel::edit_user($array);
                    }
                    
                    $array = [
                            'uid'=>$uid,
                            'weixin_api'=>$openid,
                    ];
                    UserModel::edit_user($array);
                    cache('user_'.$uid,null);
                    $bind_ok = true;
                }
                
            }else{      //注册与登录
                
                //$rs && $ps = UserModel::get_info($rs['uid'],'uid');
                
                if(empty($rs)){
                    $this->success( '你还没有注册，现在自动注册一个帐号!' , purl('reg/index',['openid'=>$openid]) );
                }elseif( empty($rs['username']) ){
                    UserModel::edit_user([
                            'uid'=>$rs['uid'],
                            'username'=>'xx-'.$rs['uid'],
                    ]);
                }elseif( $unionid && empty($rs['unionid']) ){
                    UserModel::edit_user([
                        'uid'=>$rs['uid'],
                        'unionid'=>$unionid,
                    ]);
                }elseif( $openid && empty($rs['weixin_api']) ){
                    UserModel::edit_user([
                        'uid'=>$rs['uid'],
                        'weixin_api'=>$openid,
                    ]);
                }
                
                UserModel::login($rs['username'], '', 3600*24*30,true);
            }

            set_cookie('WeiXin_AccessToken',$array['access_token'],7200); //可用于后续 获取共享收货地址

            if( $fromurl ){
                set_cookie('From_url',null);
                $jumpto = $fromurl;
            }else{
                $jumpto = get_url('member');
            }

            if($type=='bind'){
                $this->success($bind_ok?'绑定成功':'绑定失败',$jumpto,[],3);
            }else{
                header("location:$jumpto");
                exit;
            }
        }else{
            set_cookie('From_url',input('get.fromurl')?:null);
            $url = urlencode($this->weburl);
            
            if($this->webdb['wxlogin_url'] && $this->webdb['wxlogin_key']){    //跳转到第三方站点实现微信登录
                header('location:'.$this->webdb['wxlogin_url'].purl('login/index').'?fromurl='.$url);
                exit;
            }
            
            if($this->webdb['wxopen_appid'] && $this->webdb['wxopen_appkey']){  //配置了微信认证开放平台统一帐号
                $type = "snsapi_userinfo";
            }else{
                $type = "snsapi_base";  //这种方式得不到unionid
            }
            header('location:https://open.weixin.qq.com/connect/oauth2/authorize?appid='.config('webdb.weixin_appid').'&redirect_uri='.$url.'&response_type=code&scope='.$type.'&state=1#wechat_redirect');
            exit;
        }
    }
}