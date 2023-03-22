<?php
namespace app\common\fun;
use think\Db;


class Subscribe
{
    /**
     * 查找用户需要订阅的模板消息ID
     * @param string $check 是否做判断订阅商家小程序与否。如果订阅过的话，就不返回订阅模板ID
     * @return void|NULL|string|mixed|unknown|array|boolean|NULL
     */
    public static function get_template($if_check=true){
        static $template = null;
        if($template!==null){
            return $template;
        }
        $template = '';
        $user = login_user();
        if (in_wxapp() && get_wxappAppid()) { //在商家小程序里边访问
            $info = get_wxappinfo(get_wxappAppid());
            if(!$info['wxapp_subscribe_template_id']){
                return ;
            }
            if($if_check && $user && Db::name('qun_weixin')->where('wxapp_appid',get_wxappAppid())->where('uid',$user['uid'])->value('if_dy')){
                return ;
            }
            $template = $info['wxapp_subscribe_template_id'];
        
        }elseif(!$user['subscribe_mp'] && !$user['subscribe_wxapp'] && !$user['wx_attention']){
            if(in_wxapp() && config('webdb.wxapp_subscribe_template_id')){
                $template = config('webdb.wxapp_subscribe_template_id');
            }elseif(in_weixin() && config('webdb.mp_subscribe_template_id')){
                $template = config('webdb.mp_subscribe_template_id');
            }
        }
        return $template;
    }
}

