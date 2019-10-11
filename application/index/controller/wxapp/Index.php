<?php
namespace app\index\controller\wxapp;


use app\common\controller\IndexBase;

//小程序  
class Index extends IndexBase{
    
    public function base(){
        $array = [
            'user'=>$this->user,
            'time'=>$this->request->time(),
            'admin'=>$this->admin,
        ];
        return $this->ok_js($array);
    }
}
