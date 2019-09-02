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
        if (empty($admin)) {
            if (cache('pm_msg_'.$data['uid'])) {
                return ['errmsg'=>'请不要那么频繁的发送消息'];
            }
            cache('pm_msg_'.$data['uid'],$data['touid'],5);
        }
        $result = parent::create($data);
        if ($result) {
            return $result->id;
        }        
    }
	
}