<?php
namespace app\member\controller;

use app\common\controller\MemberBase;


class Bindlogin extends MemberBase
{

    /**
     * 绑定QQ登录
     * @return mixed|string
     */
    public function qq()
    {
        return $this->fetch();
    }
  
    /**
     * 绑定微信登录
     * @return mixed|string
     */
    public function weixin($type='')
    {
        cache('bind_'.get_cookie('user_sid'),$this->user['uid'],600);
        $url = purl('weixin/login/index',[],'index').'?type=bind&sid='.get_cookie('user_sid');
        $this->assign('url',$url);
        return $this->fetch();
    }
    
    public function ckbind($wxid=''){
        if($wxid!=$this->user['weixin_api']){
            return $this->ok_js();
        }else{
            return $this->err_js();
        }
    }


}
