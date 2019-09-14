<?php
namespace app\member\controller\wxapp;

use app\common\controller\MemberBase;
use app\common\model\Friend AS FriendModel;

class Friend extends MemberBase
{
    public function act($uid=0,$type=''){
        if ($type=='add') {
            return $this->add($uid);
        }elseif ($type=='del') {
            return $this->del($uid);
        }elseif ($type=='bad') {
            return $this->bad($uid);
        }
    }
    
    /**
     * 加对方为好友,不是加关注
     * @param number $uid 对方UID
     */
    public function add($uid=0){        
        
        $type = 1;  //默认是单向好友
        $info = getArray(FriendModel::where('suid',$this->user['uid'])->where('uid',$uid)->find());
        $rs = getArray(FriendModel::where('suid',$uid)->where('uid',$this->user['uid'])->find());
        if ($info) {
            if($rs){
                if($rs['type']==-1){
                    $msg = '对方把你加入了黑名单,虽然你把他当好友,但他不把你当好友';
                }else{
                    $msg = '你们成为了双向好友!';
                    $type = 2;
                    FriendModel::where('id',$rs['id'])->update(['type'=>$type]);
                }
            }elseif($info['type']==-1){ //从黑名单重新加好友
                $type = 2;
                $msg = '重新加好友成功，对方未必是你的好友';
            }else{
                $type = 2;
                $msg = '你们成为了双向好友。';
                FriendModel::create([
                    'suid'=>$uid,
                    'uid'=>$this->user['uid'],
                    'type'=>$type,
                ]);
            }            
            FriendModel::where('id',$info['id'])->update(['type'=>$type]);
        }elseif($rs){
            $msg = '加为单向好友成功!';
            FriendModel::where('id',$rs['id'])->update([
                'suid'=>$this->user['uid'],
                'uid'=>$uid,
                'type'=>$type,                
            ]);
        }else{
            return $this->err_js('资料有误');
        }
        return $this->ok_js(['type'=>$type],$msg);
    }
    
    /**
     * 移除对方
     * @param number $uid
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function del($uid=0){
        $info = FriendModel::where('suid',$uid)->where('uid',$this->user['uid'])->find();   //获取对方信息
        if ($info && $info['type']!=-1) {   //自己不在对方黑名单
            if($info['type']==2){   //如果是双向好友的话,就要降为单向好友
                FriendModel::where('id',$info['id'])->update(['type'=>1]);
            }            
        }
        $result = FriendModel::where('suid',$this->user['uid'])->where('uid',$uid)->delete();
        if ($result) {
            return $this->ok_js();
        }
        $result = FriendModel::where('suid',$uid)->where('uid',$this->user['uid'])->delete();
        if ($result) {
            return $this->ok_js();
        }else{
            return $this->err_js('删除失败');
        }
    }
    
    /**
     * 把对方加黑名单
     * @param number $uid
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function bad($uid=0){
        $info = FriendModel::where('suid',$uid)->where('uid',$this->user['uid'])->find();
        if($info['type']==2){   //如果是双向好友的话,就要降为单向好友
            FriendModel::where('id',$info['id'])->update(['type'=>1]);
        }
        $result = FriendModel::where('suid',$this->user['uid'])->where('uid',$uid)->update(['type'=>'-1']);
        if ($result) {
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
}
