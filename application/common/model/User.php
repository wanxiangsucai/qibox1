<?php
namespace app\common\model;

use think\Model;
use think\Db;


class User extends Model
{
    //protected static $passport_table = 'members';   //整合论坛的话，就要写上论坛的数据表前缀
	
    // 设置当前模型对应的完整数据表名称memberdata
    protected $table = '__MEMBERDATA__';
	
	//主键不是ID,要单独指定
	public $pk = 'uid';

    // 自动写入时间戳
    protected $autoWriteTimestamp = false;


    public static function getByName($name = '')
    {
        $result = self::get(['username' => $name]);
        return is_object($result) ? $result->toArray() : $result;
    }
	
	public static function getById($id = '')
    {
        $result = self::get(['uid' => $id]);
        return is_object($result) ? $result->toArray() : $result;
    }
	
	
	//仅获取用户通行证的帐号密码信息
    public static function get_passport($value,$type='uid') {
//         if(config('webdb.passport_type')){
//             return Db::table(self::$passport_table)->where($type=='uid'?'uid':'username',$value)->find();
//         }else{
//             return Db::name(self::$passport_table)->where($type=='uid'?'uid':'username',$value)->find();
//         }        
        $array = getArray(self::where($type=='uid'?'uid':'username',$value)->find());
        return $array;
	}
	
	/**
	 * 仅获取用户详细信息
	 * @param unknown $value
	 * @param string $type
	 * @return \app\common\model\User|NULL
	 */
	public static function get_info($value,$type='uid'){
	    if(is_array($value)){
	        $map = $value;
	    }elseif($type=='name'){
	        $map['username'] = $value;
	    }elseif(preg_match('/^[\w]+$/', $type)){
	        $map[$type] = $value;
	    }
	    $result = self::get($map);
	    return is_object($result) ? $result->toArray() : $result;
	}
	
	//获取用户所有信息
	public static function get_allInfo($value,$type='uid'){

	    $array1 = self::get_passport($value,$type);
		if(!$array1){
			return ;
		}
		
		$array2 = self::get_info($value,$type);
		if(!empty($array2)){
		    $array1 = array_merge($array1,$array2);
		}else{
		    //论坛过来的用户，自动注册一个帐号
			$array = array(
				'uid'=>$array1['uid'],
				'username'=>$array1['username'],
				'email'=>$array1['email'],
				'yz'=>1,
			);
			self::register_data($array);
			//add_user($array1[uid],$webdb[regmoney],'注册得分');
			$array1['yz']=1;
		}
		return $array1;
	}
	
	//检查密码是否正确
	public static function check_password($username,$password,$ckmd5=false,$type='username'){
	    $rs = self::get_passport($username,$type=='username'?'username':'uid');
		if(!$rs){
			return 0;
		}
		if(defined("UC_CONNECT")){
			if(md5(md5($password).$rs['salt'])==$rs['password']){
				return $rs;
			}
		}else{
			if($ckmd5 && strlen($password)==32 && $password==$rs['password'] ){
				return $rs;
			}elseif(md5($password)==$rs['password']){
				return $rs;
			}
		}
		return -1;
	}
	
