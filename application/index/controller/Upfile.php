<?php
namespace app\index\controller;

use app\common\controller\IndexBase;
use think\Controller;
//调试用
class Upfile extends IndexBase
{
    
    public function index($fn='upfile')
    {
        if(IS_POST){
            $obj = new Attachment();
            $o = $obj->upload('pop');
            $info = $o->getData();
            $this->assign('info',$info);
            $this->assign('fn',$fn);
        }
		return $this->fetch();
    }
}
