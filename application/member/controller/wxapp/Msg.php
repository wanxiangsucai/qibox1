<?php
namespace app\member\controller\wxapp;

use app\common\model\Msg AS Model;
use app\common\controller\MemberBase;

class Msg extends MemberBase
{
    /**
     * 查看最新的信息
     * @param number $id
     * @return void|\think\response\Json|\app\member\controller\wxapp\unknown|void|unknown|\think\response\Json
     */
    public function showmore($id=0,$type='new')
    {
        $info = $this->get_info($id);
        if(!is_array($info)){
            return $info;
        }
        
        $this->map = [
                'touid'=>$this->user['uid'],
                'uid'=>$info['uid'],
                'id'=>['<=',$id],
        ];
        
        $this->OrMap = [
                'uid'=>$this->user['uid'],
                'touid'=>$info['uid'],
                'id'=>['<=',$id],
        ];
        
        $this->NewMap = [
                'uid'=>$this->user['uid'],
                'touid'=>$info['uid'],
                'id'=>['>',$id],
                'ifread'=>0,
        ];
        
        $data_list = Model::where(function($query){
            $query->where($this->map);
        })->whereOr(function($query){
            $query->where($this->OrMap);
        })->whereOr(function($query){
            $query->where($this->NewMap);
        })->order("id desc")->paginate(5);

        $data_list->each(function(&$rs,$key){
            $rs['from_username'] = get_user_name($rs['uid']);
            $rs['from_icon'] = get_user_icon($rs['uid']);
            return $rs;
        });
        return $this->ok_js($data_list);
    }
    
    /**
     * 消息列表
     * @return void|unknown|\think\response\Json
     */
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
    
    /**
     * 删除单条信息
     * @param unknown $id
     * @return void|unknown|\think\response\Json
     */
    public function delete($id)
    {
        if (Model::where(['id'=>$id,'uid'=>$this->user['uid']])->delete()) {
            return $this->ok_js();
        }else{
            $this->err_js('删除失败');
        }
    }
    
    /**
     * 发送消息
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function add()
    {
        if($this->request->isPost()){
            $data = $this->request->post();
            $info = $data['uid'] ? get_user($data['uid']) : get_user($data['touser'],'username');
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
                $content = $this->user['username'] . ' 给你发了一条私信,请尽快查收,<a href="'.get_url(urls('member/msg/show',['id'=>$result->id])).'">点击查收</a>';
                send_wx_msg($info['weixin_api'], $content);
                return $this->ok_js();
            }else{
                return $this->err_js('发送失败');
            }
        }
    }
    
    /**
     * 获取单条信息
     * @param number $id
     * @return void|\think\response\Json|unknown
     */
    protected function get_info($id=0){
        $info = getArray(Model::where(['id'=>$id])->find());
        if(!$info){
            return $this->err_js('内容不存在');
        }elseif($info['uid']!=$this->user['uid']&&$info['touid']!=$this->user['uid']){
            return $this->err_js('你无权查看');
        }elseif($info['touid']==$this->user['uid']){
            Model::update(['id'=>$id,'ifread'=>1]);
        }
        return $info;
    }
    
    /**
     * 查看单条信息
     * @param number $id
     * @return void|\think\response\Json|unknown|void|unknown|\think\response\Json
     */
    public function show($id=0)
    {
        $info = $this->get_info($id);        
        if(!is_array($info)){
            return $info;
        }        
        return $this->ok_js($info);
    }
    
    /**
     * 清空信息
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
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
