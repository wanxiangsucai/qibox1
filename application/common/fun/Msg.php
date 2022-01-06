<?php
namespace app\common\fun;

use plugins\msgtask\model\Task AS TaskModel;
use plugins\msgtask\model\Log as LogModel;
use plugins\weixin\model\WeixinNotice;
/**
 * 群发消息
 */
class Msg{
    
    /**
     * 获取微信模板消息的相关信息
     * @param string $keyword 指定关键字
     * @return unknown
     */
    public static function template($keyword=''){
        $map = [
            'status'=>1,
        ];
        $listdb = cache('weixin_notice_template');
        if (!$listdb) {
            $listdb = WeixinNotice::where($map)->column(true,'keyword');
            cache('weixin_notice_template',$listdb);
        }
        if($keyword){
            return $listdb[$keyword];
        }else{
            return $listdb;
        }        
    }
    
    /**
     * 自定义字段替换处理
     * @param string $keyword
     * @param array $data
     * @param string $url
     * @return mixed
     */
    public static function format_data($keyword='',$data=[],$url=''){
        $array = $data;
        $winfo = self::template($keyword);
        $field_array = json_decode($winfo['data_field'],true);
        if (!$winfo || !$field_array) {
            return ;
        }
        $all_field = [];
        foreach ($field_array AS $rs){
            if($rs['title4']){
                foreach($array AS $key=>$value){
                    $rs['title4'] = str_replace('{'.$key.'}',$value,$rs['title4']);
                }
                $data[$rs['title2']] = $rs['title4'];
            }
            if($rs['title3']){
                $all_field[] = $rs['title2'];
            }            
        }
        foreach($data AS $key=>$value){
            if(!in_array($key, $all_field)){
                unset($data[$key]);
            }
        }
        $data['key_word'] = $keyword;
        $data['page_url'] = $url;
        return $data;
    }
    
    /**
     * 定时群发消息
     * @param number $uid 接收者的UID,也可以是多个,比如[55,225.65]
     * @param string $title 信息标题
     * @param string $content 信息内容
     * @param array $array 扩展信息数据,
     * time指定多少秒后发布,也可以指定未来的时间,
     * msgtype 发送消息类型,同时发送多种消息有逗号隔开,比如msg,wxmsg,phone,mail
     * ext_id ext_sys指定主题的ID及模型,可避免重复插入任务.可忽略,但不建议
     * @return string|boolean
     */
    public static function send($uid=0,$title='',$content='',$array=[]){
        $task_file = RUNTIME_PATH.'Task.txt';
        $task_web_file = RUNTIME_PATH.'Task_web.txt';
        if (!plugins_config('msgtask')) {
            return '系统没有安装 定时群发消息 插件';
        }elseif( time()-filemtime($task_file)>1800 && time()-filemtime($task_web_file)>1800  ){
            return '定时任务没有启动';
        }
        $time = $array['time']?:0;
        $sncode = $array['sncode']?:'';
        $msgtype = $array['msgtype']?:'msg,wxmsg';
        $ext_id = $array['ext_id']?:0;
        $ext_sys = $array['ext_sys']?:0;
        
        if ($time && $time<time()) {
            $time = time()+$time;
        }
        
        $msgtype = str_replace(['wxmsg','msg','phone','email','mail','wx','sms'], [1,0,2,3,3,1,2], $msgtype);
        
        if ($ext_id && empty($ext_sys)) {
            $ext_sys = M('id');
        }
        
        $info = [];
        if ($ext_id) {
            $map = [
                'ext_id'=>$ext_id,
                'ext_sys'=>intval($ext_sys),
            ];
            $info = TaskModel::where($map)->find();
        }
        if (empty($info)) {
            $data = [
                'title'=>$title,
                'content'=>$content,
                'sncode'=>$sncode,
                'begin_time'=>$time,
                'type'=>$msgtype,
                'ext_id'=>$ext_id,
                'ext_sys'=>$ext_sys,
                'ext_data'=>$array['template_data'] ? json_encode(['template_data' => $array['template_data'],]) : '',
            ];
            $result = TaskModel::create($data);
            if(!$result){
                return '入库失败1';
            }
            $tid = $result->id;
        }else{
            $tid = $info['id'];
        }
        
        if(is_array($uid)){
            $uid_array = $uid;
        }else{
            $uid_array = [$uid];
        }
        $_array = [];
        foreach ($uid_array AS $u){
            $_array[] = [
                'touid'=>$u,
                'tid'=>$tid,
            ];
        }
        $obj = new LogModel;
        if ($obj->saveAll($_array)) {
            return true;
        }else{
            return '入库失败2';   
        }        
    }
    
}