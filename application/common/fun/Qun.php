<?php
namespace app\common\fun;

class Qun{
    
    public function getByid($id,$time=3600){
        static $array = [];
        $info = $array[$id];
        if (empty($info)) {
            $info = getArray( query('qun_content1')->where('id',$id)->find() );
            $info['url'] = iurl("qun/content/show",['id'=>$info['id']]);
            $array[$id] = $info;
        }
        return $info;
    }
    
    public function getByuid($uid=0,$time=3600){
        if (empty($uid)) {
            return [];
        }
        static $array = [];
        $listdb = $array[$uid];
        if (empty($listdb)) {
            $listdb = query('qun_content1')->where('uid',$uid)->order('usernum desc')->column(true);
            $listdb = array_values($listdb);
            $array[$uid] = $listdb;
        }        
        return $listdb;
    }
    
}