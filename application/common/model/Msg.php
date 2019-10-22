<?php
namespace app\common\model;

use think\Model;
use think\Db;

/**
 * 站内短消息
 * @package app\admin\model
 */
class Msg extends Model
{
    //protected $table = '__MEMBERDATA__';
	
	//主键不是ID,要单独指定
	//public $pk = 'id';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    protected static $map;


    public static function add($data=[],$admin=null){
        $info = Friend::where('uid',$data['touid'])->where('suid',$data['uid'])->find();
        if ($info['type']==-1) {
            return ['errmsg'=>'对方把你列入了黑名单,因此无法给他发消息'];
        }
        if (empty($admin) && empty($info)) {
            if (cache('pm_msg_'.$data['uid'])) {
                return ['errmsg'=>'请不要那么频繁的发送消息'];
            }
            cache('pm_msg_'.$data['uid'],time(),5);
        }
        $result = parent::create($data);
        if($data['uid']>0 && $data['touid']>0){
            if (empty($info)) {
                Friend::add($data['uid'],$data['touid']);    //给用户发消息,就相当于成为他的粉丝
            }else{
                if ($info['type']!=2) {
                    Friend::where('id',$info['id'])->update(['type'=>2]);   //把对方也设置为双向好友
                }
                $res = Friend::where('suid',$data['touid'])->where('uid',$data['uid'])->update(['type'=>2,'update_time'=>time()]);
                if (empty($res)) {   //如果自己当中没有对方的资料,就要新建一条
                    Friend::create([
                        'suid'=>$data['touid'],
                        'uid'=>$data['uid'],
                        'type'=>2
                    ]);
                }
            }            
        }
        if ($result) {
            return $result->id;
        }        
    }
    
