<?php
namespace app\index\controller;

use app\common\controller\IndexBase;
use \tncode\Tncode AS TncodeClass;

class Tncode extends IndexBase
{
    public function img(){
        $tn  = new TncodeClass();
        $tn->make();
        exit;
    }
    
    public function check($code=''){
        $tn  = new TncodeClass();
        if ($tn->check($code)) {
            cache('tn_code'.get_cookie('user_sid'),1,180);
            return 'ok';//$this->ok_js();
        }else{
            return 'error';//$this->err_js();
        }
    }
}

