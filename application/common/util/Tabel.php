<?php
namespace app\common\util;
use app\common\builder\Table as BuilderTable;
use app\common\traits\AddEditList;

class Tabel{
    
    use AddEditList;
    public static $Btable;
    protected static $instance;
    protected static $listdb=[];   //列表数据
    protected static $f_array=[];   //字段信息
    
    /**
     * 创建显示列表的table表格
     * @param array $data_list 从数据库取出的列表数据
     * @param array $tab_list 表格参数
     * 比如 [   ['title','标题','text']  , ['mid','模型名称','select2','',[1=>'文章',2=>'图片'] ] ]
	 * 第三项常用类型 比如 text select select2 text.edit
	 * 第四项是默认值，第五项是参数，常用为数组
     */
    public static function make($data_list=[],$tab_list=[],$right_button='',$top_button_delete=true){
        self::$listdb = $data_list;     //纯属给个性模板使用的
        self::$f_array = $tab_list;     //纯属给个性模板使用的
        self::$Btable = BuilderTable::make()->addColumns($tab_list)->setRowList($data_list);

	    if(!empty($right_button)){
	        //self::$Btable->addRightButtons($right_button);
	    }
	    if(!empty($delete_all)){
	        //self::$Btable->addRightButtons($right_button);
	    }
	    
	    if (is_null(self::$instance)) {
	        self::$instance = new static();
	    }
	    return self::$instance;
	}
	
	/**
     * 添加一列
     * @param string $name 字段变量名称
     * @param string $title 列标题
     * @param string $type 数据类型 比如 text select select2 text.edit
     * @param string $default 默认值
     * @param string $param 额外参数，常用为数组
     * @param string $class css类名
     * @return $this
     */
	public static function addList($name = '', $title = '', $type = '', $default = '', $param = '', $class = ''){
	    self::$Btable->addColumn($name, $title, $type, $default, $param, $class);
	    return self::$instance;
	}
	
	/**
	 * 一次性添加多列
	 * @param array $array 
	 * 比如 [['title','标题','text'],['mid','模型名称','select2','',[1=>'文章',2=>'图片']]],]
	 * 第三项常用类型 比如 text select select2 text.edit
	 * 第四项是默认值，第五项是参数，常用为数组
	 * @return $this
	 */
	public static function addLists($array=[]){
	    self::$Btable->addColumns($array);
	    return self::$instance;
	}
	
	/**
	 * 设置分组导航列表
	 * @param array $tab_list Tab列表  [1=>['title' => '标题A', 'url' => 'http://xxxx.com'],2=>['title' => '标题B', 'url' => 'http://xxxx.com']]
	 * @param string $curr_id 当前分组ID
	 * @return $this
	 */
	public static function addNav($tab_list = [], $curr_id = ''){
	    self::$Btable->setTabNav($tab_list, $curr_id );
	    return self::$instance;
	}
	
	/**
	 * 设置页面提示
	 * @param string $msg 提示信息
	 * @param string $type 提示类型：success/info/warning/danger，默认info
	 * @return $this
	 */
	public static function addPageTips($msg = '', $type = 'info'){
	    self::$Btable->setPageTips($msg , $type );
	    return self::$instance;
	}
	
	/**
	 * 设置页面标题
	 * @param string $title 页面标题
	 * @return $this
	 */
	public static function addPageTitle($title = ''){
	    self::$Btable->setPageTitle($title);
	    return self::$instance;
	}
	
	/**
	 * 添加一个顶部按钮
	 * @param string $type 按钮类型：add/enable/disable/back/delete/custom
	 * @param array $attribute 按钮样式属性
	 * @param bool $blank 是否使用弹出新窗口
	 * @return $this
	 */
	public static function addTopButton($type = '', $attribute = [], $blank = false){
	    self::$Btable->addTopButton($type, $attribute, $blank);
	    return self::$instance;
	}
	
