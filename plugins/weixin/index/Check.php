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
        $result = wx_check_attention($uid);
        if ($result===true) {
            return $this->ok_js([],'已关注');
        }elseif($result===false){
            return $this->err_js('还没关注公众号');
        }else{  //出错了
            return $this->err_js('错误:'.$result,[],2);
        }
    }
}