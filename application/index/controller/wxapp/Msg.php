<?php
namespace app\index\controller\wxapp;

use app\common\model\Msg AS Model;
use app\common\controller\IndexBase;


class Msg extends IndexBase{
    
    public function index(){
        return $this->ok_js();
    }
    
    /**
     * 获取某个人跟它人或者是圈子的会话记录
     * @param number $uid 正数用户UID,负数圈子ID
     * @param number $id 某条消息的ID
     * @param number $rows 取几条
     * @param number $maxid 消息中最新的那条记录ID
     * @param number $is_live 是否在视频直播
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function get_more($uid=0,$id=0,$rows=5,$maxid=0,$is_live=0){
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
        if ($this->user) {
            $array['userinfo'] = [
                'uid'=>$this->user['uid'],
                'username'=>$this->user['username'],
                'nickname'=>$this->user['nickname'],
                'icon'=>tempdir($this->user['icon']),
                'groupid'=>$this->user['groupid'],
            ];
        }else{
            $array['userinfo'] = ['uid'=>0,'username'=>'游客','groupid'=>0];
        }
        
        if ($is_live) {
//             $live_array = cache('live_qun');
//             $data = $this->request->post();
//             if($live_array['qun'.$uid]['flv_url']!=$data['flv_url']){
//                 fun('alilive@add',$this->user['uid'],$uid,'qun',$data);
//             }
//             $live_array['qun'.$uid] = [
//                 'flv_url'=>$data['flv_url'],
//                 'm3u8_url'=>$data['m3u8_url'],
//                 'rtmp_url'=>$data['rtmp_url'],
//                 'time'=>time(),
//             ];
//             cache('live_qun',$live_array);
        }elseif($uid<1){    //代表群聊
            $live_array = cache('live_qun');
            if($live_array['qun'.$uid]){
                $live_array['qun'.$uid]['time'] = 0;    //此参数将弃用
                $live_array['qun'.$uid]['push_url']='';
                $array['live_video'] = $live_array['qun'.$uid];
            }else{
                $live_array['qun'.$uid]['time'] = 100;//此参数将弃用
            }
        }        
        return $this->ok_js($array);
    }
    
    /**
     * 调取各个圈子的最新留言
     * @return void|unknown|\think\response\Json
     */
    public function newmsg(){
        $data = [];
        $array = model::where('qun_id','>',0)->order('id desc')->limit(10)->column(true);
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
