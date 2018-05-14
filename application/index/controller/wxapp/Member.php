<?php
namespace app\index\controller\wxapp;

use app\common\controller\IndexBase;
use app\common\model\User;

//小程序 用户相关
class Member extends IndexBase
{
    public function get_total(){
        $num = User::where([])->count('id');
        return $this->ok_js(['num'=>$num]);
    }
    
    public function get_list($type='',$rows=1){
        $map = [];
        $data_list = User::where($map)->order("uid desc")->paginate($rows);
        $data_list->each(function($rs,$key){
            $rs['icon'] = tempdir($rs['icon']);
            return $rs;
        });
//         $listdata = User::where([])->limit($rows)->column(true);
//         $listdata = array_values($listdata);
         return $this->ok_js($data_list);
    }
}
