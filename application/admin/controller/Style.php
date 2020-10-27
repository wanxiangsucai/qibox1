<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\traits\AddEditList;
//use app\common\model\Plugin as PluginModel;
use app\common\traits\Market;
use app\common\model\Market AS MarketModel;

class Style extends AdminBase
{
    use AddEditList,Market;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
				'page_title'=>'风格管理',
				];
	
	/**
	 * 应用市场
	 */
	public function market($id=0,$page=0,$fid=8,$type=''){	    
	    //执行安装或卸载云端模块
	    if($id){
	        if ($type=='delete') {
	            return $this->delete_market_style($id);
	        }else{
	            return $this->get_style($id,'style',$type);
	        }
	        
	    }
	    $this->assign('fid',$fid?:8);	
	    return $this->fetch();
	}
	
	/**
	 * 卸载应用市场的风格
	 * @param number $id
	 * @return void|\think\response\Json|void|unknown|\think\response\Json
	 */
	protected function delete_market_style($id=0){
	    $keywords = input('keywords');
	    $appkey= input('appkey');
	    $domain= input('domain');
	    
	    $info = MarketModel::where('version_id',$id)->find();
	    if (empty($info)){
	        return $this->err_js('你并没有安装当前风格!');
	    }
	    
	    $url = "https://x1.php168.com/appstore/getapp/down.html?id=$id&domain=$domain&appkey=$appkey";
	    $result = $this->delele_model_file($url);
	    if ($result!==true) {
	        return $this->err_js($result);
	    }
	    
	    $data = [
	        'version_id'=>$id,
	    ];
	    $result = MarketModel::where($data)->delete();
	    if ($result){
	        return $this->ok_js([],'当前风格卸载成功!');
	    }else{
	        return $this->err_js('数据库删除失败');
	    }
	}
	
	/**
	 * 下载并安装风格
	 * @param number $id
	 * @param string $sort
	 * @return void|\think\response\Json|void|unknown|\think\response\Json
	 */
	protected function get_style($id=0,$sort='style'){
	    $keywords = input('keywords');
	    $appkey= input('appkey');
	    $domain= input('domain');
	    
	    $basepath = TEMPLATE_PATH;
	    
	    if(!is_writable($basepath)){
	        return $this->err_js($basepath.'目录不可写,请先修改目录属性可写');
	    }elseif ( is_dir($basepath.'index_style/'.$keywords) ){
	        //return $this->err_js($basepath.'index_style/'.$keywords.'目录已经存在了,无法安装此风格');
	    }
	    $url = "https://x1.php168.com/appstore/getapp/down.html?id=$id&domain=$domain&appkey=$appkey";
	    $result = $this->downModel($url,$keywords,$sort);
	    if($result!==true){
	        return $this->err_js($result);
	    }
	    
	    $url = "https://x1.php168.com/appstore/getapp/info.html?id=$id&domain=$domain&appkey=$appkey";
	    if(($str=file_get_contents($url))==false){
	        $str = http_curl($url);
	    }
	    $info = json_decode($str,true);
	    
	    $data = [
	            'type'=>$info['type']?:'',
	            'keywords'=>$keywords,
	            'version_id'=>$id,
	            'name'=>$info['title']?:'',
	            'author'=>$info['author']?:'',
	            'author_url'=>$info['author_url']?:'',
	    ];
	    MarketModel::create($data);
	    
// 	    $result = $this->install($keywords,$type);
// 	    if($result!==true){
// 	        return $this->err_js($result);
// 	    }
	    
	    return $this->ok_js(['url'=>url('setting/index')],'风格安装成功,请在系统设置那里选择启用此风格');

	}
	
}
