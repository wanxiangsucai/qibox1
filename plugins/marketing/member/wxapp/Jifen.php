<?php
namespace plugins\marketing\member\wxapp;

use app\common\controller\MemberBase; 
use plugins\marketing\model\Moneylog AS Model;


class Jifen extends MemberBase
{
    public function index($rows=10,$type=0)
    {
        $map = [
            'uid'=>$this->user['uid'],
            'type'=>$type
        ];
        $data_list = Model::where($map)->order("id desc")->paginate($rows);
        $data_list->each(function($rs,$key){
            $rs['title'] = del_html($rs['about']);
            $rs['posttime'] = date('Y-m-d H:i:s',$rs['posttime']);
            return $rs;
        });
        return $this->ok_js($data_list);
    }
}
