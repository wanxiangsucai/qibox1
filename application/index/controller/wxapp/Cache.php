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
        if(!preg_match('/^[a-z0-9_]+$/i', $key)){
            return $this->err_js('KEY有误!');
        }elseif (cache($key)) {
            if(is_array(cache($key))){
                return $this->err_js('数组内容不允许直接获取');
            }
            return $this->ok_js(cache($key));
        }else{
            return $this->err_js('内容不存在');
        }
    }
    
}