    /**
     * 查找往来消息
     * @param number $myuid 当前登录用户UID
     * @param number $uid 他人UID 负数代表圈子ID
     * @param number $id 内容详情ID
     * @param number $rows 取几条
     * @param number $maxid 指定大于某个ID的内容
     * @return array|NULL[]|unknown
     */
    public static function list_moremsg($myuid=0,$uid=0,$id=0,$rows=10,$maxid=0){
        $rows<1 && $rows=10;
        if($uid>0){
            cache('msg_time_'.$myuid.'-'.$uid,time(),60);  //把自己的操作时间做个标志
            $from_time = cache('msg_time_'.$uid.'-'.$myuid); //查看对方给自己的最后操作时间
        }elseif($uid<0){    //负数代表圈子ID
            $from_time = 0;
            $c_array = cache('msg_time_'.$uid)?:[];
            unset($c_array[$myuid]);
            $from_time = end($c_array);
            $c_array[$myuid] = time();
            cache('msg_time_'.$uid,$c_array,10);
        }
        
        if($uid<0){
            self::$map['a'] = [];
            self::$map['b'] = [
                'qun_id'=>abs($uid),
            ];
            if($maxid>0){
                self::$map['b']['id'] = ['>',$maxid];
            }
        }else{
            self::$map['a'] = [
                'touid'=>$myuid,
                'uid'=>$uid,
                'id'=>['<=',$id],
            ];
            
            self::$map['b'] = [
                'uid'=>$myuid,
                'touid'=>$uid,
                'id'=>['<=',$id],
            ];
            if (empty($id)) {
                unset(self::$map['a']['id'],self::$map['b']['id']);
            }
            if($maxid>0){
                self::$map['a']['id'] = ['>',$maxid];
                self::$map['b']['id'] = ['>',$maxid];
            }
        }
        
        $data_list = self::where(function($query){
                $query->where(self::$map['a']);
            })->whereOr(function($query){
                $query->where(self::$map['b']);
            })->order("id desc")->paginate($rows);
        
        $array = getArray($data_list);
        foreach($array['data'] AS $key=>$rs){
            if($rs['id']>$maxid){
                $maxid = $rs['id'];
                if($rs['qun_id']>0){
                    $qs = self::where(['qun_id'=>$rs['qun_id'],'uid'=>$myuid])->order('id desc')->find();
                    if($qs){
                        self::update(['id'=>$qs['id'],'visit_time'=>time()]);  //标志最后收到圈子群聊信息的时间
                    }
                }
            }
            
            //$rs['content'] = str_replace(["\n",' '],['<br>','&nbsp;'],filtrate($rs['content']));
            $rs['content'] = self::format_content($rs['content']);
            $rs['content'] = fun("content@bbscode",$rs['content']);
            $rs['from_username'] = get_user_name($rs['uid']);
            $rs['from_icon'] = get_user_icon($rs['uid']);
            $rs['full_time'] = strtotime($rs['create_time']);
            $rs['time'] = format_time($rs['full_time'],true);
            if($rs['ifread']==0&&$rs['touid']==$myuid){
                self::update(['id'=>$rs['id'],'ifread'=>1]);
            }
            $array['data'][$key] = $rs;
        }
        $array['lasttime'] = time()-$from_time; //对方最近操作的时间
        $array['maxid'] = $maxid;
        return $array;
    }
    
    
    /**
     * 获取消息用户列表
     * @param number $uid 当前登录用户的UID
     * @param number $rows
     * @param number $page
     */
    public static function get_listuser($uid=0,$rows=0,$page=0){
        $page>0 || $page=1;
        $min = ($page-1)*$rows;
        $subQuery = self::where('touid',$uid)->whereOr('uid',$uid)
        ->field('uid,touid,create_time,title,id,ifread,qun_id,visit_time,update_time')
        //->order('id desc')
        ->order('update_time desc,visit_time desc,id desc')
        ->limit(5000)   //理论上某个用户的短消息不应该超过5千条。
        ->buildSql();
        
        $listdb = Db::table($subQuery.' a')
        ->field('uid,touid,create_time,title,id,qun_id,visit_time,update_time,count(id) AS num,sum(ifread) AS old_num,(qun_id*1000000 + uid + touid + uid * uid + touid * touid) AS MX')
        ->group('MX')
        //->order('id','desc')
        ->order('update_time','desc')
        ->limit($min,$rows)
        ->select();
        
        foreach($listdb AS $key=>$rs){
            $rs['new_num'] = 0;
            if($rs['qun_id']>0){
                $rs['f_uid'] = -$rs['qun_id'];
                $rs['title'] = '圈子群聊';
                $rs['qun'] = [];
                $rs['new_num'] = self::where([
                    'qun_id'=>$rs['qun_id'],
                    'create_time'=>['>',$rs['visit_time']],
                ])->count('id');
                if($rs['new_num']>0){
                    $qs = getArray(self::where('qun_id',$rs['qun_id'])->order('id desc')->find());
                    $qs['content'] = get_word(del_html($qs['content']), 100);
                    $rs['id'] = $qs['id'];
                    $qs['username'] = get_user_name($qs['uid']);
                    $rs['qun'] = $qs;
                }else{
                    $rs['num'] = self::where('qun_id',$rs['qun_id'])->count('id');
                }
                $quninfo = fun('qun@getByid',$rs['qun_id']);
                $rs['f_name'] = $quninfo['title'];
                $rs['f_icon'] = $quninfo['picurl'];
            }else{
                if($rs['uid']==$uid){
                    $rs['f_uid'] = $rs['touid'];
                    $rs['title'] = '对方还未回复...';
                }else{
                    $rs['f_uid'] = $rs['uid'];
                }
                if($rs['num']!=$rs['old_num']){ //无法确认是哪一方的未读消息,所以要进一步查询
                    $rs['new_num'] = self::where('touid',$uid)->where('uid',$rs['f_uid'])->where('ifread',0)->count('id');
                }
                if ($rs['f_uid']!=0) {
                    $rs['f_name'] = get_user_name($rs['f_uid']);
                    $rs['f_icon'] = get_user_icon($rs['f_uid']);
                }else{
                    $rs['f_name'] = '系统消息';
                }      
            }
             
            $rs['create_time'] = date('Y-m-d H:i',$rs['create_time']);
            $listdb[$key] = $rs;
        }
        return $listdb;
    }
    
    /**
     * 解析网址可以点击打开
     * @param string $content
     * @return mixed
     */
    private static function format_content($content=''){
        if(strstr($content,"<") && strstr($content,">")){    //如果是网页源代码的话，就不解晰了。
            return $content;
        }
        $content = filtrate($content);
        $content = preg_replace_callback("/(http|https):\/\/([\w\?&\.\/=-]+)/", array(self,'format_url'), $content);
        $content = str_replace(["\n"],['<br>'],$content);
        return $content;
    }
    
    private static function format_url($array=[]){
        return '<a href="'.$array[0].'" target="_blank">'.$array[0].'</a>';
    }
	
}