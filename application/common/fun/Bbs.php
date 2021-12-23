<?php
namespace app\common\fun;
use think\Db;

class Bbs{
    
    /**
     * 获取我的相关商品
     * @return array|mixed|PDOStatement|string|boolean|number
     */
    public static function myshop(){
        if (!modules_config('appstore')) {
            return [];
        }
        $aids = Db::name('appstore_buyuser')->where(['uid'=>login_user('uid')])->field('id,aid')->group('aid')->order('id','desc')->column('id,aid');
        $listdb = Db::name('appstore_content')->where('id','in',$aids)->column('id,fid,mid,title');
        foreach ($listdb AS $key=>$rs){
            if (!$rs['title']) {
                $rs['title'] = Db::name('appstore_content'.$rs['mid'])->where('id',$rs['id'])->value('title');
            }
            $listdb[$key] = $rs;
        }
        return $listdb;
    }
    
    /**
     * 兼容旧模板,使用缓存,提高效率
     * @param number $id
     * @param string $content
     * @return string
     */
    public static function content_cache($id=0,$content=''){
        static $array = [];
        if ($content!='') {
            $array[$id] = $content;
        }else{
            return $array[$id];
        }        
    }
    
    /**
     * 获取贴子内容
     * @param number $id
     * @param number $leng
     * @return unknown|string
     */
    public static function getContents($id=0,$leng=0){
        $contents = self::content_cache($id) ?: query('bbs_contents')->where('id',$id)->value('content');
        self::content_cache($id,$contents);
        if ($leng>0) {
            $contents = get_word(del_html($contents),$leng);
        }
        return $contents;
    }
    
    /**
     * 获取回复
     * @param number $id
     * @param unknown $rows
     */
    public static function getReply($id=0,$rows=5){
        $listdb = query('bbs_reply')->where('aid',$id)->limit($rows)->order('id desc')->select();
        return $listdb;
    }
    
    /**
     * 统计某个会员的发贴数，删除与未审核的不统计
     * @param number $uid 用户UID
     * @param string $type 为空则是所有，topic 只统计主题  reply只统计回复
     * @return number|string
     */
    public static function mytotal($uid=0,$type=''){
        $num = 0;
        $map = [
            'status'=>['>',0],
            'uid'=>$uid,
        ];
        if(!$type || $type=='topic'){
            $num += Db::name('bbs_content')->where($map)->count('id');
        }
        if(!$type || $type=='reply'){
            $num += Db::name('bbs_reply')->where($map)->count('id');
        }
        return $num;
    }
    
    
}