<?php
namespace app\member\controller;

use app\common\model\User AS UserModel;
use app\common\controller\MemberBase;

class Group extends MemberBase
{
    public function index()
    {
        $data_list = getGroupByid(null,false);
        foreach($data_list AS $gid=>$rs){
            if($rs['type']==0){
                $groupdb[] = $rs;
            }
        }        
        $this->assign('groupdb',$groupdb);
        return $this->fetch();
    }

    public function buy($gid=0)
    {
        if ($gid<1) {
            $this->error('请选择要购买的用户组');
        }
        $data_list = getGroupByid(null,false);
        $info = $data_list[$gid];
        if (empty($info)){
            $this->error('用户组不存在!');
        }elseif ($info['type']) {
            $this->error('系统组,不可以购买');
        }
        
        $array = [
                'uid'=>$this->user['uid'],
                'groupid'=>$gid,
        ];
        if (UserModel::edit_user($array)) {
            $this->success('购买成功');
        }else{
            $this->error('购买失败');
        }
    }
}
