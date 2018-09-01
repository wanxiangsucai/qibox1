<?php
namespace app\common\fun;
use think\Db;

class Qun{
    
    public function getByid($id,$time=3600){
        static $array = [];
        $info = $array[$id];
        if (empty($info)) {
            $info = getArray( query('qun_content1')->where('id',$id)->find() );
            $info['url'] = iurl("qun/content/show",['id'=>$info['id']]);
            $info['picurl'] = tempdir($info['picurl']);
            $array[$id] = $info;
        }
        return $info;
    }
    
    /**
     * 用户加入过的群
     * @return array|\think\Collection|\think\db\false|PDOStatement|string
     */
    public static function myjoin(){
        if (!is_dir(APP_PATH.'qun')) {
            return [];
        }
        $uid = login_user('uid');
		if(empty($uid)){
			return [];
		}
        $listdb = Db::name('qun_member')->alias('A')->join('qun_content1 B','A.aid=B.id','left')->field('B.*')->where('A.uid='.$uid)->select();
        return $listdb;
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