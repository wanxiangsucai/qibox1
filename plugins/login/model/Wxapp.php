<?php
namespace plugins\login\model;
use app\common\model\User AS UserModel;

//小程序用户注册
class Wxapp extends UserModel
{
    public static function api_reg($openid,$data=array()){
        if($openid==''){
            return 'openid 值不存在！';
        }elseif($data['nickName']==''){
			$data['nickname'] = '小程序用户'.rands(5);
            //return 'nickName 昵称不存在！';
        }
        
        if( self::check_wxappIdExists( $openid ) ){
            return '当前微信号已经注册过了！';
        }
        
        $data['nickName'] = self::filterEmoji($data['nickName']);
        $username = $nickname = str_replace(array('|',' ','',"'",'"','/','*',',','~',';','<','>','$',"\\","\r","\t","\n","`","!","?","%","^"),'',$data['nickName']);
        
        $address = filtrate("{$data['province']} {$data['city']}");
        
        if(self::check_username($username)!==true){ //用户名不合法或者有非法字符
            $username='aa_'.rands(10);
        }elseif(strlen($username)>50||strlen($username)<2){            
            $username = get_word($username,16,0).'_'.static::get_top_uid();
        }
        
        $openid = filtrate($openid);
        //$username = filtrate($username);
        $icon = filtrate($data['avatarUrl']);
        $sex = ($data['gender']=='男'||$data['gender']==1)?1:2;
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
                'unionid'=>$data['unionid']?filtrate($data['unionid']):'',
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
                'wxapp_api'=>$openid,
                'bday'=>$bday,
        );
        
        //入库
        $uid = self::register_user($array);
        if($uid<1){
            return $uid;
        }
        
        $array['uid'] = $uid;
        return $array;
    }
}