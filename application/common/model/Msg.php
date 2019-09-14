<?php
namespace app\common\model;

use think\Model;

/**
 * 站内短消息
 * @package app\admin\model
 */
class Msg extends Model
{
    //protected $table = '__MEMBERDATA__';
	
	//主键不是ID,要单独指定
	//public $pk = 'id';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;


    public static function add($data=[],$admin=null){
        $info = Friend::where('uid',$data['touid'])->where('suid',$data['uid'])->find();
        if ($info['type']==-1) {
            return ['errmsg'=>'对方把你列入了黑名单,因此无法给他发消息'];
        }
        if (empty($admin) && empty($info)) {
            if (cache('pm_msg_'.$data['uid'])) {
                return ['errmsg'=>'请不要那么频繁的发送消息'];
            }
            cache('pm_msg_'.$data['uid'],$data['touid'],5);
        }
        $result = parent::create($data);
        if($data['uid']>0){
            if (empty($info)) {
                Friend::add($data['uid'],$data['touid']);    //给用户发消息,就相当于成为他的粉丝
            }else{
                if ($info['type']!=2) {
                    Friend::where('id',$info['id'])->update(['type'=>2]);   //把对方也设置为双向好友
                }
                $res = Friend::where('suid',$data['touid'])->where('uid',$data['uid'])->update(['type'=>2,'update_time'=>time()]);
                if (empty($res)) {   //如果自己当中没有对方的资料,就要新建一条
                    Friend::create([
                        'suid'=>$data['touid'],
                        'uid'=>$data['uid'],
                        'type'=>2
                    ]);
                }
            }            
        }
        if ($result) {
            return $result->id;
        }        
    }
	
}