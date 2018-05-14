<?php
namespace app\member\controller;

use app\common\model\User AS UserModel;
use app\common\controller\MemberBase;
use app\common\util\Menu;
use think\Controller;

class Pcindex extends MemberBase
{
    public function index()
    {
        //$class = "plugins\\{$name}\\".ENTRANCE."\\".format_class_name($controller);
        //$obj = new $class;
        $obj = new Index();$action='index';
        return call_user_func_array([$obj, $action], []);EXIT;
        
        die('这是PC页');
        $this->assign('info',$this->user);
        $this->assign('menu',Menu::make('member'));//print_r(Menu::make('member'));exit;
		return $this->fetch();
    }
    
    public function edituser()
    {
        $this->assign('info',$this->user);
        return $this->fetch();
    }
    
    public function quit()
    {
        if (empty($this->user)) {
            $this->error('你还没登录！','index');
        }
        UserModel::quit($this->user['uid']);
        $this->success('成功退出','index');
    }
    
    public function login()
    {
        if (!empty($this->user)) {
            $this->error('你已经登录了','index');
        }
        if(IS_POST){
            $data= get_post('post');
            $result = UserModel::login($data['username'],$data['password'],$data['cookietime']);
            if($result==0){
                $this->error("当前用户不存在,请重新输入");
            }elseif($result==-1){
                $this->error("密码不正确,点击重新输入");
            }else{
                $this->success('登录成功','index');
            }
        }
		return $this->fetch();
    }
	
    public function reg()
    {
		return $this->fetch();
    }
}
