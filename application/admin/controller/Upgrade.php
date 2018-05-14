<?php
namespace app\admin\controller;

use app\common\controller\AdminBase; 


class Upgrade extends AdminBase
{
    public function _initialize(){
        parent::_initialize();
        if(config('client_upgrade_edition')==''){
            config('client_upgrade_edition',RUNTIME_PATH . '/client_upgrade_edition.php');
        }
    }
	
	public function index()
    {
        $array = @include(config('client_upgrade_edition'));
        $this->assign('upgrade',$array);
		return $this->fetch('index');
	}
	
	/**
	 * 更新升级日志
	 * @param string $upgrade_edition
	 * @return boolean|string
	 */
	private function writelog($upgrade_edition=''){
	    if( file_put_contents(config('client_upgrade_edition'), '<?php return ["md5"=>"'.$upgrade_edition.'","time"=>"'.date('Y-m-d H:i').'",];') ){
	        return true;
	    }else{
	        return '权限不足,日志写入失败';
	    }
	}
	
	/**
	 * 更新前,先备份一下文件
	 * @param string $filename
	 */
	private function bakfile($filename=''){
	    $bakpath = RUNTIME_PATH.'bakfile/';
	    if(!is_dir($bakpath)){
	        mkdir($bakpath);
	    }
	    $new_name = $bakpath.date('Y_m_d-H_i--').str_replace(['/','\\'], '--', $filename);
	    copy(ROOT_PATH.$filename,$new_name);
	}
	
	/**
	 * 升级数据库
	 * @param string $filename
	 */
	private function up_sql($filename=''){
	    if(preg_match('/^\/application\/common\/upgrade\/([\w]+)\.sql/', $filename)){
	        into_sql(ROOT_PATH.$filename,true,0);
	    }
	}
	
	/**
	 * 执行更多复杂的逻辑性的升级
	 * @param string $filename
	 */
	private function up_run($filename=''){
	    if(preg_match('/^\/application\/common\/upgrade\/([\w]+)\.php/', $filename)){
	        $classname = "app\\common\\upgrade\\".ucfirst(substr(basename($filename), 0,-4));
	        if( class_exists($classname) && method_exists($classname, 'up') ){
	            $obj = new $classname;
	            try {
	                $obj->up();
	            } catch(\Exception $e) {
	                //echo $e;
	            }
	        }
	    }
	}
	
	/**
	 * 开始升级,一个一个的文件升级
	 */
	public function sysup($filename='',$upgrade_edition=''){
	    if($upgrade_edition){  //升级完毕,写入升级信息日志
	        $result = $this->writelog($upgrade_edition);
	        if( $result===true ){
	            return $this->ok_js([],'升级成功');
	        }else{
	            return $this->err_js($result);
	        }
	    }
	    
	    if($filename==''){
	        return $this->err_js('文件不存在!');
	    }
	    
	    $str = $this->get_server_file($filename);
	    if($str){
	        $this->bakfile($filename);
	        makepath(dirname(ROOT_PATH.$filename));    //检查并生成目录
	        if( file_put_contents(ROOT_PATH.$filename, $str) ){
	            $this->up_sql($filename);
	            $this->up_run($filename);
	            return $this->ok_js([],'文件升级成功');
	        }else{
	            return $this->err_js('目录权限不足,文件写入失败');
	        }	        
	    }else{
	        return $this->err_js('获取云端数据失败,请晚点再偿试');
	    }
	}
	
	public function view_file($filename=''){
	    $str = $this->get_server_file($filename);
	    echo '<textarea style="width:100%;height:100%;">'.$str.'</textarea>';
	    die();
	}
	
	/**
	 * 核对需要升级文件
	 * @return void|\think\response\Json
	 */
	public function check_files($upgrade_edition=''){
	    set_time_limit(0); //防止超时
	    $array = $this->getfile();
	    if(empty($array)){
	        return $this->err_js('获取云端数据失败,请晚点再偿试');
	    }
	    $data = [];
	    foreach($array AS $rs){
	        $file = ROOT_PATH.$rs['file'];
	        if(is_file($file.'.lock')){
	            continue;  //用户不想升级的文件
	        }
	        if(!is_file($file) || md5_file($file)!=$rs['md5']){
	            $data[]=[
	                    'file'=>$rs['file'],
	                    'ctime'=>is_file($file)?date('Y-m-d H:i',filemtime($file)):'缺失的文件',
	                    'time'=>date('Y-m-d H:i',$rs['time']),
	            ];
	        }
	    }
	    if($data){
	        return $this->ok_js($data);
	    }else{
	        $upgrade_edition && $reustl = $this->writelog($upgrade_edition);
	        return $this->err_js($reustl!==true?$reustl:'没有可更新文件');
	    }
	}
	
	protected function get_server_file($filename){
	    $str = http_curl('https://x1.php168.com/appstore/Upgrade/make_client_file.html?filename='.$filename.'&domain='.$this->request->domain());
	    if(substr($str,0,2)=='QB'){    //文件核对,防止网络故障,抓取一些出错信息
	        $str = substr($str,2);
	    }else{
	        $str='';
	    }
	    return $str;
	}
	
	protected function getfile(){error_reporting(7);
	   $str = http_curl('https://x1.php168.com/appstore/Upgrade/get_list_file.html?domain='.$this->request->domain());
	    return $str ? json_decode($str,true) : '';
	}

}
?>