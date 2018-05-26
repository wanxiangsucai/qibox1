<?php
namespace app\member\controller;

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
        $pages = $data_list->render();
        $listdb = getArray($data_list)['data'];
        
        //给模板赋值变量
        $this->assign('pages',$pages);
        $this->assign('listdb',$listdb);
        
        return $this->fetch();
    }

    public function delete($id)
    {
        if (Model::where(['id'=>$id,'uid'=>$this->user['uid']])->delete()) {
            $this->success('删除成功','index');
        }else{
            $this->error('删除失败');
        }
    }
    
    public function add($username='',$uid=0)
    {
        if($this->request->isPost()){
            $data = $this->request->post();
            $info = get_user($data['touser'],'username');
            if (!$info) {
                $this->error('该用户不存在!');
            }elseif (!$data['content']) {
                $this->error('内容不能为空');
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
                $this->success('发送成功','index');
            }else{
                $this->error('发送失败');
            }
        }
        if($uid){
            $username = get_user($uid)['username'];
        }
        $this->assign('username',$username);
        return $this->fetch();
    }
    
    public function show($id=0)
    {
        $info = Model::where(['id'=>$id])->find();
        if(!$info){
            $this->error('内容不存在');
        }elseif($info['uid']!=$this->user['uid']&&$info['touid']!=$this->user['uid']){
            $this->error('你无权查看');
        }elseif($info['touid']==$this->user['uid']){
            Model::update(['id'=>$id,'ifread'=>1]);
        }        
        $this->assign('info',$info);
        return $this->fetch();
    }
    
    public function clean()
    {
        $touid=$this->user['uid'];
        if(Model::where('touid','=',$touid)->delete()){
            $this->success('清空成功','index');
        }else{
            $this->error('清空失败');
        }
    }
}
