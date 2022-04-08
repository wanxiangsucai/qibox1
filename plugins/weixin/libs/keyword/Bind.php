<?php
namespace plugins\weixin\libs\keyword;

use plugins\weixin\index\Api;
use app\common\model\User AS UserModel;

class Bind extends Api
{
    public function run(){
		$this->bd();
    }
    
    protected function bd(){
        $content = $this->From_content;
        if(preg_match("/^bind([\d]+)$/i",$content,$array)){
            $rands = $array[1];
            $str = cache('binds'.$rands);
            if (!$str) {
                echo $this->give_text("不存在校验信息!");
                exit;
            }
            cache('binds'.$rands,null);
            list($uid,$url) = $str;
            $buser = get_user($uid);
            if (!$buser) {
                echo $this->give_text("该UID帐号不存在");
                exit;
            }
            $user = UserModel::get_info($this->user_appId,'weixin_api');
            if (empty($user) || $user['uid']!=$uid) {
                if($buser['weixin_api']){
                    send_wx_msg($buser['weixin_api'], '你的帐号 '.$buser['username'].' 被其它微信绑定了！如果不是你操作，可能帐号被盗，请马上联系管理员。');
                }
                if($user){
                    edit_user([
                        'uid'=>$user['uid'],
                        'weixin_api'=>'',
                        'unionid'=>'',
                        'wxapp_api'=>'',
                        'wxopen_api'=>'',
                        'wx_attention'=>0,
                    ]);
                }
                
                $ac = wx_getAccessToken();
                $string = file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$ac.'&openid='.$this->user_appId.'&lang=zh_CN');
                $data = json_decode($string,true);
                
                edit_user([
                    'uid'=>$uid,
                    'weixin_api'=>$this->user_appId,
                    'wx_attention'=>1,
                    'unionid'=>$data['unionid']?:'',
                ]);
                $msg = "当前微信成功绑定此帐号:“".$buser['username']."”";
                if ($user) {
                    $msg .= "，原帐号:“".$user['username']."”已解绑本微信登录";
                }
                if ($url && !is_numeric($url)) {
                    $msg .= '<a href="'.$url.'">'.$msg.'，请点击进行相关业务操作</a>';
                }
                echo $this->give_text($msg);
            }else{
                echo $this->give_text("请不要重复绑定");
            }
            exit;
        }
    }
    
}