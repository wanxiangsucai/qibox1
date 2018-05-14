<?php
namespace app\index\controller;

use app\common\model\User AS UserModel;
use app\common\controller\IndexBase;
use QCloud_WeApp_SDK\Auth\LoginService;
use QCloud_WeApp_SDK\Constants as Constants;

class Login extends IndexBase
{
    //小程序检查是否已登录过
    public function wxapp_check(){
        $result = LoginService::check();        
        if ($result['loginState'] === Constants::S_AUTH) {
            return json([
                    'code' => 0,
                    'data' => $result['userinfo']
            ]);
        } else {
            return json([
                    'code' => -1,
                    'data' => []
            ]);
        }
    }
    
    //小程序登录
    public function wxapp_login(){
        $result = LoginService::login();        
        if ($result['loginState'] === Constants::S_AUTH) {
            return json([
                    'code' => 0,
                    'data' => $result['userinfo']
            ]);
        } else {
            return json([
                    'code' => -1,
                    'error' => $result['error']
            ]);
        }
    }
    
    public function quit()
    {
        if (empty($this->user)) {
            $this->error('你还没登录！','index/login/index');
        }
        UserModel::quit($this->user['uid']);
        $this->success('成功退出','index/index/index');
    }
    

    /**
     * 登录
     * @param string $fromurl 返回地址
     * @param string $type 等于iframe 是框架登录
     * @return mixed|string
     */
    public function index($fromurl='',$type='')
    {
        if (!empty($this->user)) {
            if($type=='iframe'){
                return $this->fetch('ok');
            }
            $this->error('你已经登录了','index/index');
        }
        if(IS_POST){
            $data= get_post('post');
            $result = UserModel::login($data['username'],$data['password'],$data['cookietime']);
            if($result==0){
                $this->error("当前用户不存在,请重新输入");
            }elseif($result==-1){
                $this->error("密码不正确,点击重新输入");
            }else{
                if($type=='iframe'){
                    return $this->fetch('ok');
                }
                $jump = $fromurl ? urldecode($fromurl) : iurl('index/index/index');
                $this->success('登录成功',$jump);
            }
        }
        if($type=='iframe'){
            $fromurl = url('index').'?type='.$type;
        }
        $this->assign('fromurl',urlencode($fromurl));
        $this->assign('type',$type);
        return $this->fetch($type=='iframe'?'iframe':'index');
    }
}
