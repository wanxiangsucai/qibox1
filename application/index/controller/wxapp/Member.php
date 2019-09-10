<?php
namespace app\index\controller\wxapp;

use app\common\controller\IndexBase;
use app\common\model\User;

//小程序 用户相关
class Member extends IndexBase
{
    /**
     * 统计全站用户总数
     * @return void|unknown|\think\response\Json
     */
    public function get_total(){
        $num = User::where([])->count('id');
        return $this->ok_js(['num'=>$num]);
    }
    
    private function format_field(&$rs=[]){
        $rs['icon'] = tempdir($rs['icon']);
        unset($rs['password'],$rs['password_rand'],$rs['qq_api'],$rs['weixin_api'],$rs['wxapp_api'],$rs['lastip'],$rs['regip'],$rs['email'],$rs['address'],$rs['mobphone'],$rs['idcard'],$rs['truename'],$rs['config'],$rs['rmb'],$rs['rmb_pwd']);
    }
    
    /**
     * 获取用户列表
     * @param string $type
     * @param number $rows
     * @return void|unknown|\think\response\Json
     */
    public function get_list($type='',$rows=1){
        $map = [];
        $data_list = User::where($map)->order("uid desc")->paginate($rows);
        $data_list->each(function($rs,$key){            
            $this->format_field($rs);
            return $rs;
        });
//         $listdata = User::where([])->limit($rows)->column(true);
//         $listdata = array_values($listdata);
         return $this->ok_js($data_list);
    }
    
    /**
     * 根据UID获取用户资料
     * @param number $uid
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function getbyid($uid=0){
        $user = get_user($uid,'uid');
        if ($user) {
            $this->format_field($user);
            return $this->ok_js($user);
        }else{
            return $this->err_js('用户不存在');
        }
    }
    
    /**
     * 根据用户名获取用户的UID
     * @param string $name
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function get_uid($name=''){
        $user = get_user($name,'username');
        if ($user) {
            return $this->ok_js(['uid'=>$user['uid']]);
        }else{
            return $this->err_js('用户不存在');
        }
    }
    
    
}
