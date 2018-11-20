<?php
namespace app\common\fun;
use think\Db;

class Qun{
    
    /**
     * 统计某个圈子里的图片或商品或贴子的数量
     * @param string $table 统计的数据表,不用加前缀
     * @param number $id 圈子ID
     * @return number|string
     */
    public static function count($table='',$id=0){
        if (preg_match('/^qb_/', $table)) {
            $table = str_replace('qb_', '', $table);
        }
        if (preg_match('/member$/', $table)) {
            $map = ['aid'=>$id];
        }else{
            $map = ['ext_id'=>$id];
        }
        return Db::name($table)->where($map)->count('id');
    }
    
    /**
     * 根据圈子ID获取圈子的信息
     * @param unknown $id
     * @param number $time
     * @return void|string|mixed
     */
    public static function getByid($id,$time=3600){
        static $array = [];
        $info = $array[$id];
        if (empty($info)) {
            $info = getArray( query('qun_content1')->where('id',$id)->find() );
            if (empty($info)) {
                return ;
            }
            $info['url'] = iurl("qun/content/show",['id'=>$info['id']]);
            $info['picurl'] = tempdir($info['picurl']);
            $array[$id] = $info;
        }
        return $info;
    }
    
    /**
     * 某用户加入过的圈子
     * @param number $uid
     * @return array|array|mixed
     */
    public static function myjoin($uid=0){
        if (!modules_config('qun')) {
            return [];
        }
        $uid || $uid = login_user('uid');
		if(empty($uid)){
			return [];
		}
		$listdb = Db::name('qun_member')->alias('A')->join('qun_content1 B','A.aid=B.id','left')->field('B.*')->where('A.uid='.$uid)->order('A.id desc')->select();
        return $listdb;
    }
    
    /**
     * 某用户最近访问过的圈子
     * @param number $uid
     * @return array|array|mixed
     */
    public static function myvisit($uid=0){
        if (!modules_config('qun')) {
            return [];
        }
        $uid || $uid = login_user('uid');
        if(empty($uid)){
            return [];
        }
        $listdb = Db::name('qun_visit')->alias('A')->join('qun_content1 B','A.aid=B.id','left')->field('B.*')->where('A.uid='.$uid)->order('A.id desc')->select();
        return $listdb;
    }
    
    /**
     * 某用户所创建的所有圈子
     * @param number $uid
     * @param number $time
     * @return array|array|mixed
     */
    public static function getByuid($uid=0,$time=3600){
        if (!modules_config('qun')) {
            return [];
        }
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