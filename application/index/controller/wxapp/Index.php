<?php
namespace app\index\controller\wxapp;


use app\common\controller\IndexBase;

//小程序  
class Index extends IndexBase{
    
    public function base(){
        if (empty($this->user)) {
            $this->user = [];
        }else{
            unset($this->user['password'],$this->user['password_rand'],$this->user['qq_api'],$this->user['weixin_api'],$this->user['wxapp_api'],$this->user['config'],$this->user['rmb_pwd']);
        }
        $array = [
            'user'=>$this->user,
            'time'=>$this->request->time(),
            'admin'=>$this->admin,
        ];
        return $this->ok_js($array);
    }
}