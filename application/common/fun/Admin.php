<?php
namespace app\common\fun;

/**
 * 权限相关
 */
class Admin{
    /**
     * 查找栏目管理员及频道管理员
     * @param number $fid
     * @param string $dirname
     * @return boolean
     */
    public static function sort($fid=0,$dirname=''){
        $user = login_user();
        if ($user['groupid']==3) {
            return true;
        }
        if (empty($dirname)) {
            $dirname = config('system_dirname');
        }
        $str = config('webdb.M__'.$dirname)['admin'];   //频道管理员
        if ($fid) {
            $str .= ','.get_sort($fid,'admin','',$dirname);
        }        
        foreach(explode(',',$str) AS $uid){
            if($uid>0 && $uid==$user['uid']){
                return true;
            }
        }
    }
    
    /**
     * 审核员的权限检查
     * @param number $status 主题表的status字段值
     * @return boolean
     */
    public static function status_check($status=0){
        if (!is_numeric($status)) {
            return false;
        }elseif($status==-1 || $status>1){ //回收站与推荐权限 不显示
            return false;
        }elseif($status==1 && !in_array(count(config('webdb.status_users')), self::status_power())){    //是否有终审权限
            return false;
        }elseif($status==0 && !in_array(0, self::status_power())){
            return false;
        }elseif($status<-1 && !in_array(abs($status)-1, self::status_power())){   //只显示相应的哪级审核权限
            return false;
        }
        return true;
    }
    
    /**
     * 审核员具有哪些权限
     * @return number[]
     */
    public static function status_power($dirname=''){
        $user = login_user();
        if (empty($dirname)) {
            $dirname = config('system_dirname');
        }
        $s_array = config('webdb.M__'.$dirname)['status_users'];
        $powers = [];
        if($s_array && is_array($s_array) && $user){  // && in_array($user['uid'], str_array(implode(',', config('webdb.status_users'))))
            $ck = false;
            for($i=count($s_array);$i>0;$i--){
                if($ck == true){
                    $powers[$i] = $i;
                }
                if(in_array($user['uid'], str_array($s_array[$i-1]))){
                    //return $i;
                    $powers[$i] = $i;
                    if($i==1){
                        $powers[] = 0;
                    }
                    $ck = true;
                }else{
                    $ck = false;
                }
            }
        }
        return array_values($powers);
        //return 0;
    }
}