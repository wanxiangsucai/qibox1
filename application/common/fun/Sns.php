<?php
namespace app\common\fun;

class Sns{
    
    /**
     * 把动态里边的每一条记录获取对应的数据
     * @param array $rs
     */
    public function get($rs=[]){
        static $info_array = [];
        if ($info_array[$rs['id']]) {       //同一条记录,避免重复调用此函数,而造成多次查询,另外也可以写入缓存处理
            return $info_array[$rs['id']];
        }
        if(empty($rs['sysid']) && empty($rs['aid'])){
            return ;
        }
        $array = modules_config($rs['sysid']);
        $class = "app\\{$array['keywords']}\\model\\Content";
        static $obj_array = [];
        $obj = $obj_array[$class];
        if(empty($obj)){
            if(class_exists($class)&&method_exists($class, 'getInfoByid')){
                $obj = $obj_array[$class] = new $class;
            }else{
                return ;
            }
        }
        $reply = [];
        $topic = $obj->getInfoByid($rs['aid'],true);
        if($rs['type']=='comment'){
            $reply = query('comment_content',[
                    'where'=>['id'=>$rs['rid']],
            ]);
        }elseif($rs['type']=='reply'){
            $reply = query($array['keywords'].'_reply',[
                    'where'=>['id'=>$rs['rid']],
                    'type'=>'one',
            ]);
        }
        
        $info_array[$rs['id']] = [$topic,$reply];
        return $info_array[$rs['id']];
    }
    
    /**
     * 将动态加入到所有粉丝那里
     * @param number $aid 微博主UID
     * @param array $data 博主动态索引
     * @return boolean
     */
    public function push_toFans($aid=0,$data=[]){
        $listdb = query('weibo_member',[
                'where'=>['aid'=>$aid],
                'column'=>'uid',
        ]);
        $array = [];
        foreach ($listdb AS $_uid){
            $array[] = array_merge($data,['uid'=>$_uid]);
        }
        
        $obj = new \app\weibo\model\Feed;
        $obj->push_all($array);
        return true;
    }
    
    /**
     * 载入某个用户的所有频道的主题
     * @param number $uid 信息来源UID
     * @param number $touid 插入到某个用户
     * @return void|boolean
     */
    public function push_topic($uid=0,$touid=0){
        $touid || $touid=$uid;
        $data = [];
        $array = modules_config();
        foreach($array AS $rs){
            if($rs['keywords']=='weibo'){
                continue;
            }
            $class = "app\\{$rs['keywords']}\\model\\Content";
            if(class_exists($class)&&method_exists($class, 'getIndexByUid')){
                $obj = new $class;                
                $listdb = $obj->getIndexByUid($uid);
                foreach($listdb AS $vs){
                    $create_time = query("{$rs['keywords']}_content{$vs['mid']}",['where'=>['id'=>$vs['id']],'value'=>'create_time']);
                    $data[] = [
                            'aid'=>$vs['id'],
                            'sysid'=>$rs['id'],
                            'uid'=>$touid,
                            'create_time'=>$create_time,
                            'type'=>'add'
                    ];
                }                
            }
        }
        if (empty($data)) {
            return ;
        }
        $obj = new \app\weibo\model\Feed;
        $obj->push_all($data);
        return true;
    }
    
    /**
     * 载入某个用户的所有频道的评论
     * @param number $uid 信息来源UID
     * @param number $touid 插入到某个用户
     * @param number $rows 只取最近多少条
     * @return void|boolean
     */
    public function push_comment($uid=0,$touid=0,$rows=100){
        $touid || $touid=$uid;
        $data = [];
        $listdb = query('comment_content',[
                'where'=>['uid'=>$uid],
                'order'=>'id desc',
                'limit'=>$rows,
                'column'=>'id,sysid,aid,create_time'
        ]);
        foreach($listdb AS $vs){
            $data[] = [
                    'aid'=>$vs['aid'],
                    'sysid'=>$vs['sysid'],
                    'uid'=>$touid,
                    'create_time'=>$vs['create_time'],
                    'type'=>'comment',
                    'rid'=>$vs['id'],
            ];
        }
        if (empty($data)) {
            return ;
        }
        $obj = new \app\weibo\model\Feed;
        $obj->push_all($data);
        return true;
    }
    

    /**
     * 载入某个用户的论坛回复
     * @param number $uid 信息来源UID
     * @param number $touid 插入到某个用户
     * @param number $rows 只取最近多少条
     * @param string $type 数据表类型 默认是论坛频道,也有可能是把论坛复制为其它频道的话,要对应的修改
     * @return void|boolean
     */
    public function push_reply($uid=0,$touid=0,$rows=100,$type='bbs_reply'){
        $touid || $touid=$uid;
        $data = [];
        $listdb = query('bbs_reply',[
                'where'=>['uid'=>$uid],
                'order'=>'id desc',
                'limit'=>$rows,
                'column'=>'id,sysid,aid,create_time'
        ]);
        foreach($listdb AS $vs){
            $data[] = [
                    'aid'=>$vs['aid'],
                    'sysid'=>$vs['sysid'],
                    'uid'=>$touid,
                    'create_time'=>$vs['create_time'],
                    'type'=>'reply',
                    'rid'=>$vs['id'],
            ];
        }
        if (empty($data)) {
            return ;
        }
        $obj = new \app\weibo\model\Feed;
        $obj->push_all($data);
        return true;
    }
    
}