<?php
namespace app\common\fun;

class Haibao{
    /**
     * 带分销的宣传二维码
     * @param array $userdb
     * @param number $id
     * @return string
     */
    public function img($userdb=[],$id=0){
        $share_url = get_url(auto_url('content/show',['id'=>$id]));
        if (!strstr($share_url,'p_uid=')) {
            if(strstr($share_url,'?')){
                $share_url .= '&';
            }else{
                $share_url .= '?';
            }
            $share_url .= 'p_uid='.$userdb['uid'];
        }
        return get_qrcode($share_url);
    }
}