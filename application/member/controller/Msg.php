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
        if (Model::destroy([$id])) {
            $this->success('删除成功','index');
        }else{
            $this->error('删除失败');
        }
    }
    
    public function add($username='')
    {
        if($this->request->isPost()){
            $data = $this->request->post();
            $info = get_user($data['touser'],'username');
            if (!$info) {
                $this->error('该用户不存在!');
            }elseif (!$data['title']) {
                $this->error('标题不能为空');
            }elseif (!$data['content']) {
                $this->error('内容不能为空');
            }
            $data['touid'] = $info['uid'];
            $data['uid'] = $this->user['uid'];
            if(Model::create($data)){
                $this->success('发送成功','index');
            }else{
                $this->error('发送失败');
            }
        }
        //$info = get_user($username,'username');
        $this->assign('username',$username);
        return $this->fetch();
    }
    
    public function show($id=0)
    {
        $info = Model::find($id);
        if(!$info){
            $this->error('没有相应的内容');
        }
        $this->assign('info',$info);
        return $this->fetch();
    }
    
    public function clean()
    {
        $touid=$this->user['uid'];
       // if(Model::destroy(['touid' => $touid])){
        if(Model::where('touid','=',$touid)->delete()){
            $this->success('清空成功','index');
        }else{
            $this->error('清空失败');
        }
    }
}
