<?php
namespace app\index\controller\wxapp;

use app\common\controller\IndexBase;


class Cache extends IndexBase
{
    public function set($key='',$value=''){
        if ($key&&$value) {
            cache($key,$value,60);
            return $this->ok_js();
        }else{
            return $this->err_js('参数不全');
        }
    }
    
    public function get($key=''){
        if (cache($key)) {
            return $this->ok_js(cache($key));
        }else{
            return $this->err_js('内容不存在');
        }
    }
    
}
