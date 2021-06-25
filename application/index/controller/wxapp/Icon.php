<?php
namespace app\index\controller\wxapp;
use app\common\controller\IndexBase;

class Icon extends IndexBase{
    public function index(){
        $data = [];
        preg_match_all("/<i class=\"([^\"]+)\">/is", file_get_contents(STATIC_PATH.'icon/index.html'),$array);
        foreach($array[1] AS $value){
            $data[] = $value;
        }
        return $this->ok_js($data);
    }
}