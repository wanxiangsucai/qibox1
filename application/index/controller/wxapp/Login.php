<?php
namespace app\index\controller\wxapp;

use plugins\login\model\Wxapp AS UserModel;
use app\common\controller\IndexBase;
use QCloud_WeApp_SDK\Auth\LoginService;
use QCloud_WeApp_SDK\Constants as Constants;
use QCloud_WeApp_SDK\Auth\AuthAPI;


//小程序 用户登录与退出
class Login extends IndexBase
{
    /**
     * webapp退出
     */
    public function getout($token=''){
        if ($token) {
            cache2(md5($token),null);
        }elseif ($this->request->header('token')) {
            cache2(md5($this->request->header('token')),null);
        }
        UserModel::quit($this->user['uid']);
        return $this->ok_js([],'退出成功');
    }
    
    /**
     * webapp登录
     */
    public function goin(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if(empty($data['cookietime'])){
                $data['cookietime'] = $this->webdb['login_time']?:3600*24*30;
            }
            $result = UserModel::login($data['username'],$data['password'],$data['cookietime']);
            if($result==0){
                return $this->err_js("当前用户不存在,请重新输入");
            }elseif($result==-1){
                return $this->err_js("密码不正确,点击重新输入");
            }elseif(is_array($result) && $result['uid']){
                $user = $result;
                $token = md5( mymd5($user['uid'] . $user['password']  . date('z')) );
                cache2(md5($token),"{$user['uid']}\t{$user['username']}\t".mymd5($user['password'],'EN')."\t",3600*240);
                $array = [
                    'uid'=>$result['uid'],
                    'userInfo'=>\app\common\fun\Member::format($result,$user['uid']),
                    'token'=>$token,
                ];
                return $this->ok_js($array,'登录成功');
            }else{
                return $this->err_js('未知错误!');
            }
        }else{
            return $this->err_js('提交方式有误!');
        }
    }
    
    /**
     * 网页端AJAX检查登录状态
     */
    public function web_login_check(){
        if ($this->user) {
            return $this->ok_js(['uid'=>$this->user['uid']],'已登录');
        }else{
            return $this->err_js('未登录');
        }
    }
    
    /**
     * 微信开放平台移动应用APP登录
     * @param string $code
     */
    public function wxopen($code=''){
        if (empty($code)) {
            return $this->err_js("code值不存在");
        }
        $string = file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->webdb['wxopen_appid'].'&secret='.$this->webdb['wxopen_appkey'].'&code='.$code.'&grant_type=authorization_code');
        $array = json_decode($string,true);
        if(empty($array['unionid'])){
            return $this->err_js("unionid获取失败");
        }elseif(empty($array['openid'])){
            return $this->err_js("openid获取失败");
        }
        
        $result = UserModel::where('unionid',$array['unionid'])->find();
        if(empty($result)){ //新用户,自动注册帐号
            $string2 = file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$array['access_token'].'&openid='.$array['openid'].'&lang=zh_CN');
            $data = json_decode($string2,true);
            if(empty($data['openid'])){
                return $this->err_js("用户资料获取失败");
            }
            $data['nickName'] = $data['nickname'];
            $data['avatarUrl'] = $data['headimgurl'];
            $user = UserModel::api_reg($array['openid'],$data);
            if(is_array($user) && $user['uid']>0){
                UserModel::edit_user([
                    'uid'=>$user['uid'],
                    'wxapp_api'=>'',
                    'wxopen_api'=>$array['openid'],
                    'sex'=>intval($data['sex']),
                ]);                
            }else{
                return $this->err_js('注册失败:'.$user);
            }
        }elseif(empty($result['wxopen_api'])){
            UserModel::edit_user([
                'uid'=>$result['uid'],
                'wxopen_api'=>$array['openid'],
            ]);            
        }
        $result = UserModel::login($array['unionid'],'',3600*24,true,'unionid');
        $user = $result;
        $token = md5( mymd5($user['uid'] . $user['password']  . date('z')) );
        cache2(md5($token),"{$user['uid']}\t{$user['username']}\t".mymd5($user['password'],'EN')."\t",1800);
        $array = [
            'uid'=>$result['uid'],
            'token'=>$token,
            'userInfo'=>\app\common\fun\Member::format($user,$user['uid']),
        ];
        return $this->ok_js($array,'登录成功');
    }
    
    /**
     * 小程序用户登录
     * @param string $code 微信端提交过来的
     * @param string $encryptedData 微信端提交过来的
     * @param string $iv 微信端提交过来的
     * @param string $uids 微信端提交过来的之前的框架webview登录标志
     * @param string $userinfo 微信新版登录接口getUserProfile 无法获取openid  ,所以要把用户的基本信息传过来
     * @return string
     */
    public function index($code='',$encryptedData='',$iv='',$uids='',$userinfo=[]){
        if($code=='the code is a mock one'||empty($code)){
            return $this->err_js('无法登录,code 获取失败');
        }
        
        if (get_wxappAppid() && wxapp_open_cfg(get_wxappAppid())) {
            $string = file_get_contents('https://api.weixin.qq.com/sns/component/jscode2session?appid='.get_wxappAppid().'&js_code='.$code.'&grant_type=authorization_code&component_appid='.config('webdb.P__wxopen')['open_appid'].'&component_access_token='.wx_getOpenAccessToken());
            $array = json_decode($string,true);
            if ($array['unionid'] || $array['openid']){
                $info = $userinfo;
                $openid = $info['openId'] = $array['openid'];
                $info['unionId'] = $array['unionid'];
                $skey = $sessionKey = $code;
            }else{
                return $this->err_js('登录失败,详情是：'.$string);
            }
        }else{
            if ($userinfo) {    //微信新版登录接口getUserProfile 无法获取openid
                $info = is_array($userinfo)?$userinfo:[];
                $string = file_get_contents('https://api.weixin.qq.com/sns/jscode2session?appid='.config('webdb.wxapp_appid').'&js_code='.$code.'&grant_type=authorization_code&secret='.config('webdb.wxapp_appsecret'));
                $_array = json_decode($string,true);
                if(!$_array['openid']){
                    return $this->err_js($_array);
                }
                $openid = $info['openId'] = $_array['openid'];
                $info['unionId'] = $_array['unionid'];
                $skey = $sessionKey = $code;
            }else{
                $array = AuthAPI::login($code, $encryptedData, $iv);
                if(!is_array($array)){
                    return $this->err_js($array);
                }
                /*
(
    [userinfo] => Array
        (
            [phoneNumber] => 18664780444 //用户绑定的手机号（国外手机号会有区号）
            [purePhoneNumber] => 18664780444 //没有区号的手机号
            [countryCode] => 86
            [watermark] => Array
                (
                    [timestamp] => 1620867919
                    [appid] => wxa53b9074eeee84f
                )

        )

    [skey] => 5dcc377c389875d3588a86403b8b188ede7376d3
    [sessionKey] => 6TNiTv4wszPVrMFQEbOZPQ==
)

                 */
                $skey = $array['skey'];
                $sessionKey = $array['sessionKey'];
                $info = $array['userinfo'];
                $openid = $info['openId'];
            }
        }        
        
        
        if (empty($openid)) {
            return $this->err_js('登录失败,openid获取不到');
        }
        
        $user = UserModel::check_wxappIdExists($openid);  //根据小程序ID获取用户信息,优先级最高
        
        if (get_wxappAppid()) {
            unset($info['unionId']);    //商家小程序不允许使用 unionId
        }
        if(!$user && modules_config('qun') && class_exists("app\\qun\\model\\Weixin")){
            $user = \app\qun\model\Weixin::get_info_by_openid($openid);   //用户在大平台登录时，看看是否在某个商家那里注册过，就避免重复注册
        }
 
        if ( $user && $info['unionId'] && empty($user['unionid']) ) {   //后来开通了认证微信开放平台的老用户处理
            UserModel::edit_user([
                'uid'=>$user['uid'],
                'unionid'=>$info['unionId'],
            ]);
        }
        
        if (empty($user) && $info['unionId']) {     //绑定了微信认证开放平台
            $user = UserModel::get(['unionid'=>$info['unionId']]);

            if ( $user && empty($user['wxapp_api']) ) { //开通了微信认证开放平台正常情况新用户都是这种
                UserModel::edit_user([
                    'uid'=>$user['uid'],
                    'wxapp_api'=>$openid,
                ]);
            }
        }
        
        
        if(empty($user) && $uids){  //有传递WEB框架用户已登录的标志过来 , 这个是针对没有绑定认证开放平台处理的,已认证的话,用不到这里
            list($uid,$time) = explode(',',mymd5($uids,'DE'));
            if (time()-$time<600) {
                $user = UserModel::getById($uid);
                if (get_wxappAppid()) {
                    \app\qun\model\Weixin::add($uid,$openid);   //首次绑定某个商家的小程序
                }else{
                    if (empty($user['wxapp_api'])) {
                        UserModel::edit_user([
                            'uid'=>$uid,
                            'wxapp_api'=>$openid,
                        ]);
                    }
                }                
            }
        }
        if(empty($user)){
            if($info['unionId']){
                $info['unionid']=$info['unionId'];  //注意I是大写
            }
            $user = UserModel::api_reg($openid,$info);
            if(!is_array($user)||$user['uid']<1){
                return $this->err_js('注册失败:'.$user);
            }
            if (get_wxappAppid()) {
                \app\qun\model\Weixin::add($uid,$openid);   //首次绑定某个商家的小程序
                UserModel::where('uid',$uid)->update([
                    'wxapp_api'=>''  //因为并不是平台的小程序ID，所以要做处理
                ]);
            }
        }
        
        UserModel::login($user['username'], '', '',true);   //这个并不能真正的登录.只是做一些登录的操作日志及其它接口处理
        
        $user = UserModel::get_info($user['uid']);  //这句可以删除,主要是考虑到以前password没有统一在一个数据表的情况
        $skey = md5( mymd5($user['uid'].$user['password'].date('z') ) );
        cache2(md5($skey),"{$user['uid']}\t{$user['username']}\t".mymd5($user['password'],'EN')."\t$sessionKey"."\t{$openid}",3600*72);
        $array = [
            'token'=>$skey,
            'userInfo'=>\app\common\fun\Member::format($user,$user['uid']),
        ];
        return $this->ok_js($array);
    }
    
    /**
     * 通过code获取用户登录信息
     * @param string $code
     * @param string $uids 统一登录标志
     * @param number $qid
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function wxapp_getuser_bycode($code='',$uids='',$qid=0){
        if (empty($qid)) {
            $webdb = config('webdb.P__xcx_qun');
            $cfg = [
                'cover_image'=>$webdb['cover_image']?tempdir($webdb['cover_image']):'',
                'wait_time'=>$webdb['wait_time'],
                'allow_click_jump'=>$webdb['allow_click_jump'],
                'click_jump_url'=>$webdb['click_jump_url'],
            ];
        }else{
            $cfg = [
                'cover_image'=>'',
                'wait_time'=>2,
                'allow_click_jump'=>0,
                'click_jump_url'=>'',
            ];
        }
        
        if (!$code) {
            return $this->err_js('CODE为空',$cfg);
        }
        if (get_wxappAppid() && wxapp_open_cfg(get_wxappAppid())) {
            $string = file_get_contents('https://api.weixin.qq.com/sns/component/jscode2session?appid='.get_wxappAppid().'&js_code='.$code.'&grant_type=authorization_code&component_appid='.config('webdb.P__wxopen')['open_appid'].'&component_access_token='.wx_getOpenAccessToken());
        }else{
            $string = file_get_contents('https://api.weixin.qq.com/sns/jscode2session?appid='.$this->webdb['wxapp_appid'].'&secret='.$this->webdb['wxapp_appsecret'].'&js_code='.$code.'&grant_type=authorization_code');
        }        
        $array = json_decode($string,true);
        if ($array['unionid'] || $array['openid']){
            $user = get_user($array['openid'],'wxapp_api');
            if ($user) {
                if ($array['unionid'] && empty($user['unionid']) && empty(get_wxappAppid())) {   //后来开通的微信开放平台
                    UserModel::edit_user([
                        'uid'=>$user['uid'],
                        'unionid'=>$array['unionid'],
                    ]);
                }
            }else{
                
                //if(empty(get_wxappAppid()) && $array['unionid']){
                if($array['unionid']){
                    $user = get_user($array['unionid'],'unionid');
                }
                if (!$user && $uids) {
                    list($uid,$time) = explode(',',mymd5($uids,'DE'));
                    if (time()-$time<600) {
                        $user = UserModel::getById($uid);
                    }
                }
                
                if ($user) {
                    if (get_wxappAppid()) {
                        \app\qun\model\Weixin::add($user['uid'],$array['openid']);   //首次绑定某个商家的小程序
                        cache('user_'.$user['uid'],null);
                    }else{
                        UserModel::edit_user([
                            'uid'=>$user['uid'],
                            'wxapp_api'=>$array['openid'],
                        ]);
                    }
                }
            }
            if ($user) {
                $code = md5( mymd5($user['uid'].$user['password'].date('z')) );
                cache2(md5($code),"{$user['uid']}\t{$user['username']}\t".mymd5($user['password'],'EN')."\t\t{$array['openid']}",3600);
                $array = [
                    'token'=>$code,
                    'userInfo'=>\app\common\fun\Member::format($user,$user['uid']),
                ];
                return $this->ok_js(array_merge($array,$cfg));
            }else{
                $cfg['openid'] = mymd5(time()."\t".$array['openid']);
                return $this->err_js('用户不存在！',$cfg);
            }            
        }else{
            return $this->err_js($string.'appid:'.get_wxappAppid(),$cfg);
        }
    }
    
    /**
     * 借助H5实现小程序登录
     * @param string $sid
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function login_from_h5($sid=''){
        if(!$sid){
            return $this->err_js('sid参数不存在');
        }
        $str = cache($sid);
        if(!$str){
            return $this->err_js('资料不存在');
        }
        list($ip,$uid,$sockpuppet) = explode("\t", $str);
        
        if ($ip!=get_ip()) {
            return $this->err_js('登录IP不一致！');
        }elseif(!$uid){
            return $this->err_js('游客状态！');
        }
        
        if($sockpuppet){
            list($time,$suid) = explode("\t", mymd5($sockpuppet,'DE'));
            if($suid){
                $uid = $suid;
            }
        }
        
        $user = get_user($uid);
        $code = md5( mymd5($user['uid'].$user['password'].date('z')) );
        cache2(md5($code),"{$user['uid']}\t{$user['username']}\t".mymd5($user['password'],'EN'),3600);
        $array = [
            'uid'=>$uid,
            'token'=>$code,
        ];
        return $this->ok_js($array);
    }
    
    
    /**
     * 小程序或APP客户端检查登录状态
     * @param string $token
     * @return \think\response\Json
     */
    public function check($token=''){    
        list($uid,$username,$pwd,,$wxapp_id) = explode("\t", cache2(md5($token)));
        $code = 1;
        $userInfo = [];
        $msg = '用户并没有处于登录状态';
        
        if ($uid && $username) {
            if ( get_wxappAppid() ) {
                $userInfo = get_user($wxapp_id,'wxapp_api');
            }else{
                $userInfo = UserModel::get_info($uid);
            }            
        }
        
        if($userInfo){
            $code = 0;            
            unset($userInfo['password_rand'],$userInfo['qq_api'],$userInfo['weixin_api'],$userInfo['wxapp_api']);
            $msg = '用户已处于登录状态';
            
        }
        $data = [
                'meta'=>[
                        'code'=>$code,
                        'message'=>$msg,
                ],
                'data'=>[
                        'userInfo'=>$userInfo,
                ],
        ];
        return json($data);
    }    
    
    /**
     * 小程序或APP退出登录 ,还需进一步完善
     * @param string $code
     * @return string
     */
    public function quit($code=''){
        $string = '{"meta":{"code":0,"message":"登出成功"},"data":null}';
        return $string;
    }
}
