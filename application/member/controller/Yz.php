<?php
namespace app\member\controller;

use app\common\model\User AS UserModel;
use app\common\controller\MemberBase;


class Yz extends MemberBase
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
        $num = cache(get_cookie('user_sid').'_reg') ?: rand(1000,9999);
        $send_num = get_md5_num($to.$num,6);
        $title = '来自《'.config('webdb.webname').'》的注册验证码,请注册查收';
        $content = '你的注册验证码是:'.$send_num;
        cache(get_cookie('user_sid').'_reg',$num,600);
        if($type=='mobphone'){
            $result = send_sms($to,$send_num);
        }elseif($type=='email'){
            $rs = UserModel::get_info($to,'email');
            if($rs && $rs['uid']!=$this->user['uid'] ){
                $result = '当前邮箱已经被另个一帐号 '.$rs['username'].' 占用了,请更换一个邮箱';
            }else{
                $result = send_mail($to,$title,$content);
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
    
    public function index($email='',$email_code='')
    {
        if($this->request->isPost()){
            $num = cache(get_cookie('user_sid').'_reg');
            $send_num = get_md5_num($email.$num,6);
            if( $email_code!=$send_num  || empty($num)) {
                $this->error('验证码不对');
            }
            $array = [
                    'uid'=>$this->user['uid'],
                    'email'=>$email,
                    'email_yz'=>1,
            ];
            if (UserModel::edit_user($array)) {
                $this->success('验证成功','index');
            }else{
                $this->error('数据写入失败');
            }
        }
        return $this->fetch();
    }
    
    //邮箱验证
//     public function index($code='')
//     {
//         $code = input('get.code');
//         if ($code!='') {
//             list($uid,$time,$email) = explode("\t", $code);
//             if(time()-$time>1800){  //半小时有效
//                 $this->error('链接已失效,请重新发送邮件');
//             }
//             $array = [
//                     'uid'=>$this->user['uid'],
//                     'email'=>$email,
//                     'email_yz'=>1,
//             ];
//             if (UserModel::edit_user($array)) {
//                 $this->success('验证成功','index');
//             }else{
//                 $this->error('数据写入失败');
//             }
//         }elseif($this->request->isPost()){
//             $email = input('email');
//             $content = mymd5($this->user['uid'] . "\t" . time() . "\t" . $email);
//             send_mail($email, '邮箱验证', "请点击以下链接进行邮箱验证<a target='_blank' href='" . $this->weburl . "'>" .$this->weburl . "</a>");
//         }
//         return $this->fetch();
//     }
    
    //身份证验证
    public function idcard()
    {
        $data = get_post('post');
        if($this->request->isPost()){
            if($this->user['idcard_yz']){
                $this->error('资料已通过审核,不可再修改');
            }
            if($data['truename']==''||$data['idcard']==''){
                $this->error('主体名称与证件号码为必填项');
            }
            $array = [
                    'uid'=>$this->user['uid'],
                    'truename'=>$data['truename'],
                    'idcard'=>$data['idcard'],
                     'idcardpic'=>$data['idcardpic'],
            ];
            if (UserModel::edit_user($array)) {
                $this->success('数据已成功提交,请等待管理员人工审核');
            }else{
                $this->error('数据写入失败');
            }
        }
        return $this->fetch();
    }
    
    public function mob($mobphone='',$mobphone_code='')
    {
        if($this->request->isPost()){            
            $num = cache(get_cookie('user_sid').'_reg');
            $send_num = get_md5_num($mobphone.$num,6);
            if( $mobphone_code!=$send_num  || empty($num)) {
                $this->error('验证码不对');
            }
            
            $array = [
                    'uid'=>$this->user['uid'],
                    'mobphone'=>$mobphone,
                    'mob_yz'=>1,
            ];
            
            if (UserModel::edit_user($array)) {
                $this->success('验证成功','index');
            }else{
                $this->error('数据写入失败');
            }
        }
        return $this->fetch();
    }
    
    //手机号码验证
//     public function mob($mobphone='')
//     {
//         session([
//                 'prefix'         => 'yzuser',
//                 'type'           => '',
//                 'auto_start'     => true,
//         ]);
        
//         if($this->request->isPost()){
//             $num = input('post.num');
//             $code = session('uid_'.$this->user['uid'].'_'.$num);
//             list($uid,$time,$mob) = explode("\t", $code);
//             if(time()-$time>1800){  //半小时有效
//                 $this->error('验证码已失效,请重新发送证码');
//             }
//             $array = [
//                     'uid'=>$this->user['uid'],
//                     'mobphone'=>$mob,
//                     'mob_yz'=>1,
//             ];
//             if (UserModel::edit_user($array)) {
//                 $this->success('验证成功','index');
//             }else{
//                 $this->error('数据写入失败');
//             }
//         }elseif ($mobphone!='') {
//             $rands = rand(1000,9999);
//             $content = mymd5($this->user['uid'] . "\t" . time() . "\t" . $mobphone);
//             session('uid_'.$this->user['uid'].'_'.$rands,$content);
//             send_sms( $mobphone , '你的邮箱验证码是:' . $rands );
//         }
//         return $this->fetch();
//     }

}
