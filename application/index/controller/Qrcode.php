<?php
namespace app\index\controller;

use app\common\controller\IndexBase;

class Qrcode extends IndexBase
{
    public function index($url = 'http://www.baidu.com',$size=6){
        include_once (ROOT_PATH.'vendor/phpqrcode/phpqrcode.php');
    }    
}

?>