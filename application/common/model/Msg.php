<?php
namespace app\common\model;

use think\Model;

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