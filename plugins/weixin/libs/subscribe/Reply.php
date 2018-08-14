<?php 
namespace plugins\weixin\libs\subscribe;

use plugins\weixin\index\Api;

class Reply extends Api
{
    public function run(){
        $this->subscribe_news();
        $this->subscribe_text();
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
            echo $this->give_news(array($array));
            exit;
        }
    }
    
    //新关注回复的纯文本信息
    protected function subscribe_text(){
        $MSG = $this->webdb['weixin_welcome'];
        if($MSG!=''){	//纯文本回复
            if($this->webdb['weixin_type']<2){	//非认证号，不能使用客服接口！
                echo $this->give_text($MSG);
                exit;
            }else{
                send_wx_msg($this->user_appId,$MSG);    //用客服接口的话，就可以跟图文信息不冲突
            }
        }
    }
}