<?php
namespace plugins\login\model;
use app\common\model\User AS UserModel;

//QQ用户注册
class Qq extends UserModel
{
    public static function api_reg($openid,$data=array()){

        if($data['nickname']=='' || $openid==''){
            return 'nickname 或 openid 值不存在！';
        }
        

        if(!defined('QQ_INAPP') && self::check_qqIdExists( $data['openid'] ) ){
            return '当前QQ号已经注册过了！';
        }elseif(defined('QQ_INAPP') && self::get_info( ['qq_open_api'=>$data['openid']]) ){
            return '当前QQ号已经注册过了!!!';
        }
        
        $username = $nickname = str_replace(array('|',' ','',"'",'"','/','*',',','~',';','<','>','$',"\\","\r","\t","\n","`","!","?","%","^"),'',$data['nickname']);
        
        $address = filtrate("{$data['province']} {$data['city']}");
        
        
        if(self::check_username($username)!==true){ //用户名不合法或者有非法字符
            $username='QQ用户';
        }elseif(strlen($username)>50||strlen($username)<2){
            $username = get_word($username,16,0).'_'.static::get_top_uid();
        }
        
        $openid = filtrate($openid);
        //$username = filtrate($username);
        $icon = filtrate($data['figureurl_qq_2']);
        $sex = $data['gender']=='男'?1:2;
        $address = filtrate($address);
        $groupid=8;
        
        //$username = get_word($username,40,0);	//帐号不能太长
        if(self::check_userexists($username)){	//检查用户名是否已存在
            $username .='-'.static::get_top_uid();
        }
        
        //随机生成邮箱与密码
        $password = rands(10);
        $email = rands(20).'@123.cn';
        
        $bday = $data['year']?$data['year'].'-00-00':'';
        
        $array = array(
                'username'=>$username,
                'nickname'=>$nickname,
                'password'=>$password,
                'email'=>$email,
                'groupid'=>$groupid,
                'icon'=>$icon,
                'yz'=>1,
                'lastvist'=>time(),
                'lastip'=>get_ip(),
                'regdate'=>time(),
                'regip'=>get_ip(),
                'sex'=>$sex,
                'address'=>$address,
                'qq_api'=>$openid,
                'bday'=>$bday,
        );
        
        if(defined('QQ_INAPP')){
            $array['qq_api'] = '';
            $array['qq_open_api'] = $openid;
        }
        
        //入库
        $uid = self::register_user($array);
        if($uid<1){
            return $uid;
        }
        
        $array['uid'] = $uid;
        return $array;
    }
}