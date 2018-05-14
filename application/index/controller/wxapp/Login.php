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
     * 小程序用户登录
     * @param string $code 微信端提交过来的
     * @param string $encryptedData 微信端提交过来的
     * @param string $iv 微信端提交过来的
     * @return string
     */
    public function index($code='',$encryptedData='',$iv=''){
        if($code=='the code is a mock one'||empty($code)){
            $data = [
                    'meta'=>[
                            'code'=>1,
                            'message'=>'无法登录,code 获取失败',
                    ],
                    'data'=>[
                            'code'=>$code,
                            'token'=>'',                            
                    ],
            ];
            return json($data);
        }
        $array = AuthAPI::login($code, $encryptedData, $iv);
        $skey = $array['skey'];
        $sessionKey = $array['sessionKey'];
        $info = $array['userinfo'];
        $openid = $info['openId'];
        
        if (empty($openid)) {
            $data = [
                    'meta'=>[
                            'code'=>1,
                            'message'=>'登录失败,openid获取不到',
                    ],
                    'data'=>[
                            'token'=>''
                    ],
            ];
            return json($data);
        }
        $user = UserModel::check_wxappIdExists($openid);
        if(empty($user)){
            $user = UserModel::api_reg($openid,$info);
            if(empty($user['uid'])){
                $data = [
                        'meta'=>[
                                'code'=>1,
                                'message'=>'注册失败',
                        ],
                        'data'=>[
                                'msg'=>$user
                        ],
                ];
                return json($data);
            }
        }        
        UserModel::login($user['username'], '', '',true);   //这个并不能真正的登录.只是做一些登录的操作日志及其它接口处理
        
        $user = UserModel::get_passport($user['uid']);
        cache($skey,"{$user['uid']}\t{$user['username']}\t".mymd5($user['password'],'EN')."\t$sessionKey",3600*72);
        $data = [
                'meta'=>[
                        'code'=>0,
                        'message'=>'登录成功',
                ],
                'data'=>[
                        'token'=>$skey,
                        'userInfo'=>UserModel::get_allInfo($user['uid']),
                ],
        ];
        return json($data);
    }
    
    /**
     * 小程序或APP客户端检查登录状态
     * @param string $token
     * @return \think\response\Json
     */
    public function check($token=''){    
        list($uid,$username) = explode("\t", cache($token));
        $code = 1;
        $userInfo = [];
        $msg = '调用失败';
        if($uid&&$username){
            $code = 0;
            $userInfo = UserModel::get_allInfo($uid);
            $msg = '调用成功';
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
