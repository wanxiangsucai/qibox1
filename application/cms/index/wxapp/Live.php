<?php
namespace app\cms\index\wxapp;

use app\common\controller\IndexBase;
use app\cms\model\Content AS ContentModel;
use plugins\msgtask\model\Log AS LogModel;
use plugins\msgtask\model\Task AS TaskModel;

class Live extends IndexBase
{
    /**
     * 订阅直播
     * @param number $id
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function fav($id=0){
        $task_file = RUNTIME_PATH.'Task.txt';
        $task_web_file = RUNTIME_PATH.'Task_web.txt';
        
        if (!plugins_config('msgtask')) {
            return $this->err_js('系统没安装“定时群发短消息”插件');
        }elseif( time()-filemtime($task_file)>1800 && time()-filemtime($task_web_file)>1800  ){
            return $this->err_js('定时任务没有启动');
        }
        
        $info = ContentModel::getInfoByid($id);
        if (empty($info)){
            return $this->err_js('内容不存在');
        }elseif($info['start_time']<time()){
            return $this->err_js('直播时间不存在,或已过期');
        }
        $map = [
            'ext_sys'=>M('id'),
            'ext_id'=>$id,
        ];
        $task = getArray(TaskModel::where($map)->find());
        if ($task['id'] && LogModel::where('touid',$this->user['uid'])->where('tid',$task['id'])->find()){
            return $this->err_js('你已经预约过了!');
        }
        
        $title = '直播开始了!';
        $url = get_url(iurl('content/show',['id'=>$id]));
        $content = "<a href=\"{$url}\" target=\"_blank\">你预约的直播“{$info['title']}”开始了，点击收看</a>";
        $array = [
            'time'=>$info['start_time'],
            'ext_sys'=>M('id'),
            'ext_id'=>$id,
            'msgtype'=>'msg,wxmsg',
        ];
        $reshut = fun('msg@send',$this->user['uid'],$title,$content,$array);
        if ($reshut===true) {
            return $this->ok_js();
        }else{
            return $this->err_js($reshut);
        }
    }
    


}
