<?php
namespace app\common\fun;
use think\Db;
use app\qun\model\Content AS ContentModel;
/**
 * 圈子
 *
 */
class Qun{
    
    /**
     * 获取用户所在圈子里边的角色 注意: ===null  代表还没加入圈子 ==0 非正式成员,
     * @param number $id
     * @param number $uid
     * @param string $field 获取哪个字段,留空则是所有字段
     * @return void|array|\think\db\false|PDOStatement|string|\think\Model
     */
    public static function get_user_group($id=0,$uid=0,$field='type'){
        if (empty($id)) {
            return ;
        }
        if (empty($uid)) {
            $uid = login_user('uid');
        }
        if (empty($uid)) {
            return ;
        }
        $info = Db::name('qun_member')->where('uid',$uid)->where('aid',$id)->find();
        if (empty($info)) {
            return ;
        }
        if ($info['end_time'] && $info['end_time']<time()) {  //设置了有效期
            $info['type'] = $info['type']>1 ? 1 : 0 ;
        }
        if ($field){
            return $info[$field];
        }else{
            return $info;
        }
    }
    
    /**
     * 获取群的角色名称
     * @param unknown $groupid
     * @return number|string|unknown|array|number[][]|string[][]|unknown[][]|array[][]
     */
    public static function get_group($groupid=null){
        $array = [];
        $i = 0;
        $webdb = config('webdb.M__qun');
        $str = explode("\n", str_replace("\r", '', $webdb['qun_groups']));
        foreach($str AS $value){
            if (empty($value)) {
                continue;
            }
            list($name,$sysgid) = explode("|", $value);
            $i++;
            $admin = 0;
            //1,2,3是保留数字，分别是正式成员、副管理员、管理员
            if ($i==1) {    //管理员
                $gid = 3;
                $admin = 1;
            }elseif($i==2){ //副管理员
                $gid = 2;
                $admin = 1;
            }elseif($i==3){ //正式成员
                $gid = 1;
            }else{
                $gid = $i;
            }
            $array[$gid] = [
                'gid'=>$gid,
                'name'=>$name,
                'sysgid'=>$sysgid,
                'admin'=>$admin,
            ];
        }
        $array[3] || $array[3] = [
            'gid'=>3,
            'name'=>QUN.'创建人',
            'admin'=>1,
        ];
        $array[1] || $array[1] = [
            'gid'=>1,
            'name'=>'正式成员',
            'admin'=>0,
        ];
        $array[0] = [
            'gid'=>0,
            'name'=>'待审成员',
        ];
        if (is_numeric($groupid)) {
            return $array[$groupid]['name'];
        }
        return $array;
    }
    
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
            //$info = getArray( query('qun_content1')->where('id',$id)->find() );
            $info = ContentModel::getInfoByid($id);
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
		//$listdb = Db::name('qun_member')->alias('A')->join('qun_content1 B','A.aid=B.id','left')->field('B.*')->where('A.uid='.$uid)->order('A.id desc')->select();
		$array = Db::name('qun_member')->where('uid',$uid)->where('type','>',0)->order('type desc,update_time desc')->column('aid');
		$listdb = [];
		foreach($array AS $aid){
		    $listdb[] = ContentModel::getInfoByid($aid);
		}
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
        //$listdb = Db::name('qun_visit')->alias('A')->join('qun_content1 B','A.aid=B.id','left')->field('B.*')->where('A.uid='.$uid)->order('A.id desc')->select();
        $array = Db::name('qun_visit')->where('uid',$uid)->order('visittime desc')->column('aid');
        $listdb = [];
        foreach($array AS $aid){
            $listdb[] = ContentModel::getInfoByid($aid);
        }
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
            //$listdb = query('qun_content1')->where('uid',$uid)->order('usernum desc')->column(true);
            //$listdb = array_values($listdb);
            $array = Db::name('qun_content')->where('uid',$uid)->order('id desc')->column('id');
            $listdb = [];
            foreach($array AS $aid){
                $listdb[] = ContentModel::getInfoByid($aid);
            }
            $array[$uid] = $listdb;
        }        
        return $listdb;
    }
    
    /**
     * 获取某个圈子的广告位信息
     * @param number $id
     */
    public static function adsetByid($id=0){
        if (empty($id)) {
            return ;
        }
        return Db::name('qun_adset')->where('aid',$id)->find();
    }
    
    /**
     * 获取某个圈子的广告位状态,什么时候可以轮流到可以显示广告
     * @param number $id
     * @return void|array|\think\db\false|PDOStatement|string|\think\Model
     */
    public static function adset_status($id=0){
        if (empty($id)) {
            return ;
        }
        $info = Db::name('qun_adset')->where('aid',$id)->find();
        if (empty($info)) {
            return ;
        }
        $time = time();
        $end_time = Db::name('qun_aduser')->where('aid',$id)->order('id','desc')->value('end_time');
        if($end_time<$time){
            $end_time = $time;
        }
        $info['time'] = $end_time;
        return $info;
    }
    
    /**
     * 获取广告位内容
     * @param number $id
     * @return void|array[]|\think\db\false[]|PDOStatement[]|string[]|\think\Model[]
     */
    public static function adByid($id=0){
        if (empty($id)) {
            return ;
        }
        $info = Db::name('qun_adset')->where('aid',$id)->find();
        if (empty($info)) { //不存在广告位
            return ;
        }
        $time = time();
        $data = Db::name('qun_aduser')->where('aid',$id)->where('begin_time','<',$time)->where('status',1)->where('end_time','>',$time)->find();
        if($info['status']==0 && empty($data)){ //关闭了广告位购买并且没有可以显示的广告,否则的话,还是要把别人的广告显示完才行的.
            return ;
        }
        return [
                'set'=>$info,
                'ad'=>$data,
        ];
    }
    
    /**
     * 列出所有风格
     * @param string $olny_free 设置为true的话,只列出免费风格 否则是所有风格
     * @return string[]
     */
    public static function list_style($olny_free=false){
        $array = [];
        $template_path = TEMPLATE_PATH."qun_style/";
        $dir=opendir($template_path);
        while( $file=readdir($dir)){
            if($file!='.' && $file!='..' && $file!='.svn' && is_file($template_path.$file.'/info.php')){
                $rs = include($template_path.$file.'/info.php');
                if ($olny_free==true && $rs['money']>0) {
                    continue;
                }
                $rs['keyword'] = $file;
                $rs['picurl'] = config('view_replace_str.__STATIC__').'/qun_style/'.$file.'/demo_min.jpg';
                $rs['demo'] = config('view_replace_str.__STATIC__').'/qun_style/'.$file.'/demo.jpg';
                $array[] = $rs;
            }
        }
        return $array;
    }
    
}