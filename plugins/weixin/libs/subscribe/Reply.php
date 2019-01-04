<?php 
namespace plugins\weixin\libs\subscribe;

use plugins\weixin\index\Api;

class Reply extends Api
{
    public function run(){
        $msg = $this->subscribe_news();
        if ($msg!='') {
            echo $msg;
        }else{            
            $msg = $this->subscribe_text();
            if ($msg!='') {
                echo $msg;
            }
        }
        
    }
    
    //新关注回复图文信息
    protected function subscribe_news(){
        if($this->webdb['weixin_welcome_title']!=''&&$this->webdb['weixin_welcome_link']!=''){
            $array = array(
                    'title'=>$this->webdb['weixin_welcome_title'],
                    'picurl'=>tempdir($this->webdb['weixin_welcome_pic']),
                    'about'=>$this->webdb['weixin_welcome_desc'],
                    'url'=>$this->webdb['weixin_welcome_link'],
            );
            return $this->give_news(array($array));
        }
    }
    
    //新关注回复的纯文本信息
    protected function subscribe_text(){
        $MSG = $this->webdb['weixin_welcome'];
        if($MSG!=''){	//纯文本回复
 //           if($this->webdb['weixin_type']<2){	//非认证号，不能使用客服接口！
                return $this->give_text($MSG);
//             }else{
//                 send_wx_msg($this->user_appId,$MSG);    //用客服接口的话，就可以跟图文信息不冲突
//             }
        }
    }
}