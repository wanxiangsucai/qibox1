<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\model\User AS UserModel;
use app\common\util\Menu;
use think\Controller;
use app\common\model\User;
use think\Db;
use plugins\log\model\Login AS LoginLog;


class Index extends AdminBase
{
    public function index()
    {
        $base_menu = Menu::make('admin'); //Menu::get_menu();
        //菜单权限判断
        if(SUPER_ADMIN!==true){
            $power = (array)getGroupByid($this->user['groupid'],false)['admindb'];  //取得用户的菜单权限
            foreach ($base_menu AS $key1=>$rs1){
                if($key1=='often'){
                    continue;
                }
                foreach($rs1['sons'] AS $key2=>$rs2){
                    foreach ($rs2['sons'] AS $key=>$rs){
                        is_array($rs['link']) && $rs['link'] = $rs['link'][0];
                        if(empty($power["$key1-{$rs['model']}-{$rs['link']}"])){    //权限不存在,就把菜单去除
                            unset($base_menu[$key1]['sons'][$key2]['sons'][$key]);
                        }
                    }
                    if(count($base_menu[$key1]['sons'][$key2]['sons'])==0){ //三级子菜单不存的话,就把二级父菜单也去除
                        unset($base_menu[$key1]['sons'][$key2]);
                    }
                }
                if(count($base_menu[$key1]['sons'])==0){ //频道菜单不存的话,就把头部顶级菜单也去除
                    unset($base_menu[$key1]);
                }
            }
        }
        $this->assign('userdb', $this->user );
		$this->assign('base_menu', $base_menu );

		return $this->fetch();
    }
    
    public function quit()
    {
        if (empty($this->user)) {
            $this->error('你还没登录！','index');
        }
        UserModel::quit($this->user['uid']);
        $this->success('成功退出','index');
    }
	
    public function login()
    {
        if (!empty($this->user)) {
            $this->error('你已经登录了','index');
        }
        if(IS_POST){
            
            $data= get_post('post');
        
            // 验证码
            if ($this->webdb['admin_login_usercode']) {
                $captcha = $data['captcha'];
                $captcha == '' && $this->error('请输入验证码');
                if(!captcha_check($captcha, '', config('captcha'))){
                    //验证失败
                    $this->error('验证码错误或失效');
                };
            }
            
            $result = UserModel::login($data['username'],$data['password']);
            
            if($result==0){
                LoginLog::login($data['username'], $data['password']);
                $this->error("当前用户不存在,请重新输入");
            }elseif($result==-1){
                LoginLog::login($data['username'], $data['password']);
                $this->error("密码不正确,点击重新输入");
            }else{
                LoginLog::login($data['username'],md5($data['password'] . get_ip()));
                $this->success('登录成功','index');
            }
        }
		if(SUPER_ADMIN===true){
			$this->success('登录成功','index');
		}
        return $this->fetch();
    }
    
	public function welcome()
	{
		$map = [];
		$map['uid']  = ['>',0];
        $this->assign('user_num', User::where($map)->count('uid') );
        is_table('cms_content') && $this->assign('cms_num', Db::name('cms_content')->count('id') );
        $this->assign('systemMsg', self::get_system_info());
		return $this->fetch();
	}
	
	public function sysinfo(){
	    if (!function_exists('phpinfo')) {
	        $this->error('phpinfo函数被禁用了!');
	    }
	    phpinfo();
	}
	
	public function leftmenu($type='often')
    {
        $array = Menu::get_menu();
       
        //菜单权限判断
        if(SUPER_ADMIN!==true&&$type!='often'){
            $power = (array)getGroupByid($this->user['groupid'],false)['admindb'];  //取得用户的菜单权限
            foreach ($array[$type]['sons'] AS $key1=>$rs1){
                foreach ($rs1['sons'] AS $key=>$rs){
                    is_array($rs['link']) && $rs['link'] = $rs['link'][0];
                    if(empty($power["$type-{$rs['model']}-{$rs['link']}"])){    //权限不存在,就把菜单去除
                        unset($array[$type]['sons'][$key1]['sons'][$key]);
                    }
                }
                if(count($array[$type]['sons'][$key1]['sons'])==0){ //子菜单不存的话,就把父菜单也去除
                    unset($array[$type]['sons'][$key1]);
                }
            }
        }

		if(empty($array[$type]['sons'])){
			//die('空的');
		}
        
		$this->assign('userdb', $this->user );
		$this->assign('menuArray', $array[$type]['sons'] );
		
		return $this->fetch();
	}
	
	/**
	 * 获取服务器信息
	 * @return string
	 */
	private function get_system_info(){
	    $rs['mysqlVersion'] = query('SELECT VERSION()')[0]['VERSION()'];
	    
	    $rs['ifcookie'] = count($_COOKIE) ? "SUCCESS" : "FAIL";
	    $rs['sysversion'] = PHP_VERSION;	//PHP版本
	    $rs['max_upload']= ini_get('upload_max_filesize') ? ini_get('upload_max_filesize') : 'Disabled';	//最大上传限制
	    $rs['max_ex_time'] = ini_get('max_execution_time').' 秒';	//最大执行时间
	    $rs['sys_mail'] = ini_get('sendmail_path') ? 'Unix Sendmail ( Path: '.ini_get('sendmail_path').')' :( ini_get('SMTP') ? 'SMTP ( Server: '.ini_get('SMTP').')': 'Disabled' );	//邮件支持模式
	    $rs['systemtime'] = date("Y-m-j g:i A");	//服务器所在时间
	    $rs['onlineip'] = get_ip();				//当前IP
	    if( function_exists("imagealphablending") && function_exists("imagecreatefromjpeg") && function_exists("ImageJpeg") ){
	        $rs['gdpic']="支持";
	    }else{
	        $rs['gdpic']="不支持";
	    }
	    $rs['allow_url_fopen'] = ini_get('allow_url_fopen') ? "On 支持采集数据" : "OFF 不支持采集数据";
	    $rs['safe_mode'] = ini_get('safe_mode')?"打开":"关闭";
	    $rs['DOCUMENT_ROOT'] = $_SERVER["DOCUMENT_ROOT"];	//程序所在磁盘物理位置
	    $rs['SERVER_ADDR'] = $_SERVER["SERVER_ADDR"]?$_SERVER["SERVER_ADDR"]:$_SERVER["LOCAL_ADDR"];		//服务器IP
	    $rs['SERVER_PORT']=$_SERVER["SERVER_PORT"];		//服务器端口
	    $rs['SERVER_SOFTWARE'] = $_SERVER["SERVER_SOFTWARE"];	//服务器软件
	    $rs['SCRIPT_FILENAME'] = $_SERVER["SCRIPT_FILENAME"]?$_SERVER["SCRIPT_FILENAME"]:$_SERVER["PATH_TRANSLATED"];//当前文件路径
	    $rs['SERVER_NAME'] = $_SERVER["SERVER_NAME"];	//域名
	    
	    $rs['zendVersion'] = function_exists('Zend_Version') ? Zend_Version() : "未知/可能没安装";
	    $rs['memory_user_limit']=ini_get('memory_limit');    //最大执行时间/空间限制内存
	    $rs['file_uploads'] = ini_get('file_uploads')?"允许":"不允许"; //是否允许上传文件
	    
	    return $rs;
	}

}
