<?php
namespace app\member\controller;

use app\common\model\Msg AS Model;
use app\common\controller\MemberBase;

class Msg extends MemberBase
{
    /**
     * 获取单条信息
     * @param number $id
     * @return void|\think\response\Json|unknown
     */
    protected function get_info($id=0){
        $info = getArray(Model::where(['id'=>$id])->find());
        if(!$info){
            return '内容不存在';
        }elseif($info['uid']!=$this->user['uid']&&$info['touid']!=$this->user['uid']){
            return '你无权查看';
        }elseif($info['touid']==$this->user['uid']){
            Model::update(['id'=>$id,'ifread'=>1]);
        }
        return $info;
    }
    
    /**
     * 标签调用 ,查看往来消息
     * @param array $config
     * @return void|\think\response\Json|\app\member\controller\unknown|unknown
     */
    public function showmore($config=[])
    {
        $cfg = unserialize($config['cfg']);
        $id = $cfg['id'];
        $rows = $cfg['rows'];
        $info = $this->get_info($id);
        if(!is_array($info)){
            return ;
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
        
//         $this->NewMap = [
//                 'uid'=>$this->user['uid'],
//                 'touid'=>$info['uid'],
//                 'id'=>['>',$id],
//                 'ifread'=>0,
//         ];
        
        $data_list = Model::where(function($query){
            $query->where($this->map);
        })->whereOr(function($query){
            $query->where($this->OrMap);
//         })->whereOr(function($query){
//             $query->where($this->NewMap);
        })->order("id desc")->paginate($rows);
        
        $data_list->each(function(&$rs,$key){
            $rs['from_username'] = get_user_name($rs['uid']);
            $rs['from_icon'] = get_user_icon($rs['uid']);
            return $rs;
        });
            return $data_list;
    }
    
    /**
     * 收件箱
     * @return unknown
     */
    public function index()
    {
        $map = [
                'touid'=>$this->user['uid']                
        ];
        $data_list = Model::where($map)->order("id desc")->paginate(15);
        $data_list->each(function($rs,$key){
            $rs['from_username'] = $rs['uid']?get_user_name($rs['uid']):'系统消息';
            return $rs;
        });
        $pages = $data_list->render();
        $listdb = getArray($data_list)['data'];
        
        //给模板赋值变量
        $this->assign('pages',$pages);
        $this->assign('listdb',$listdb);
        
        return $this->fetch();
    }
    
    /**
     * 发件箱,已发送的消息
     * @return mixed|string
     */
    public function sendbox()
    {
        $map = [
                'uid'=>$this->user['uid']
        ];
        $data_list = Model::where($map)->order("id desc")->paginate(15);
        $data_list->each(function($rs,$key){
            $rs['to_username'] = get_user_name($rs['touid']);
            return $rs;
        });
            $pages = $data_list->render();
            $listdb = getArray($data_list)['data'];
            
            //给模板赋值变量
            $this->assign('pages',$pages);
            $this->assign('listdb',$listdb);
            
            return $this->fetch();
    }
    
    /**
     * 删除信息
     * @param unknown $id
     */
    public function delete($id)
    {
        $info = getArray(Model::where(['id'=>$id])->find());
        if(!$info){
            return '内容不存在';
        }elseif($info['uid']!=$this->user['uid']&&$info['touid']!=$this->user['uid']){
            return '你无权删除';
        }elseif($info['uid']==$this->user['uid']&&$info['ifread']){
            return '你无权删除对方已读消息';
        }
        
        if (Model::where(['id'=>$id])->delete()) {
            $this->success('删除成功','index');
        }else{
            $this->error('删除失败');
        }
    }
    
    /**
     * 发送消息
     * @param string $username
     * @param number $uid
     * @return mixed|string
     */
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
                $content = $this->user['username'] . ' 给你发了一条私信,请尽快查收,<a href="'.get_url(urls('member/msg/show',['id'=>$result->id])).'">点击查收</a>';
                send_wx_msg($info['weixin_api'], $content);
                $this->success('发送成功','index');
            }else{
                $this->error('发送失败');
            }
        }
        
        $linkman = Model::where(['touid'=>$this->user['uid']])->group('uid')->column('uid');
        
        if($uid){
            $username = get_user($uid)['username'];
        }
        $this->assign('username',$username);
        $this->assign('linkman',$linkman);
        return $this->fetch();
    }
    
    /**
     * 查看收到的消息
     * @param number $id
     * @return mixed|string
     */
    public function show($id=0)
    {
        $info = $this->get_info($id);
        if(!is_array($info)){
            $this->error($info);
        }      
        $this->assign('info',$info);
        $this->assign('id',$id);
        return $this->fetch();
    }
    
    /**
     * 查看发送出的消息
     * @param number $id
     * @return mixed|string
     */
    public function showsend($id=0)
    {
        $info = $this->get_info($id);
        if(!is_array($info)){
            $this->error($info);
        }
        $this->assign('info',$info);
        $this->assign('id',$id);
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
