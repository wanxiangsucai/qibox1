<?php
namespace app\common\fun;

use think\Db;

/**
 * 数据统计使用
 */
class Count{
    /**
     * 统计内容条数
     * @param string $table 数据表名,不要加前缀
     * @param number $uid 为数字的话,就指定为用户UID, 也可以设置为查询数组
     * @param array $map 附加查询数组
     * @return number|string
     */
    public static function Info($table='',$uid=0,$map=[]){
        if (preg_match('/^qb_/i', $table)) {
            $table = str_replace('qb_', '', $table);
        }
        if (is_array($uid)) {
            $map = $uid;
        }elseif(is_numeric($uid) && $uid>0){
            $map['uid'] = intval($uid);
        }
        return Db::name($table)->where($map)->count('*');
    }
    

    /**
     * 求和
     * @param string $table 数据表名,不要加前缀
     * @param number $uid 为数字的话,就指定为用户UID, 也可以设置为查询数组
     * @param string $field 求和的具体字段
     * @param array $map 附加查询数组
     * @return number
     */
    public static function sum($table='',$uid=0,$field='uid',$map=[]){
        if (preg_match('/^qb_/i', $table)) {
            $table = str_replace('qb_', '', $table);
        }
        if (is_array($uid)) {
            $map = $uid;
        }elseif(is_numeric($uid) && $uid>0){
            $map['uid'] = intval($uid);
        }
        return Db::name($table)->where($map)->sum($field);
    }
    
    /**
     * 统计用户消费的金额
     * @param number $uid
     * @param unknown $time
     * @return number|mixed|\think\cache\Driver|boolean
     */
    public static function rmb($uid=0,$time=10800){
        $uid = intval($uid);
        $map = [
                'uid'=>$uid,
                'ifpay'=>1,
        ];
        $num = cache('user_rmb_total_'.$uid);
        if ($num=='') {
            $num = Db::name('rmb_infull')->where($map)->sum('money');
            cache('user_rmb_total_'.$uid,$num,$time);
        }        
        return $num;
    }
    
}