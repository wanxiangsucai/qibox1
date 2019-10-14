<?php
namespace app\index\controller\wxapp;

use app\common\model\Msg AS Model;
use app\common\controller\IndexBase;

//小程序  
class Msg extends IndexBase{
    
    public function index(){
        return $this->ok_js();
    }
    
    /**
     * 获取某个人跟它人或者是圈子的会话记录
     * @param number $uid
     * @param number $id
     * @param number $rows
     * @param number $maxid
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function get_more($uid=0,$id=0,$rows=5,$maxid=0){
        if (empty($this->user) && ($uid>=0 || $id>0)) {
            return $this->err_js("请先登录");
        }
        $array = model::list_moremsg($this->user['uid'],$uid,$id,$rows,$maxid);
        return $this->ok_js($array);
    }
    
    /**
     * 调取各个圈子的最新留言
     * @return void|unknown|\think\response\Json
     */
    public function newmsg(){
        $data = [];
        $array = model::where('qun_id','>',0)->order('id desc')->limit(10)->column();
        foreach($array AS $rs){
            $rs['username'] = get_user_name($rs['uid']);
            $rs['time'] = format_time($rs['create_time'],true);
            $rs['qun_name'] = fun('qun@getByid',$rs['qun_id'])['title'];
            $rs['user_url'] = get_url('user',$rs['uid']);
            $rs['qun_url'] = get_url('msg',-$rs['qun_id']);
            $rs['title'] = get_word(del_html($rs['content']), 50);            
            $data[] = $rs;
        }
        return $this->ok_js($data);
    }
}
