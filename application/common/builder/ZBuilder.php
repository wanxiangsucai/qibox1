<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: CaiWeiMing
// +----------------------------------------------------------------------

namespace app\common\builder;

use app\common\controller\Base;
use think\Exception;

/**
 * 构建器
 * @package app\common\builder
 */
abstract class ZBuilder extends Base
{
    /**
     * @var object 对象实例
     */
    protected static $instance;
    
    /**
     * @var array 构建器数组
     */
    protected static $builder = [];
    
    /**
     * @var string 模板路径
     */
    protected static $_template = '';

    /**
     * @var array 模板变量
     */
    protected $_vars;
    
    /**
     * @var string 插件名称
     */
    protected $_plugin_name = '';
        
    /**
     * 初始化
     */
    public function _initialize()
    {
        $detail = $this->request->dispatch();
        if($detail['module'][1]=='plugin'&&$detail['module'][2]=='execute'){
            $plugin_name = input('param.plugin_name');
            self::setPluginName($plugin_name);
        }        
        parent::_initialize();
	}
    
	/**
	 * 设置插件名称（此方法只供制作插件时用）
	 * @param string $plugin_name 插件名
	 * @return $this
	 */
	public function setPluginName($plugin_name = '')
	{
	    if ($plugin_name != '') {
	        $this->_plugin_name = $plugin_name;
	    }
	    return $this;
	}
	
	abstract protected function setClientLayout($type);

	public static function make($client_type='admin')
    {
       if (is_null(self::$instance)) {
           self::$instance = new static();
           self::$instance->setClientLayout($client_type);
           self::$instance->assign('full_builder_layout', config('full_builder_layout'));
       }
       return self::$instance;
    }
    
    public function addVars( $var= [])
    {
        $this->_vars= array_merge($this->_vars,$var);
        return $this;
    }
    
    /**
     * 设置模版路径
     * @param string $template 模板路径
     * @return $this
     */
    public function setTemplate($template = '')
    {
        if ($template != '') {
            $this->_template = $template;
        }
        return $this;
    }
}
