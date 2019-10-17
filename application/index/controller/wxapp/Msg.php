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
        $qun_user = $qun_info = '';
        if ($uid<0) {
            if(!modules_config('qun')){
                return $this->err_js('你没有安装圈子模块!');
            }
            if($maxid<1){
                $qun_info = \app\qun\model\Content::getInfoByid(abs($uid),true);
                if ($this->user) {
                    $qun_user = \app\qun\model\Member::where([
                        'aid'=>abs($uid),
                        'uid'=>$this->user['uid'],
                    ])->find();
                    $qun_user = $qun_user?getArray($qun_user):[];
                }
            }
            isset($qun_info['_viewlimit']) || $qun_info['_viewlimit'] = $qun_info['viewlimit'];
            if($qun_info['_viewlimit'] && empty($this->admin)){
                if (empty($qun_info)) {
                    return $this->err_js('你不是本圈子成员,无权查看聊天内容!');
                }elseif ($qun_info['type']==0){
                    return $this->err_js('你还没通过审核,无权查看聊天内容!');
                }
            }
        }
        $array = model::list_moremsg($this->user['uid'],$uid,$id,$rows,$maxid);
        $array['qun_info'] = $qun_info;
        $array['qun_userinfo'] = $qun_user;
        $array['userinfo'] = [
            'uid'=>$this->user['uid'],
            'username'=>$this->user['username'],
            'nickname'=>$this->user['nickname'],
            'icon'=>tempdir($this->user['icon']),
            'groupid'=>$this->user['groupid'],
        ];
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
