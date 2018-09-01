<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\traits\AddEditList;
use app\common\model\Module as ModuleModel; 
use app\common\traits\Market;
use app\common\model\Hook_plugin AS HP_Model;

class Module extends AdminBase
{
	
    use AddEditList,Market;
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext;
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new ModuleModel();
		$this->tab_ext = [
		        'page_title'=>'模块管理',
		        'top_button'=>[
		                [
		                        'title'=>'安装本地新模块',
		                        'url'=>url('add'),
		                        'icon'  => 'fa fa-plus-circle',
		                        'class' => '',
		                ],
		                [
		                        'title'=>'安装应用市场模块',
		                        'url'=>url('market'),
		                        'icon'  => 'fa fa-cloud-download',
		                        'class' => '',
		                ],
		        ],
		];
		$this->list_items = [
		        ['icon', '图标', 'icon'],
				['name', '频道名称', 'link',iurl('__keywords__/index/index',[],true,false,'m'),'target'],
				//['pre', '数据表前缀', 'text'],
				['keywords', '关键字(目录名)', 'text'],
				['list', '排序', 'text'],
		        ['ifsys', '顶部菜单', 'yesno'],
				['ifopen', '开放与否', 'yesno'],
		        ['version', '版本更新', 'callback',function($value=''){
		            list($time) = explode("\t",$value);
		            return $time;
		        }],
		        ['right_button', '操作', 'callback',function($value,$rs){
		            return ($rs['type']==0?'':'<a title="复制当前模块" icon="fa fa-copy" class="btn btn-xs btn-default" href="'.url('copy',['id'=>$rs['id']]).'" target="_self"><i class="fa fa-copy"></i></a>').$value;
		        },'__data__'],
			];		
	}
	
	/**
	 * 列出所有频道
	 * @return unknown|mixed|string
	 */
	public function index() {
	    if ($this->request->isPost()) {
	        return $this->edit_order();
	    }
	    if(!table_field('module','version_id')){    //升级数据库
	        into_sql(APP_PATH.'common/upgrade/6.sql',true,0);
	    }
	    return $this -> getAdminTable(self :: getListData($map = [], $order = [],$rows=50));
	}
	
	
	/**
	 * 安装本地现成的模块
	 * @param string $keywords
	 * @return mixed|string
	 */
	public function add($keywords=''){
	    if ($keywords!='') {
	        $result = $this->install($keywords,'m');
	        if ($result!==true) {
	            $this->error($result);
	        }else{
	            $this->success('模块安装成功,请设置一下后台权限',url('group/admin_power',['id'=>$this->user['groupid']]));
	        }	        
	    }
	    
	    $this->tab_ext['right_button'] = [['type'=>'delete']];
	    $this->list_items = [
	            ['icon', '图标', 'icon'],
	            ['name', '系统名称', 'text'],
	            //['pre', '数据表前缀', 'text'],
	            ['keywords', '关键字(目录名)', 'text'],
	            ['right_button', '安装模块', 'callback',function($value,$rs){
	                return '<a title="安装当前模块" icon="fa fa-plus" class="btn btn-xs btn-default" href="'.url('add',['keywords'=>$rs['keywords']]).'" target="_self"><i class="fa fa-plus"></i></a>';
	            },'__data__'],
        ];
	    $array = modules_config();
	    foreach($array AS $key=>$rs){
	        $exists_modules[$rs['keywords']] = $rs;
	    }
	    
	    $dir = opendir(APP_PATH);
	    while ($file = readdir($dir)){
	        if($file!='.' && $file!='..' && is_dir(APP_PATH.$file) && is_file(APP_PATH."$file/install/info.php") && empty($exists_modules[$file])){
	            $modules[] = include APP_PATH."$file/install/info.php";
	        }
	    }
	    return $this -> getAdminTable($modules);
	}
	

	
	/**
	 * 应用市场
	 */
	public function market($id=0,$page=0){	    
// 	    $listdb = cache('market_modules');
// 	    if(empty($listdb)){
// 	        $str = file_get_contents('https://card.wxyxpt.com/api/module.php');
// 	        $listdb = json_decode($str,true);
// 	        cache('market_modules',$listdb,600);
// 	    }
	    
	    //执行安装云端模块
	    if($id){
	        return $this->getapp($id);
	    }
	    
	    //$this->assign('listdb',$listdb);
	    $this->assign('fid',1);	
	    return $this->fetch( );
	}
	
	
	/**
	 * 卸载模块
	 * @param number $ids
	 */
	public function delete($ids=0){
	    $result = $this->uninstall($ids);
	    if ($result!==true){
	        $this->error($result);
	    }else{
	        $this->success('卸载成功');
	    }
	}
	
	/**
	 * 复制频道
	 * @param number $id
	 * @return mixed|string
	 */
	public function copy($id=0){
	    if (empty($id)) $this->error('缺少参数');
	    
	    $info = $this->getInfoData($id);
	    
	    if ($this->request->isPost()) {	        
	        $data = $this->request->post();
	        if(!preg_match('/^[\w]+$/', $data['keywords'])){
	            $this->error($data['keywords'].'关键字只能字母或数字或下画线');
	        }
	        $str = 'abstract,and,array,as,break,callable,case,catch,class,clone,php,const,continue,declare,default,die,do,echo,else,elseif,empty,enddeclare,endfor,endforeach,endif,endswitch,endwhile,eval,exit,extends,final,finally,for,foreach,function,global,goto,if,implements,include,instanceof,interface,isset,list,namespace,new,print,private,protected,public,require,return,static,switch,throw,trait,try,unset,use,var,while,xor,yield,insteadof';
	        if(in_array($data['keywords'],explode(',',$str))){
	            $this->error('请换一个目录名,该目录名是PHP关键字');
	        }
	        $reuslt = $this->copy_mod($info,$data);
	        if($reuslt!==true){
	            $this->error($reuslt);
	        }else{
	            $this->success('模块复制成功,请设置一下后台权限',url('group/admin_power',['id'=>$this->user['groupid']]));
	        }	        
	    }
	    
	    $this->form_items = [
	            ['text','name', '新模块名称','', $info['name'].'-XXX'],
	            ['text','keywords', '新模块存放目录', '只能字母或数字',$info['keywords'].'xxx'],
	    ];
	    return $this->addContent();
	}
	
	/**
	 * 修改模块设置
	 * @param unknown $id
	 * @return mixed|string
	 */
	public function edit($id = null)
	{
	    if (empty($id)) $this->error('缺少参数');
	    
	    $info = $this->getInfoData($id);
	    
	    if ($this -> request -> isPost()) {
	        if ($this -> saveEditContent()) {
	            
	            //钩子要对应的跟着关闭或启用
	            $data = $this -> request -> post();
	            //HP_Model::where('plugin_key',$info['keywords'])->update(['ifopen'=>$data['ifopen']]);
	            HP_Model::where('hook_class','like',"app\\\\".$info['keywords']."\\\%")->update(['ifopen'=>$data['ifopen']]);
	            cache('hook_plugins', NULL);
	            
	            if($info['ifsys']!=input('ifsys')){
	                $this -> success('你更改过菜单位置,需要重新设置权限', url('group/admin_power',['id'=>3]));
	            }else{
	                $this -> success('修改成功','index');
	            }	            
	        } else {
	            $this -> error('修改失败');
	        }
	    }
	    
	    $this->form_items = [
	            ['text','name', '模块名称', ''],
	            ['static','keywords', '所在目录', ''],	      
	            ['radio','ifsys', '使用顶部菜单', '',['禁用','启用']],
	            ['radio','ifopen', '启用或禁用', '',['关闭系统','启用系统']],
	            ['textarea','about', '介绍', ''],
	            ['number','list', '排序值', ''],
	            ['icon','icon', '图标', ''],
	    ];
	    
	    return $this->editContent($info);
	}

	

	
	
}