	//检查用户名是否合法
	public static function check_username($username) {
		$guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
		$len = strlen($username);
		if($len > 50 || $len < 3 || preg_match("/\s+|^c:\\con\\con|[%,\*\'\"\s\<\>\&]|$guestexp/is", $username)) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	//检查用户名是否存在
	public static function check_userexists($username) {
		return self::get_passport($username,'username');
	}

	//检查邮箱是否存在
	public static function check_emailexists($value) {
		
// 		if(config('webdb.passport_type')){
// 		    $rs = Db::name(self::$passport_table)->where('email',$value)->find();
// 		}else{
// 		    $rs = self::get(['email'=>$value]);
// 		}	
	    $rs = self::get(['email'=>$value]);
		return $rs;
	}
	
	//用户注册
	public static function register_user($array){
	    
	    if(self::get_passport($array['username'],'username')){
	        return '当前用户已经存在了';
	    }
	    if(config('webdb.forbidRegName')!=''){
	        $detail = str_array(config('webdb.forbidRegName'));
	        if(in_array($array['username'], $detail)){
	            return '请换一个用户名,当前用户名不允许使用';
	        }
	    }
	    if(!$array['username']){
	        return '用户名不能为空';
	    }elseif(!$array['email']){
	        return '邮箱不能为空';
	    }elseif(!$array['password']){
	        return '密码不能为空';
	    }elseif(strlen($array['username'])>40||strlen($array['username'])<3){
	        return '用户名不能小于3个字节或大于40个字节';
	    }elseif (strlen($array['password'])>30 || strlen($array['password'])<5){
	        return '密码不能小于5个字符或大于30个字符';
	    }elseif(!preg_match("/^[-a-zA-Z0-9_\.]+\@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$/",$array['email'])){
	        return '邮箱不符合规则';
	    }elseif( config('webdb.emailOnly') && self::check_emailexists($array['email'])){
	        return "当前邮箱“{$array['email']}”已被注册了,请更换一个邮箱!";
	    }
	    $S_key=array('|',' ','',"'",'"','/','*',',','~',';','<','>','$',"\\","\r","\t","\n","`","!","?","%","^");
	    
	    //后来增加
	    $array['username'] = str_replace(array('|',' ','',"'",'"','/','*',',','~',';','<','>','$',"\\","\r","\t","\n","`","!","?","%","^"),'',$array['username']);
	    
	    foreach($S_key as $value){
	        if (strpos($array['username'],$value)!==false){
	            //write_file(ROOT_PATH."/cache/name.txt","$array[username]\r\n",'a');
	            return "用户名中包含有禁止的符号“{$value}”";
	        }
	        if (strpos($array['password'],$value)!==false){
	            return "密码中包含有禁止的符号“{$value}”";
	        }
	    }
	    
	    foreach($array AS $key=>$value){
	        $array[$key] = filtrate($value);
	    }
	    hook_listen('user_add_begin',$array);
// 	    $array['uid'] = self::register_passport($array);
// 	    if(!is_numeric($array['uid'])){
// 	        return "主表创建用户失败";
// 	    }
	    if(($array['uid'] = self::register_data($array))==false){
	        return "创建用户失败";
	    }
	    hook_listen('user_add_end',$array);
	    return $array['uid'];
	}
	
	//注册用户通行证,帐号与密码是必须信息
	public static function register_passport($array) {

// 		if(preg_match("/^pwbbs/",config('webdb.passport_type'))){

// 			$data = [
// 			        'password'=>md5($array['password']),
// 			        'username'=>$array['username'],
// 			        'password'=>$array['password'],
// 			        'email'=>$array['email'],
// 			        'groupid'=>-1,
// 			        'memberid'=>8,
// 			        'regdate'=>time(),
// 			        'yz'=>1,
// 			        'lastvisit'=>time(),
// 			        'onlineip'=>'',
// 			];
// 			if( !$uid = Db::name(self::$passport_table)->insertGetId($data) ){
// 			    showerr( '论坛创建用户失败，主表无法写入！');
// 			}
// 			$data['uid'] = $uid;
			
// 			if (!$result = Db::table(config('webdb.passport_pre'))->insert($data)) {
// 			    showerr( '论坛创建用户失败，副表无法写入！');
// 			}
// 		}elseif(defined("UC_CONNECT")){
// 			$uid = uc_user_register($array['username'], $array['password'], $array['email']);
// 			if($uid=='-1'){
// 				showerr('用户名不合法');
// 			}elseif($uid=='-2'){
// 				showerr('包含不允许注册的词语');
// 			}elseif($uid=='-3'){
// 				showerr('用户名已经存在');
// 			}elseif($uid=='-4'){
// 				showerr('email 格式有误');
// 			}elseif($uid=='-5'){
// 				showerr('email 不允许注册');
// 			}elseif($uid=='-6'){
// 				showerr('该 email 已经被注册');
// 			}
// 			//if($uid&&eregi("^dzbbs7",$webdb['passport_type'])){ //DZ论坛相关用户表
// 				//$this->db->query("INSERT INTO {$webdb[passport_pre]}memberfields SET uid='$uid'");
// 				//$pwd=md5($array[password]);
// 				//$this->db->query("INSERT INTO {$webdb[passport_pre]}members SET uid='$uid',username='$array[username]',password='$pwd',groupid=10,regip='$onlineip',regdate='$timestamp',email='$array[email]',newsletter='1',timeoffset='9999',editormode=2,customshow=26");
// 			//}
// 		}else{
// 			$data = [
// 			        'password'=>md5($array['password']),
// 			        'username'=>$array['username'],
// 			];
// 			if( !$uid = Db::name(self::$passport_table)->insertGetId($data) ){			   
// 			    showerr( '创建用户失败，主表无法写入！');
// 			}
// 		}
		
// 		return $uid;
	}
	
	//注册用户详细信息
	public static function register_data($array){

		//if(!$array['uid']||!$array['username']){
	    if($array['username']==''){
			return false;
		}
		$array['groupid'] || $array['groupid']=8;
		isset($array['yz']) || $array['yz']=1;
		$array['regdate'] = time();
		$array['lastvist'] = time();
		$array['regip'] = get_ip();
		$array['lastip'] = get_ip();
        
		//用户昵称
		$array['nickname'] = $array['username'];
		$array['password'] = md5 ($array['password']);

		if($result = self::create($array)){		
		    return $result->uid;
		}
		return false;
	}
	
	//修改用户任意信息
	public static function edit_user($array) {
        
	    cache('user_'.$array['uid'],null);
	    
	    hook_listen('user_edit_begin',$array);
	    
		//self::edit_passport($array);
		
	    if( config('webdb.emailOnly') && $array['email'] ){
	        $r = self::check_emailexists($array['email']);
	        if($r && $r['uid']!=$array['uid']){
	            return "当前邮箱存在了,请更换一个!";
	        }
	    }
	    if($array['password']){
	        $array['password'] = md5($array['password']);
	    }
		
		if(self::update($array)){
		    cache('user_'.$array['uid'],null);
		    hook_listen('user_edit_end',$array);
		    return true;
		}else{
		    return false;
		}
	}

	//仅修改通行证邮箱与密码
	public static function edit_passport($array) {

		if( config('webdb.emailOnly') && $array['email'] ){
			$r = self::check_emailexists($array['email']);
			if($r && $r['uid']!=$array['uid']){				
				showerr("当前邮箱存在了,请更换一个!");
			}
		}
		if($array['password']){
		    $array['password'] = md5($array['password']);
		}

// 		if(preg_match("/^pwbbs/",config('webdb.passport_type'))){
// 			if($array['password']){
// 				$array['password'] = md5($array['password']);
// 			}
			
// 			if (Db::name(self::$passport_table)->update($array)) {
// 			    return true;
// 			} else {
// 			    return false;
// 			}
			
// 		}elseif(defined("UC_CONNECT")){
// 			$rs = uc_user_edit($array['username'] , '' , $array['password'] , $array['email'] , 1 );
// 			return $rs;
// 		}else{
// 			if($array['password']){
// 				$array['password'] = md5($array['password']);
// 				if (Db::name(self::$passport_table)->update($array)) {
// 				    return true;
// 				} else {
// 				    return false;
// 				}
// 			}			
// 		}

		
		if (self::update($array)) {
		    return true;
		} else {
		    return false;
		}
	}
	
	//删除会员
	public static function delete_user($uid) {
	    hook_listen('user_delete_begin',$uid);
// 		if(preg_match("/^pwbbs/",config('webdb.passport_type'))){		    
// 		    Db::name(self::$passport_table)->delete($uid);
// 		    Db::table(config('webdb.passport_pre').'memberdata')->delete($uid);
// 		}elseif(defined("UC_CONNECT")){
// 			//uc_user_delete($uid);
// 		}else{
// 		    Db::name(self::$passport_table)->delete($uid);
// 		}
		if(self::destroy($uid)){
		    cache('user_'.$uid,null);
		    hook_listen('user_delete_end',$uid);
		    return true;
		}
	}
	
	//获取会员总数
	public static function total_num($sql = '') {
	    $rs = Db::query('SELECT COUNT(*) AS NUM FROM '.config('database.prefix').'memberdata '.$sql);
		return $rs['NUM'];
	}
	
	//获取一批会员资料信息
	public static function get_list($start, $num, $sql) {
	    return Db::query('SELECT * FROM '.config('database.prefix').'memberdata '." $sql LIMIT $start, $num");
	}
	
	
	//用户登录
	public static function login($username,$password,$cookietime=null,$not_pwd=false,$type='username'){
	    if(!table_field('memberdata','password_rand')){    //升级数据库
	        into_sql(APP_PATH.'common/upgrade/5.sql');
	    }
	    $array = [
	            'username'=>$username,
	            'password'=>$password,
	            'time'=>$cookietime,
	            'not_pwd'=>$not_pwd,
	            'type'=>$type,
	    ];
	    hook_listen('user_login_begin', $array);
	    if($username==''){
            return 0;
        }
		if($not_pwd){	//不需要知道原始密码就能登录
		    $rs = self::get_passport($username,$type=='username'?'username':'uid');
		}else{
		    $rs = self::check_password($username,$password);
			if(!is_array($rs)){
				return $rs;		//0为用户不存在,-1为密码不正确
			}
			$data = [
			        'uid'=>$rs['uid'],
			        'lastvist'=>time(),
			        'lastip'=>get_ip(),
			];
			self::edit_user($data);
		}
// 		if(preg_match("/^pwbbs/",config('webdb.passport_type'))){
// 		    if(!empty($db_ifsafecv)){
// 		        $_r = self::get_passport($username,$type=='username'?'name':'uid');
// 				$safecv = $_r['safecv'];
// 			}
// 			//set_cookie(CookiePre().'_winduser',StrCode($rs['uid']."\t".PwdCode($rs['password'])."\t$safecv"),$cookietime);
// 			//set_cookie('lastvisit','',0);			
// 		}else{
			set_cookie("passport","{$rs['uid']}\t$username\t".mymd5($rs['password'],'EN'),$cookietime);
//		}
// 		if(defined("UC_CONNECT")){
// 			global $uc_login_code;
// 			//$uc_login_code=uc_user_synlogin($rs['uid']);
// 		}
		$array = [
		        'uid'=>$rs['uid'],
		        'username'=>$username,
		        'password'=>$password,
		        'time'=>$cookietime,
		        'not_pwd'=>$not_pwd,
		        'type'=>$type,
		];
		hook_listen('user_login_end', $array);
		return $rs['uid'];
	}
	
	//用户退出
	public static function quit($uid=0){

// 		if( preg_match("/^pwbbs/",config('webdb.passport_type')) ){
// 			//set_cookie(CookiePre().'_winduser','');
// 		}else{
			set_cookie('passport','');
// 		}
		cache('user_'.$uid,null);
		set_cookie('token_secret','');
		setcookie('adminID','',0,'/');	//同步后台退出
		if(defined('UC_CONNECT')){
			//global $uc_login_code;
			//$uc_login_code = uc_user_synlogout();
		}
		hook_listen('user_quit_end',$uid);
	}
	
	public static  function get_token(){
	    $token = input('token');
	    if($token && cache($token)){   //APP或小程序
	        list($uid,$username,$password) = explode("\t",cache($token));
	        if($uid&&$username&&$password){
	            return ['uid'=>$uid,'username'=>$username,'password'=>$password];
	        }
	    }
	    
	    list($uid,$username,$password) = explode("\t",get_cookie('passport'));
	    if($uid&&$username&&$password){
	        return ['uid'=>$uid,'username'=>$username,'password'=>$password];
	    }
	}
	
	//用户登录状态的信息
	public static function login_info(){
        
	    if(!$token=self::get_token()){
	        return ;
	    }
	    
	    $usr_info = cache('user_'.$token['uid']);
	    if(empty($usr_info['password'])){
	        $usr_info = self::get_allInfo(intval($token['uid']));
	        cache('user_'.$usr_info['uid'],$usr_info,3600);
	    }
	    if( mymd5($usr_info['password'],'EN') != $token['password'] ){
	        self::quit($usr_info['uid']);
			return ;
		}
		return $usr_info;
	}

	//检查微信openid是否存在
	public static function check_wxIdExists($openid) {
		return self::get(['weixin_api'=>$openid]);
	}
	
	//检查微信openid是否存在
	public static function check_qqIdExists($openid) {
	    return self::get(['qq_api'=>$openid]);
	}
	
	//检查小程序openid是否存在
	public static function check_wxappIdExists($openid) {
	    return self::get(['wxapp_api'=>$openid]);
	}
	
	
	/**
	 * 会员标签调用数据
	 * @param unknown $tagArray
	 * @param number $page
	 * @return string
	 */
	public static function labelGet($tagArray , $page=0)
	{
	    $map = [];
	    $cfg = unserialize($tagArray['cfg']);
	    $cfg['rows'] || $cfg['rows'] = 10;
	    $cfg['order'] || $cfg['order'] = 'uid';
	    $cfg['by'] || $cfg['by'] = 'desc';
	    
	    $page = intval($page);
	    if ($page<1) {
	        $page=1;
	    }
	    $min = ($page-1)*$cfg['rows'];
	    
	    if($cfg['where']){  //用户自定义的查询语句
	        $_array = label_format_where($cfg['where']);
	        if($_array){
	            $map = array_merge($map,$_array);
	        }
	    }
	    $whereor = [];
	    if($cfg['whereor']){  //用户自定义的查询语句
	        $_array = label_format_where($cfg['whereor']);
	        if($_array){
	            $whereor = $_array;
	        }
	    }
	    
	    //         $array = User::where($map)->whereOr($whereor)->order($cfg['order'],$cfg['by'])->limit($min,$cfg['rows'])->column(true);
	    //         foreach ($array AS $key=>$rs){
	    //             $rs['title'] = $rs['username'];
	    //             $rs['full_lastvist'] = $rs['lastvist'];
	    //             $rs['lastvist'] = date('Y-m-d H:i',$rs['lastvist']);
	    //             $rs['full_regdate'] = $rs['regdate'];
	    //             $rs['regdate'] = date('Y-m-d H:i',$rs['regdate']);
	    //             $rs['icon'] = $rs['picurl'] = tempdir($rs['icon']);
	    //             $rs['url'] = get_url('user',['uid'=>$rs['uid']]);
	    //             $array[$key] = $rs;
	    //         }
	    
	    $array = self::where($map)->whereOr($whereor)->order($cfg['order'],$cfg['by'])->limit($min,$cfg['rows'])->paginate($cfg['rows'],false,['page'=>$page]);
	    $array->each(function($rs,$key){
	        $rs['title'] = $rs['username'];
	        $rs['full_lastvist'] = $rs['lastvist'];
	        $rs['lastvist'] = date('Y-m-d H:i',$rs['lastvist']);
	        $rs['full_regdate'] = $rs['regdate'];
	        $rs['regdate'] = date('Y-m-d H:i',$rs['regdate']);
	        $rs['icon'] = $rs['picurl'] = tempdir($rs['icon']);
	        $rs['url'] = get_url('user',['uid'=>$rs['uid']]);
	        $rs['group_name'] = getGroupByid($rs['groupid']);
	        return $rs;
	    });
	        return $array;
	}
	
	
}