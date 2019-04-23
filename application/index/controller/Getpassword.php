<?php
namespace app\index\controller;

use app\common\model\User AS UserModel;
use app\common\controller\IndexBase;
use think\Controller;

class Getpassword extends IndexBase
{
    /**
     * 获取邮箱或手机注册码
     * @param string $type
     */
    public function getnum($type='',$to=''){
        //邮箱注册码与手机注册码,不建议同时启用,所以这里没分开处理
        if( time()-get_cookie('send_num') <120 ){
            return $this->err_js('2分钟后,才能再次获取验证码!');
        }
        $to = mymd5($to,'DE');
        $num = cache(get_cookie('user_sid').'_reg') ?: rand(1000,9999);
        $send_num = get_md5_num($to.$num,6);
        $title = '来自《'.config('webdb.webname').'》的验证码,请注册查收';
        $content = '你的验证码是:'.$send_num;
        cache(get_cookie('user_sid').'_reg',$num,600);
        if($type=='mobphone'){
            $result = send_sms($to,$send_num);
        }elseif($type=='email'){
            if( UserModel::get_info($to,'email') ){
                $result = send_mail($to,$title,$content);                
            }else{
                $result = '当前邮箱不存在!';
            }
        }else{
            $result = '请选择类型!';
        }        
        if($result===true){
            set_cookie('send_num', time());
            return $this->ok_js();
        }else{
            return $this->err_js($result);
        }
    }
    
    /**
     * 核对手机或邮箱注册码
     * @param string $num
     * @return void|\think\response\Json
     */
    public function check_num($num='',$field=''){
        //邮箱注册码与手机注册码,不建议同时启用,所以这里没分开处理
        $field = mymd5($field,'DE');
        $_num = cache(get_cookie('user_sid').'_reg');
        $send_num = get_md5_num($field.$_num,6);
        if( $num ==  $send_num){
            return $this->ok_js();
        }
        return $this->err_js('验证码不正确');
    }
    
    /**
     * 校验用户名与密码
     */
    public function check($username='',$password='',$captcha=''){
        if($username!=''){
            $info = UserModel::get_info($username,'username');
            if($info){
                $array = [
                        'uid'=>$info['uid'],
                        'email'=>mymd5($info['email']),
                        'mobphone'=>$info['mobphone']?mymd5($info['mobphone']):'',
                ];
                return $this->ok_js($array);
            }else{
                return $this->err_js('用户不存在!');
            }
        }else{
            $data = get_post('get');
            if(isset($data['captcha'])&&$data['captcha']==''){
                $data['captcha'] = 'test';
            }
            foreach($data AS $key=>$value){
                $name = $key;
                break;
            }
            $result = $this->validate($data, 'Reg.'.$name);
            if( $result!==true ){
                return $this->err_js($result);
            }else{
                return $this->ok_js();
            }            
        }		
    }
    
    /**
     * 取回密码
     * @return mixed|string
     */
    public function index()
    {
        if ($this->user) {
            $this->error('你已经登录了!');
        }
        
        $data = get_post('post');

        $this->get_hook('getpassword_begin',$data);
        hook_listen('getpassword_begin',$data);		
        
        if(IS_POST){
            $info = UserModel::get_info($data['username'],'username');
            if(!$info){
                $this->error('帐号不存在');
            }
            
            if($data['captcha']==''){
                $data['captcha'] = 'test';
            }
            $result = $this->validate($data, 'Reg.captcha');
            if(true !== $result) $this->error($result);
            
            //邮箱注册码与手机注册码,这里只判断只中一种
            $num = cache(get_cookie('user_sid').'_reg');
            $send_num = get_md5_num((($this->webdb['getpassword_by_phone']&&$info['mobphone'])?$info['mobphone']:$info['email']).$num,6);
            if( ($data['email_code']!=$send_num&&$data['phone_code']!=$send_num) || empty($num)) {
                $this->error('验证码不对');
            }
            cache(get_cookie('user_sid').'_reg',null);

            $array = [
                    'uid'=>$info['uid'],
                    'password'=>$data['password'],
            ];
            
            $result = UserModel::edit_user($array); 

            $this->get_hook('getpassword_end',$data,$info);
            hook_listen('getpassword_end',$info,$data);			
            
            if($result){
                $this->success('密码设置成功','index/index');
            }else{
                $this->error('密码设置失败！');
            }
        }
		return $this->fetch();
    }
}
