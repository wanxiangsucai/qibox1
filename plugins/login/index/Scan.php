<?php
namespace plugins\login\index;
use app\common\controller\IndexBase;
use app\common\model\User AS UserModel;
use plugins\login\model\Scan AS ScanModel;

class Scan extends IndexBase
{
    protected static $sid=null;
    
    protected function _initialize(){
        parent::_initialize();
        if(input('sid')){
            self::$sid = input('sid');
        }else{
            self::$sid = get_cookie('user_sid');
            if(empty(self::$sid )){
                self::$sid = rands(10);
                set_cookie('user_sid', self::$sid );
            }
        }
    }
    
    /**
     * 生成登录二维码给手机扫描
     * @param string $type 默认是微信,也可以是APP
     */
    public function qrcode($type='wx',$sid='',$backurl=''){
        $url = $this->request->domain() . purl('login/scan/in_app') . '?type=' . $type . '&code=' . mymd5(time() . "\t" . self::$sid ."\t" .get_ip() ).'&sid='.$sid.'&backurl='.urlencode($backurl);        
        if ($this->request->isAjax()) {
            return $this->ok_js([
                'url'=>$url,
            ]);
        }
        if ($type=='wx' && $this->webdb['wxapp_appid'] && $this->webdb['wxapp_appsecret'] && 
            ($this->webdb['weixin_type']!=3 || $this->webdb['scan_login_type']=='wxapp')
            ) {
            $url = fun('wxapp@wxapp_codeimg',$url,$this->user['uid'],'',600);
        }else{
            $url = iurl('index/qrcode/index') . '?url=' . urlencode($url);
        }        
        header('location:'.$url);
        exit;
    }
    
    /**
     * PC端刷新数据库手机端是否已登录成功
     * @param string $type
     * @return string
     */
    public function cklogin($type='',$sid=''){
        if($type=='success'){            
            $this->success('登录成功',iurl('index/index/index'));
        }
        if ($this->user) {
            die('ok');
        }
        $info = getArray( ScanModel::where('sid',self::$sid )->find() );
        if($info['uid']){
            if($info['ip']!=$this->request->ip()){
                //return 'error IP';
            }
            if(time() - $info['posttime']>300){
                die('overtime');
            }            
            $user = UserModel::login($info['uid'], '', 3600*24*30,true,'uid');
            ScanModel::where('sid',self::$sid)->delete();
            if ($sid) {
                $skey = md5( mymd5($user['uid'].$user['password'].date('z') ) );
                cache2(md5($skey),"{$user['uid']}\t{$user['username']}\t".mymd5($user['password'],'EN'),3600*72);
                $array = [
                    'token'=>$skey,
                    'userInfo'=>\app\common\fun\Member::format($user,$user['uid']),
                ];
                return $this->ok_js($array);
            }else{
                die('ok');
            }            
        }
        die('数据为空');
    }
    
    /**
     * 手机端访问执行登录操作
     * @param string $type 主要是指定如果没有登录的时候,以什么方式登录
     * @param string $code
     */
    public function in_app($type='wx',$code='',$sid='',$backurl='',$msg=''){
        if(empty($this->user)){
            if($type=='wx'){
                $url = iurl('weixin/login/index') . '?fromurl=' . urlencode($this->weburl);
                echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=".$url."'>";
                exit;
            }
        }
        if($code){
            $step = $this->request->post('step');
            if (!$step) {
                $this->assign('msg',filtrate($msg));
                $this->assign('step', mymd5(time()."\t".$this->user['uid']) );
                return $this->fetch();
            }
            list($time,$uid) = explode("\t",mymd5($step,'DE'));
            if($uid!=$this->user['uid']){
                $this->error("UID参数有误！");
            }elseif (time()-$time>30){
                $this->error("超时了，请重新扫码");
            }
//             if(!check_tncode()){
//                 $this->error('验证码错误或不存在！');
//             }
            list($time,$usrID,$ip) = explode("\t",mymd5($code,'DE'));
            if(!$usrID){
                $this->error("参数有误！");
            }elseif( (time()-$time)>600 ){
                $this->error("超时了，10分钟内有效，请再次刷新一下电脑页面再扫描！");
            }
            $usrID = filtrate($usrID);
            if($ip!=get_ip()){
                //$this->error('PC端的IP与手机端的IP不一致,请把手机连接到WIFI,再重新扫码!');
            }
            ScanModel::where('sid',$usrID)->delete();
            $data = [
                    'uid'=>$this->user['uid'],
                    'sid'=>$usrID,
                    'ip'=>get_ip(),
                    'posttime'=>time(),
            ];
            ScanModel::create($data);
            if($sid!=''&&strlen($sid)==32){
                $user = $this->user;
                cache2(md5($sid),"{$user['uid']}\t{$user['username']}\t".mymd5($user['password'],'EN')."\t",3600);
            }
            $this->success('请求方登录成功，你可以关闭本页面' , $backurl?:get_url('member'), [] , $backurl?1:60);            
        }
    }
}