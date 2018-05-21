<?php
namespace app\member\controller\wxapp;

use app\common\model\Msg AS Model;
use app\common\controller\MemberBase;

class Msg extends MemberBase
{
    public function index()
    {
        $map = [
                'touid'=>$this->user['uid']                
        ];
        $data_list = Model::where($map)->order("id desc")->paginate(15);
        $data_list->each(function($rs,$key){
            $rs['from_username'] = get_user_name($rs['uid']);
            return $rs;
        });       
        return $this->ok_js($data_list);
    }
    
    /**
     * 检查是否有新消息
     * @return void|\think\response\Json
     */
    public function checknew(){
        $num = Model::where([ 'touid'=>$this->user['uid'],'ifread'=>0 ])->count('id');
        if($num>0){
            return $this->ok_js(['num'=>$num]);
        }else{
            $this->err_js('没有新消息');
        }
    }

    public function delete($id)
    {
        if (Model::where(['id'=>$id,'uid'=>$this->user['uid']])->delete()) {
            return $this->ok_js();
        }else{
            $this->err_js('删除失败');
        }
    }
    
    public function add()
    {
        if($this->request->isPost()){
            $data = $this->request->post();
            $info = get_user($data['touser'],'username');
            if (!$info) {
                return $this->err_js('该用户不存在!');
            }elseif (!$data['content']) {
                return $this->err_js('内容不能为空');
            }
            if (!$data['title']) {
                $data['title'] = '来自 '.$this->user['username'].' 的私信';
            }
            $data['touid'] = $info['uid'];
            $data['uid'] = $this->user['uid'];
            $result = Model::create($data);
            if($result){
                $content = $this->user['username'] . ' 给你发了一条私信,请尽快查收,<a href="'.get_url(urls('show',['id'=>$result->id])).'">点击查收</a>';
                send_wx_msg($info['weixin_api'], $content);
                return $this->ok_js();
            }else{
                return $this->err_js('发送失败');
            }
        }
    }
    
    public function show($id=0)
    {
        $info = Model::where(['id'=>$id])->find();
        if(!$info){
            $this->err_js('内容不存在');
        }elseif($info['uid']!=$this->user['uid']&&$info['touid']!=$this->user['uid']){
            $this->err_js('你无权查看');
        }elseif($info['touid']==$this->user['uid']){
            Model::update(['id'=>$id,'ifread'=>1]);
        } 
        Model::update(['id'=>$id,'ifread'=>1]);
        return $this->ok_js($info);
    }
    
    public function clean()
    {
        $touid=$this->user['uid'];
       // if(Model::destroy(['touid' => $touid])){
        if(Model::where('touid','=',$touid)->delete()){
            return $this->ok_js();
        }else{
            return $this->err_js('清空失败');
        }
    }
}
