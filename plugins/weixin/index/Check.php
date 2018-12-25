<?php
namespace plugins\weixin\index;

use app\common\controller\IndexBase;


class Check extends IndexBase
{
    /**
     * 检查用户是否已关注公众号
     * @param number $uid
     */
    public function ifgz($uid=0){
        if (empty($uid)) {
            $uid = $this->user['weixin_api'];
        }
        if (config('webdb.weixin_type')!=3) {
            return $this->err_js();
        }
        if (wx_check_attention($uid)===true) {
            return $this->ok_js();
        }else{
            return $this->err_js();
        }
    }
}