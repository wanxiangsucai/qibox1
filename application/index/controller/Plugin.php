<?php
namespace app\index\controller;

use app\common\controller\IndexBase;

use app\common\traits\AddEditList;

use app\common\model\Plugin as PluginModel;

class Plugin extends IndexBase
{
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
				'help_msg'=>'插件管理',
				];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new PluginModel();
		$this->list_items = [				 
				['name', '插件名称', 'text'],                
				['keywords', '插件关键字', 'text'],
				['ifopen', '启用或停用', 'yesno'],
				['postitme', '添加日期', 'datetime'],
				['list', '排序值', 'text'],
				
			];
	}

    public function add()
    {
    	$this->form_items = [
					['text', 'keywords', '关键字'],
					['text', 'name', '插件名称'],					
				];
        return $this->addContent();
    }
	public function edit($id = null)
	{
	    if (empty($id)) $this->error('缺少参数');
	    
	    $this->form_items = [
	            ['text','name', '插件名称', ''],
	            ['static','keywords', '插件关键字', ''],
	            ['radio','ifopen', '启用或禁用', '',['关闭系统','启用系统']],
	            ['textarea','about', '介绍', ''],
	            ['text','list', '排序值', ''],
	    ];
	    
	    $info = $this->getInfoData($id);
	    
	    return $this->editContent($info);
	}
	/**
	 * 执行插件内部方法
	 * @return mixed
	 */
	public function execute()
	{
	    $plugin     = input('param.plugin_name');
	    $controller = input('param.plugin_controller');
	    $action     = input('param.plugin_action');
	    define('IN_PLUGIN', $plugin .'#' . $controller .'#' . $action);

	    //参数优先级为 POST>GET>ROUTE
	    $get_params = input('get.');
	    $post_params = input('post.');
	    $params     = $this->request->except(['plugin_name', 'plugin_controller', 'plugin_action'], 'route');
	    if($get_params){
	        $params = $params?array_merge($params,$get_params):$get_params;
	    }
	    if($post_params){
	        $params = $params?array_merge($params,$post_params):$post_params;
	    }
	    
	    if (empty($plugin) || empty($controller) || empty($action)) {
	        $this->error('没有指定插件名称、控制器名称或操作名称');
	    }
	    
	    if (!plugin_action_exists($plugin, $controller, $action)) {
	            $this->error("找不到类及方法：plugins\\{$plugin}\\index\\".format_class_name($controller));
	    }
	    return plugin_action($plugin, $controller, $action, $params);
	}

}
