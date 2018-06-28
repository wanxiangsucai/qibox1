<?php
namespace app\common\fun;

//防攻击
class Ddos{

    /**
     * 新增时的防止
     * @param unknown $data
     * @return boolean
     */
    public function add($data=[]){        
        $uid = login_user('uid');
        if ( cache('ddos_'.$uid) ) {
            return '请不要那么频繁的发表内容!';
        }
        cache('ddos_'.$uid,1,15);
        return true;
    }

}