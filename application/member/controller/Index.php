<?php
namespace app\member\controller;

use app\common\model\User AS UserModel;
use app\common\controller\MemberBase;
use app\common\util\Menu;
use think\Controller;

class Index extends MemberBase
{
    public function index()
    {
        $this->assign('info',$this->user);
        $this->assign('user',$this->user);
        $this->assign('menu',Menu::make('member'));
		return $this->fetch();
    }
    
    public function edituser()
    {
        $this->assign('info',$this->user);
        return $this->fetch();
    }
 
    public function map()
    {
        $this->assign('user',$this->user);
        return $this->fetch();
    }

}
