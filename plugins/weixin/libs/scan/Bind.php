<?php
namespace plugins\weixin\libs\scan;

use plugins\weixin\index\Api;
use app\common\model\User AS UserModel;

class Bind extends Api
{
    public function run(){
        $this->bd();
    }
    
    protected function bd(){
        //$content = $this->From_content;
        $content = $this->EventKey;
        $content = str_replace('qrscene_', '', $content); //新关注的话,会出现qrscene_开头的标志
        if(preg_match("/^bind([\d]+)$/i",$content,$array)){
            $uid = $array[1];
            $str = cache('bind'.$uid);
            if (!$str) {
                echo $this->give_text("资料有误,不存在!");
                exit;
            }            
            $buser = get_user($uid);
            if (!$buser) {
                echo $this->give_text("该UID帐号不存在");
                exit;
            }
            $user = UserModel::get_info($this->user_appId,'weixin_api');
//             if (NewUser===true) {                
//                 edit_user([
//                     'uid'=>$this->user['uid'],
//                     'weixin_api'=>'',
//                     'wx_attention'=>0,
//                 ]);
//             }else
           if ($buser['weixin_api'] && $buser['weixin_api']!=$this->user_appId) {
               $randstr = rand(20000,99999);
               cache('binds'.$randstr,[$uid,$str],300);
               echo $this->give_text("提醒：该帐号《".$buser['username']."》已经绑定了其它微信，你可以选择用之前的微信扫码关注，或者是发送“bind{$randstr}”把该帐号:".$buser['username']." 重新绑定到你现在使用的微信");
               exit;
            }elseif ($user && $user['uid']!=$uid) {
                $randstr = rand(20000,99999);
                cache('binds'.$randstr,[$uid,$str],300);
                echo $this->give_text("提醒：你现在使用的微信原来绑定的帐号是《".$user['username']."》,你若想取消它，想要重新绑定到当前帐号《".$buser['username']."》的话，请发送“bind{$randstr}”即可把".$buser['username']."绑定到现在的微信，之前的帐号就会自动解绑！");
                exit;
            }
            
            $ac = wx_getAccessToken();
            $string = file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$ac.'&openid='.$this->user_appId.'&lang=zh_CN');
            $data = json_decode($string,true);
            
            $array = [
                'uid'=>$uid,
                'weixin_api'=>$this->user_appId,
                'wx_attention'=>1,
                'unionid'=>$data['unionid']?:'',
            ];
            if($buser['wxapp_api']!='' && $buser['unionid']!=$data['unionid']){
                $array['wxapp_api'] = '';
            }
            edit_user($array);
            cache('bind'.$uid,null);
            if (!is_numeric($str)) {
                echo $this->give_text('<a href="'.get_url($str).'">绑定成功，请点击进行相关业务操作</a>');
            }else{
                echo $this->give_text('当前微信成功绑定帐号“'.$buser['username'].'”,你可以继续相应操作了');
            }
            exit;
        }
    }
    
}