	/**
	 * 添加一个右侧按钮
	 * @param string $type 按钮类型：edit/enable/disable/delete/custom
	 * @param array $attribute 按钮属性
	 * 例如 ['title' => '添加','icon' => 'fa fa-plus', 'data-tips' => '删除后无法恢复。','class' => 'btn btn-'.config('zbuilder.right_button')['size'].' btn-'.config('zbuilder.right_button')['style'], 'href' => url('add', ['id' => '__id__']),]
	 * 
	 * @param bool $blank 是否使用弹出新窗口
	 * @return $this
	 */
	public static function addRightButton($type = '', $attribute = [], $blank = false){
	    if($type=='add'){
	        $attribute = array_merge(
	                ['title' => '添加','icon' => 'fa fa-plus', 'data-tips' => '删除后无法恢复。','class' => 'btn btn-'.config('zbuilder.right_button')['size'].' btn-'.config('zbuilder.right_button')['style'], 'href' => url('add', ['id' => '__id__']),],
	                $attribute
	                );
	    }
	    self::$Btable->addRightButton($type, $attribute, $blank);
	    return self::$instance;
	}
	
	/**
	 * 一次性添加多个右侧按钮
	 * @param array|string $buttons 按钮类型
	 * 例如： 'edit' 或 'edit,delete' 或 ['edit', 'delete'] 或 ['delete','edit' => ['title' => '修改']]
	 * @return $this
	 */
	public static function addRightButtons($button_array = []){
	    self::$Btable->addRightButtons($button_array);
	    return self::$instance;
	}
	
	/**
	 * 添加表头排序
	 * @param array|string $column 表头排序字段，多个以逗号隔开
	 * @return $this
	 */
	public static function addOrder($column=[]){
	    self::$Btable->addOrder($column);
	    return self::$instance;
	}
	
	/**
     * 设置搜索参数
     * @param array $fields 参与搜索的字段
     * @param string $placeholder 提示符
     * @param string $url 提交地址
     * @param null $search_button 提交按钮
     * @return $this
     */
	public static function addSearch($fields = [], $placeholder = '', $url = '', $search_button = null){
	    self::$Btable->setSearch($fields, $placeholder, $url, $search_button);
	    return self::$instance;
	}
	
	/**
     * 添加表头筛选
     * @param array|string $columns 表头筛选字段，多个以逗号隔开
     * @param array $options 选项，供有些字段值需要另外显示的，比如字段值是数字，但显示的时候是其他文字。
     * @param array $default 默认选项，['字段名' => '字段值,字段值...']
     * @param string $type 筛选类型，默认为CheckBox，也可以是radio
     * @return $this
     */
	public static function addFilter($columns = [], $options = [], $default = [], $type = 'radio'){
	    self::$Btable->addFilter($columns, $options, $default, $type);
	    return self::$instance;
	}
	
	/**
     * 添加表头筛选列表
     * @param string $field 表头筛选字段
     * @param array $list 需要显示的列表
     * @param string $default 默认值，一维数组或逗号隔开的字符串
     * @param string $type 筛选类型，默认为CheckBox，也可以是radio
     * @return $this
     */
	public static function addFilterList($field = '', $list = [], $default = '', $type = 'radio'){
	    self::$Btable->addFilterList($field, $list, $default, $type);
	    return self::$instance;
	}
	
	/**
	 * 加载模板输出
	 * @param string $template 模板文件名
	 * @param array  $vars     模板输出变量
	 * @param array  $replace  模板替换
	 * @param array  $config   模板参数
	 * @return mixed
	 */
	public static function fetch($template = '', $vars = [], $replace = [], $config = []){	    
	    if(empty($template)&&$template = static::get_template('')){    //如果模板存在的话,就用实际的后台个性模板
	        $vars = array_merge($vars,['listdb'=>self::$listdb,'f_array'=>self::$f_array]);
	    }
	    return self::$Btable->fetch($template, $vars, $replace, $config);
	}
	
}