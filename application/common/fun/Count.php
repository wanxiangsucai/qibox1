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
}