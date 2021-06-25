<?php
namespace app\member\controller\wxapp;

use app\common\controller\MemberBase;
use app\common\model\User AS UserModel;

//小程序 
class Introducer extends MemberBase
{
    public function index($uid=0,$rows=10){
        if (!$uid) {
            $uid = $this->user['uid'];            
        }
        $listdb = UserModel::where('introducer_1',$uid)->field('uid,nickname,username,icon,regdate,lastvist')->paginate($rows);
        $listdb->each(function(&$rs,$key){
            if($rs['icon']){
                $rs['icon'] = tempdir($rs['icon']);
            }
            $rs['regdate'] = format_time($rs['regdate'],true);
            $rs['lastvist'] = format_time($rs['lastvist'],true);
            return $rs;
        });
        return $this->ok_js($listdb);
    }
}