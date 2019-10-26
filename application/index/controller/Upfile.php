<?php
namespace app\index\controller;

use app\common\controller\IndexBase;
use think\Controller;
//上传功能
class Upfile extends IndexBase
{
    /**
     * 框架或者是点击弹窗的上传功能
     * @param string $fn 回调函数
     * @param string $par 回调函数中用到的参数
     * @return mixed|string
     */
    public function index($fn='upfile',$par='')
    {
        if (empty($this->user)) {
            $this->error("你还没登录");
        }
        if(IS_POST){
            $obj = new Attachment();
            $o = $obj->upload('pop');
            $info = $o->getData();
            if (preg_match('/^\/public\//', $info['path'])) {
                empty($info['url']) && $info['url'] = $info['path'];
                $info['path'] = str_replace('/public/', '', $info['path']);
            }
            $this->assign('info',$info);
            $this->assign('fn',$fn);
            $this->assign('par',$par);
        }
		return $this->fetch();
    }
    
    public function images($img=''){
        $this->assign('img',$img);
        return $this->fetch();
    }
}
