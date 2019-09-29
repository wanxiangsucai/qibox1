<?php
namespace app\member\controller;

use app\common\model\Msg AS Model;
use app\common\controller\MemberBase;
use think\Db;

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
     * 标签调用活跃圈子
     * @param array $config
     */
    public function listqun($config=[])
    {
        $cfg = unserialize($config['cfg']);
        $rows = intval($cfg['rows']) ?: 10;
        $page = input('page')>1?input('page'):1;
        $min = ($page-1)*$rows;

        
        $listdb = Model::where('qun_id','>',0)
        ->field('uid,create_time,content,id,qun_id,count(id) AS num')
        ->group('qun_id')
        ->order('id','desc')
        ->paginate($rows);
        
        $listdb->each(function(&$rs,$key){
            $rs['create_time'] = date('Y-m-d H:i',$rs['create_time']);
            $rs['qun'] = fun('qun@getByid',$rs['qun_id']);
            $listdb[$key] = $rs;
        });
//         $array['data'] = $listdb;
//         $array['s_data'] = $array['data'];
//         $array['total'] = '';
        return $listdb;
    }
    
    /**
     * 标签调用，调用类似微信那样的用户列表。
     * @param array $config
     */
    public function listuser($config=[])
    {
        $cfg = unserialize($config['cfg']);
        $rows = intval($cfg['rows']) ?: 10;
        $page = input('page')>1?input('page'):1;
        $min = ($page-1)*$rows;

        $subQuery = Model::where('touid',$this->user['uid'])->whereOr('uid',$this->user['uid'])
        ->field('uid,touid,create_time,title,id,ifread,qun_id,visit_time')
        ->order('id desc')
        ->limit(5000)   //理论上某个用户的短消息不应该超过5千条。
        ->buildSql();
        
        $listdb = Db::table($subQuery.' a')
        ->field('uid,touid,create_time,title,id,qun_id,visit_time,count(id) AS num,sum(ifread) AS old_num,(qun_id*1000000+(uid + touid + ABS( cast(uid AS signed) - cast(touid AS signed) ))/2) AS MX')
        ->group('MX')
        ->order('id','desc')
        ->limit($min,$rows)
        ->select();
        
        foreach($listdb AS $key=>$rs){
            $rs['new_num'] = 0;
            if($rs['qun_id']>0){
                $rs['f_uid'] = -$rs['qun_id'];
                $rs['title'] = '圈子群聊';
                $rs['qun'] = [];
                $rs['new_num'] = Model::where([
                    'qun_id'=>$rs['qun_id'],
                    'create_time'=>['>',$rs['visit_time']],
                ])->count('id');
                if($rs['new_num']>0){
                    $qs = Model::where('qun_id',$rs['qun_id'])->order('id desc')->find();
                    $rs['id'] = $qs['id'];
                    $rs['qun'] = $qs;
                }else{
                    $rs['num'] = Model::where('qun_id',$rs['qun_id'])->count('id');
                }
            }else{
                if($rs['uid']==$this->user['uid']){
                    $rs['f_uid'] = $rs['touid'];
                    $rs['title'] = '对方还未回复...';
                }else{
                    $rs['f_uid'] = $rs['uid'];
                }
                if($rs['num']!=$rs['old_num']){ //无法确认是哪一方的未读消息,所以要进一步查询
                    $rs['new_num'] = Model::where('touid',$this->user['uid'])->where('uid',$rs['f_uid'])->where('ifread',0)->count('id');
                }
            }
            
            $rs['create_time'] = date('Y-m-d H:i',$rs['create_time']);
            $listdb[$key] = $rs;
        }
        $array['data'] = $listdb;
        $array['s_data'] = $array['data'];
        $array['total'] = '';
        return $array;
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
        $uid = intval($cfg['uid']);
        $maxid = intval($cfg['maxid']);
//         $time = $cfg['time'];
//         if($cfg['num']>0){
//             $time = get_cookie('msg_time');
//         }
        
        if($id){
            $info = $this->get_info($id);
            if(!is_array($info)){
                return ;
            }
            $uid = $info['uid'];        }
        if (empty($uid) && !is_numeric($cfg['uid']) && empty($id)) {
            return [];
        }
        
        if($uid>0){
            cache('msg_time_'.$this->user['uid'].'-'.$uid,time(),60);  //把自己的操作时间做个标志            
            $from_time = cache('msg_time_'.$uid.'-'.$this->user['uid']); //查看对方给自己的最后操作时间
        }elseif($uid<0){
            $from_time = 0;
            $c_array = cache('msg_time_'.$uid)?:[];
            unset($c_array[$this->user['uid']]);
            $from_time = end($c_array);
            $c_array[$this->user['uid']] = time();
            cache('msg_time_'.$uid,$c_array,10);
        }
        
        if($uid<0){
            $this->OrMap = [];
            $this->map = [
                'qun_id'=>abs($uid),
            ];
            if($maxid>0){
                $this->map['id'] = ['>',$maxid];
            }
        }else{
            $this->map = [
                'touid'=>$this->user['uid'],
                'uid'=>$uid,
                'id'=>['<=',$id],
            ];
            
            $this->OrMap = [
                'uid'=>$this->user['uid'],
                'touid'=>$uid,
                'id'=>['<=',$id],
            ];
            if (empty($id)) {
                unset($this->map['id'],$this->OrMap['id']);
            }
            if(isset($cfg['maxid'])){
                $this->map['id'] = ['>',$maxid];
                $this->OrMap['id'] = ['>',$maxid];
            }
//             elseif($time>0){
//                 $this->map['create_time'] = ['>',$time];
//                 $this->OrMap['create_time'] = ['>',$time];
//             }
        }
        
        
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
        
//         $this->cktime = true;
        $array = getArray($data_list);
        foreach($array['data'] AS $key=>$rs){
//             $create_time = strtotime($rs['create_time']);
//             if($create_time>get_cookie('msg_time')){
//                 set_cookie('msg_time',$create_time);
//             }
            if($rs['id']>$maxid){
                $maxid = $rs['id'];
                if($rs['qun_id']>0){
                    $qs = Model::where(['qun_id'=>$rs['qun_id'],'uid'=>$this->user['uid']])->order('id desc')->find();
                    if($qs){
                        Model::update(['id'=>$qs['id'],'visit_time'=>time()]);  //标志最后收到圈子群聊信息的时间
                    }
                }
            }
            
            //$rs['content'] = str_replace(["\n",' '],['<br>','&nbsp;'],filtrate($rs['content']));
            $rs['from_username'] = get_user_name($rs['uid']);
            $rs['from_icon'] = get_user_icon($rs['uid']);
            $rs['content'] = $this->format_content($rs['content']);
            if($rs['ifread']==0&&$rs['touid']==$this->user['uid']){
                Model::update(['id'=>$rs['id'],'ifread'=>1]);
            }
            $array['data'][$key] = $rs;
        }
        $array['lasttime'] = time()-$from_time; //对方最近操作的时间
        $array['maxid'] = $maxid;
        return $array;
    }
    
    /**
     * 解析网址可以点击打开
     * @param string $content
     * @return mixed
     */
    private function format_content($content=''){
        if(strstr($content,"<") && strstr($content,">")){    //如果是网页源代码的话，就不解晰了。
            return $content;
        }
        $content = str_replace(["\n",' '],['<br>','&nbsp;'],filtrate($content));
        $content = preg_replace_callback("/(http|https):\/\/([\w\?&\.\/=-]+)/", array($this,'format_url'), $content);
        return $content;
    }
    
    private function format_url($array=[]){
        return '<a href="'.$array[0].'" target="_blank">'.$array[0].'</a>';
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
            $data['content'] = str_replace(["\n",' '],['<br>','&nbsp;'],filtrate($data['content']));
            $result = Model::add($data,$this->admin);
            if(is_numeric($result)){
                $content = $this->user['username'] . ' 给你发了一条私信,请尽快查收,<a href="'.get_url(urls('member/msg/show',['id'=>$result])).'">点击查收</a>';
                send_wx_msg($info['weixin_api'], $content);
                $this->success('发送成功','index');
            }elseif($result['errmsg']){
                return $this->error($result['errmsg']);
            }else{
                $this->error('发送失败');
            }
        }
        
        $linkman = Model::where(['touid'=>$this->user['uid']])->group('uid')->column('uid');
        
        if($uid){
            $username = get_user($uid)['username'];
        }
        $this->assign('touid',$uid);
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
		//$info['content'] = str_replace(["\n",' '],['<br>','&nbsp;'],filtrate($info['content']));
        $this->assign('info',$info);
        $this->assign('touid',$info['uid']);
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
		//$info['content'] = str_replace(["\n",' '],['<br>','&nbsp;'],filtrate($info['content']));
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
