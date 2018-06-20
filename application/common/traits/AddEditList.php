<?php
namespace app\common\traits;
use app\common\builder\Table;
use app\common\builder\Form;

trait AddEditList {
    
    /**
     * 左上角按钮,示例如下
     
            $array = [
		                [
		                        'title'=>'新增',
		                        'url'=>url('add'),
		                        'icon'  => 'fa fa-plus-circle',
		                        'class' => 'btn btn-primary',
		                ],
		                [
		                        'title'=>'批量删除',
		                        'url'=>url('delete'),
		                        'icon'  => 'fa fa-microchip',
		                        'class' => 'btn btn-danger',
		                ],
		                [
		                        'title'=>'其它',
		                        'url'=>url('info/index'),
		                        'icon'  => 'fa fa-plus-circle',
		                        'class' => 'btn btn-danger',
		                ],
		        ];
		        
     * @param array $array
     */
    protected function page_topButton($array=[]){        
    }
    
    /**
     * 信息列表右边按钮,示例如下
     
     $array = [
     [
     'title'=>'新增',
     'url'=>url('add'),
     'icon'  => 'fa fa-plus-circle',
     'class' => 'btn btn-primary',
     ],
     [
     'title'=>'批量删除',
     'url'=>url('delete'),
     'icon'  => 'fa fa-microchip',
     'class' => 'btn btn-danger',
     ],
     [
     'title'=>'其它',
     'url'=>url('info/index'),
     'icon'  => 'fa fa-plus-circle',
     'class' => 'btn btn-danger',
     ],
     ];
     
     * @param array $array
     */
    protected function page_rightButton($array=[]){
    }
    
    /**
     * 页面标题
     * @param string $title
     */
    protected function page_title($title='内容管理'){        
    }    
    
    /**
     * 列表要显示的字段信息,举例如下:
     
      $array = [
                ['title', '字段名称', 'text'],
                ['name', '字段变量名', 'text'],
                ['type', '表单类型', 'select',config('form')],
                ['list', '排序值', 'text.edit'],
        ];
        
     * @param array $array
     */
    protected function page_list_field($array=[]){        
    }
    
    
    /**
     * 表单中某些字段选中后隐藏或显示另外的字段事件
     
             $array = [
                        ['type', '1,2', 'age'],
              ];
     * @param array $array
     */
    protected function page_form_trigger($array=[]){        
    }
    
    /**
     * 表单页要显示的字段信息,举例如下:

     $array = [
                ['text', 'name', '字段变量名','创建后不能随意修改,否则会影响其它地方的数据调用,只能数字或字母及下画线，但必须要字母开头',"title_".rand(0,100)],
                ['text', 'title', '字段名称'],
                ['select', 'type', '表单字段类型','',config('form'),'text'],
                ['textarea', 'options', '参数选项', '用于单选、多选、下拉等类型'],
                ['text', 'value', '字段默认值'],
                ['text', 'field_type', '数据库字段类型','','varchar(128) NOT NULL'],
                ['radio', 'listshow', '是否在列表显示', '', ['不在列表显示', '显示'], 0],
                ['radio', 'ifsearch', '是否作为内容搜索选项', '', ['否', '是'], 0],
                ['radio', 'ifmust', '是否属于必填项', '', ['可不填', '必填'], 0],
                ['text', 'list', '排序值'],
                ['text', 'nav', '分组名[:对于不重要的字段,你可以添加组名,让他在更多那里显示]'],                
        ];
        
     * @param array $array
     */
    protected function page_form_field($array=[]){        
    }
    
	/**
	 * 表单页填写的字段，参数为true的话，表单里要带上ID，一起提交，好核对要更新哪条主键记录
	 * @param string $isEdit
	 * @return string[]
	 */
	protected function getFormItems($isEdit = false) {
		// 表单页填写的字段
		$tab_list = $this -> form_items;

		if ($isEdit) {
			// 修改的时候，增加一个隐藏ID，如果主键不是ID的话，要特别指定
			$tab_list[] = [
			'hidden',
			empty($this -> model -> pk) ? 'id' : $this -> model -> pk,
			];
		}
		return $tab_list;
	}
	
	/**
	 * 列表页默认显示字段
	 * @return array
	 */
	protected function getListItems() {
		$tab_list = [
		['id', 'ID'],
		]; 
		// 列表页设置 $this->tab_ext['id'] = false;可以隐藏ID这一列，如果主键名不是ID的话，可以重新定义
		if (isset($this -> tab_ext['id'])) {
			if (!empty($this -> tab_ext['id'])) {
				$tab_list = [
				[$this -> tab_ext['id'], 'ID'],
				];
			} else {
				$tab_list = [];
			} 
		}
		
		$tab_list = array_merge($tab_list , $this -> list_items , end($this -> list_items)[0]=='right_button'?[]:[['right_button', '操作', 'btn']]);

		return $tab_list;
	}
	
	/**
	 * 列表要显示的数据
	 * @param array $map 查询条件
	 * @param array $order 排序方式
	 * @param unknown $rows 每页显示多少条
	 * @return unknown
	 */
	protected function getListData($map = [], $order = [],$rows=20) {
		$map = array_merge($this -> getMap(), $map);
		
		$order = $this -> getOrder() ? $this -> getOrder() : $order ;

		$data_list = $this -> model -> where($map) -> order($order) -> paginate($rows);
		return $data_list;
	}
	
	/**
	 * 处理url href 混乱的问题
	 * @param array $array
	 * @return unknown
	 */
	protected function builder_url($array=[]){
	    if ($array) {
	        foreach($array AS $key=>$rs){
	            $rs['url'] = $rs['href'] = $rs['url'] ?: $rs['href'];
	            $array[$key] = $rs;
	        }
	        return $array;
	    }
	}
	
	/**
	 * 自动生成列表页模板,并把数据显示出来
	 * @param array $data_list
	 * @return mixed|string
	 */
	protected function getAdminTable($data_list = []) {
	    
	    $template = $this->get_template('',$this->mid);
	    if (empty($template)) {
	        $template = getTemplate('admin@common/wn_table');
	    }
	    if(!empty($template)){    //如果模板存在的话,就用实际的后台模板
	        $this->tab_ext['top_button'] = $this->builder_url($this->tab_ext['top_button']);
	        $this->tab_ext['top_button'] || $this->tab_ext['top_button'] = [['type'=>'add'],['type'=>'delete']];   //如果没设置顶部菜单 就给两个默认的
	        $this->tab_ext['right_button'] = $this->builder_url($this->tab_ext['right_button']);
	        $pages = is_object($data_list) ? $data_list->render() : '';
	        $array = getArray($data_list);
	        $this->assign('listdb',isset($array['data'])?$array['data']:$array);
	        $this->assign('mid',$this->mid);
	        $this->assign('tab_ext',$this->tab_ext);	
	        $this->assign('f_array',$this->list_items);
	        $this->assign('pages',$pages);
	        return $this->fetch($template);
	    }
	    
		// 显示的字段信息
		$tab_list = $this -> getListItems();

		$list_table = Table :: make() -> addColumns($tab_list) -> setRowList($data_list) -> setTableName('list_all') -> addOrder('id'); 
		// ->addTopButton('back', ['href' => url('advert/index')]) // 批量添加顶部按钮
		// ->addTopButtons('add,enable,disable,delete'); // 批量添加顶部按钮
		if (!empty($this -> model -> pk) && $this -> model -> pk != 'id') {
			$list_table -> setPrimaryKey($this -> model -> pk);
		} 
		// 右上角快速搜索 数组展示
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['search'])) {
			$list_table -> setSearch($this -> tab_ext['search']);
		}
		
		// 添加字段排序
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['order'])) {
		    $list_table -> addOrder($this -> tab_ext['order']);
		}
		
		//字段筛选搜索项
		if (!empty($this -> tab_ext) && !empty($this->tab_ext['filter_search'])) {
		    foreach($this->tab_ext['filter_search'] AS $key=>$value){
		        $list_table -> addFilterList($key, $value,'','radio');
		    }		    
		}
		
		// 分组显示
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['nav'])) {
			$list_table -> setTabNav($this -> tab_ext['nav'][0], $this -> tab_ext['nav'][1]);
		} 
		// 快速编辑内容过滤 第一项是规则，第二项是字段名，多个用，号隔开
		if (!empty($this -> validate) && !empty($this -> tab_ext['validate'])) {
			$list_table -> addValidate($this -> tab_ext['validate'][0], $this -> tab_ext['validate'][1]);
		} 
		// 表格顶部的公告提示
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['help_msg'])) {
			$list_table -> setPageTips($this -> tab_ext['help_msg']);
		} 
		// 页面标题
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['page_title'])) {
			$list_table -> setPageTitle($this -> tab_ext['page_title']);
		} 
		// 表格顶部的菜单
		if (!empty($this -> tab_ext) && isset($this -> tab_ext['top_button'])) {
			if (is_array($this -> tab_ext['top_button'])) {
				foreach($this -> tab_ext['top_button'] AS $ar) {
					$list_table -> addTopButton(
						empty($ar['type']) ? 'custom' : $ar['type'],
						$ar
						);
				} 
			} 
		} else { // 列表页，默认给个添加按钮
			$list_table -> addTopButtons('add');
			$list_table -> addTopButtons('delete');
		} 
		// 表格右边的菜单
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['right_button'])) {
			foreach($this -> tab_ext['right_button'] AS $value) {
				$list_table -> addRightButton('custom', $value);
			} 
		} 
		$right_button = ['edit', 'delete' => ['data-tips' => '删除后无法恢复。']];

		if($this -> tab_ext['hidden_edit']){
			unset($right_button[0]);
		}
		if($this -> tab_ext['hidden_delete']){
			unset($right_button['delete']);
		}


		$list_table -> addRightButtons($right_button); // 添加右侧按钮
		
		return $list_table -> fetch();
	} 
	
	/**
	 * 取模板路径
	 * @param string $type 方法名,也即文件名
	 * @param string $mid 模型ID,可为空
	 * @return string
	 */
	protected static function get_template($type='',$mid=''){
	    if($type==''){
	        if(defined('IN_PLUGIN')){
	           $type = input('param.plugin_action');
	       }else{
	           $type = request()->action();
	       }	        
	    }
	    //当前风格的模板
	    $template = static::search_tpl($type,$mid);
	    
	    if (empty($template)) { //新风格找不到的话,就寻找默认default模板
	        if( config('template.default_view_base') ){ //没有使用默认风格
	            $view_base = config('template.view_base');
	            $style = config('template.index_style');
	            config('template.view_base',config('template.default_view_base'));
	            config('template.index_style','default');   // check_file 此方法要用到
	            $template = static::search_tpl($type,$mid);
	            config('template.view_base',$view_base);
	            config('template.index_style',$style);
	        }
	    }
	    return $template;
	}
	
	/**
	 * 查找路径
	 * @param string $type 方法名,也即文件名
	 * @param string $mid 模型ID,可为空
	 * @return string
	 */
	protected static function search_tpl($type='',$mid=''){
	    $filename = $type.$mid;
	    static $path_array = [];
	    $path = $path_array[config('template.view_base')];
	    if(empty($path)){  //避免反复找路径
	        $path_array[config('template.view_base')] = $path = dirname( makeTemplate('index',false) ).'/'; //取得路径
	    }
	    $file = $path . $filename . '.' . ltrim(config('template.view_suffix'), '.');
	    if(is_file($file)&&filesize($file)){
	        return $file;
	    }elseif($mid!==''){ //寻找母模板
	        $file = $path . $type . '.' . ltrim(config('template.view_suffix'), '.');
	        if(is_file($file)&&filesize($file)){
	            return $file;
	        }
	    }
	}
    
	/**
	 * 辅栏目添加内容
	 * @param array $map
	 * @param array $order
	 * @return mixed|string
	 */
	protected function addCategoryInfo($map = [], $order = []) {
		// 显示的字段信息
		$tab_list = $this -> getListItems(); 
		// 数据内容
		$data_list = $this -> getListData($map, $order);
		
		$template = $this->get_template('',$this->mid);
		if (empty($template)) {
		    $template = getTemplate('admin@common/wn_table');
		}
		if(!empty($template)){    //如果模板存在的话,就用实际的后台模板
		    $this->assign('listdb',$data_list);
		    $this->assign('mid',$this->mid);
		    return $this->fetch($template);
		}

		$list_table = Table :: make() -> addColumns($tab_list) -> setRowList($data_list) -> setTableName('list_all') -> addOrder('id'); 
		// ->addTopButton('back', ['href' => url('advert/index')]) // 批量添加顶部按钮
		// ->addTopButtons('add,enable,disable,delete'); // 批量添加顶部按钮
		if (!empty($this -> model -> pk) && $this -> model -> pk != 'id') {
			$list_table -> setPrimaryKey($this -> model -> pk);
		} 
		// 右上角快速搜索 数组展示
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['search'])) {
			$list_table -> setSearch($this -> tab_ext['search']);
		} 
		// 分组显示
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['nav'])) {
			$list_table -> setTabNav($this -> tab_ext['nav'][0], $this -> tab_ext['nav'][1]);
		} 
		// 快速编辑内容过滤 第一项是规则，第二项是字段名，多个用，号隔开
		if (!empty($this -> validate) && !empty($this -> tab_ext['validate'])) {
			$list_table -> addValidate($this -> tab_ext['validate'][0], $this -> tab_ext['validate'][1]);
		} 
		// 表格顶部的公告提示
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['help_msg'])) {
			$list_table -> setPageTips($this -> tab_ext['help_msg']);
		} 
		// 页面标题
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['page_title'])) {
			$list_table -> setPageTitle($this -> tab_ext['page_title']);
		} 
		// 表格顶部的菜单
		if (!empty($this -> tab_ext) && isset($this -> tab_ext['top_button'])) {
			if (is_array($this -> tab_ext['top_button'])) {
				foreach($this -> tab_ext['top_button'] AS $ar) {
					$list_table -> addTopButton(
						empty($ar['type']) ? 'custom' : $ar['type'],
						$ar
						);
				} 
			} 
		} 

		return $list_table -> fetch();
	} 
	
	/**
	 * 保存新增数据
	 * @return unknown|boolean
	 */
	protected function saveAddContent() {
		// 保存数据
		if ($this -> request -> isPost()) {
			// 表单数据
			$data = $this -> request -> post();

			if (!empty($this -> validate)) {
				// 验证
				$result = $this -> validate($data, $this -> validate);
				if (true !== $result) $this -> error($result);
			} 
			$data['uid'] = $this -> user['uid'];
			$data['posttime'] = $data['create_time'] = time();
			if ($result = $this -> model -> create($data)) {
				return $result; //$result->id 方便其它地方通过这个得到新的ID
			} else {
				return false;
			} 
		} 
	} 
	
	/**
	 * 新发表内容,可以自动生成表单与处理提交的数据
	 * @param string $url
	 * @param array $vars
	 * @return mixed|string
	 */
	protected function addContent($url = 'index', $vars = []) {
		// 保存数据
		if ($this -> request -> isPost()) {
			if ($this -> saveAddContent()) {
				$this -> success('添加成功', $url);
			} else {
				$this -> error('添加失败');
			} 
		}
		
		$template = $this->get_template('',$this->mid);
		if (empty($template)) {
		    $template = getTemplate('admin@common/wn_form');
		}
		if(!empty($template)){    //如果模板存在的话,就用实际的后台模板
		    //$this->assign('listdb',$data_list);
		    $this->assign('mid',$this->mid);
		    $this->assign('f_array',$this -> form_items);
		    $this->assign('tab_ext',$this->tab_ext);	
		    return $this->fetch($template,$vars);
		}
		
		// 要填写的表单字段
		$tab_list = $this -> getFormItems(false);

		$form_table = Form :: make() -> addFormItems($tab_list); 
		// 表格顶部的公告提示
		if (!empty($vars)) {
			$form_table -> addVars($vars);
		}
		
		// 分组导航
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['nav'])) {
		    $form_table -> setTabNav($this -> tab_ext['nav'][0], $this -> tab_ext['nav'][1]);
		}
		
		//分组显示表单
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['group'])) {
		    $form_table -> addGroup($this -> tab_ext['group']);
		}
		
		// 顶部提示信息
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['help_msg'])) {
			$form_table -> setPageTips($this -> tab_ext['help_msg']);
		} 
		// 页面标题
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['page_title'])) {
			$form_table -> setPageTitle($this -> tab_ext['page_title']);
		} 
		
		// 增加按钮
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['addbtn'])) {
		    $form_table -> addBtn($this -> tab_ext['addbtn']);
		}
		
		// 隐藏按钮
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['hidebtn'])) {
		    $form_table -> hideBtn($this -> tab_ext['hidebtn']);
		}
		
		// 引入JS文件
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['js_file'])) {
			$form_table -> js($this -> tab_ext['js_file']);
		} 
		// 页面提醒注意事项信息
		empty($this -> tab_ext['warn_msg']) || $form_table -> setPageTips($this -> tab_ext['warn_msg'], 'warning');

		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['trigger'])) {
			foreach($this -> tab_ext['trigger'] AS $rs) {
				$rs[0] && $rs[2] && $form_table -> setTrigger($rs[0], $rs[1], $rs[2], isset($rs[3])?false:true);
			} 
		} 

		return $form_table -> fetch();
	}
	
	
	/**
	 * 修改时候的原始数据
	 * @param number $id
	 * @return array|unknown|NULL[]
	 */
	protected function getInfoData($id = 0) {
		return getArray( $this -> model -> get($id));
	} 
	
	/**
	 * 保存修改时的数据
	 * @return boolean
	 */
	protected function saveEditContent() {
		// 表单数据
		$data = $this -> request -> post(); 
		// 验证
		if (!empty($this -> validate)) {
			// 验证
			$result = $this -> validate($data, $this -> validate);
			if (true !== $result) $this -> error($result);
		} 

		if ($this -> model -> update($data)) {
			return true;
		} else {
			return false;
		} 
	}
	
	/**
	 * 修改内容 并且自动生成网页模板
	 * @param unknown $info 要修改的内容数据数组
	 * @param string $url 修改成功后跳转的网址
	 * @param string $type 前台还是后台模板
	 * @return mixed|string
	 */
	protected function editContent($info, $url = 'index', $type = 'admin') {
		// 保存数据
		if ($this -> request -> isPost()) {
			if ($this -> saveEditContent()) {
				$this -> success('修改成功', $url);
			} else {
				$this -> error('修改失败');
			} 
		}
		
		$template = $this->get_template('',$this->mid);
		if (empty($template)) {
		    $template = getTemplate('admin@common/wn_form');
		}
		if(!empty($template)){    //如果模板存在的话,就用实际的后台模板
		    $this->assign('info',$info);
		    $this->assign('f_array',$this -> form_items);
		    $this->assign('mid',$this->mid);
		    $this->assign('tab_ext',$this->tab_ext);		    
		    return $this->fetch($template);
		}
		
		// 表单填写项目
		$tab_list = $this -> getFormItems(true);
		$form_table = Form :: make($type) -> addFormItems($tab_list) -> setFormdata($info); 
		
		// 分组导航
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['nav'])) {
			$form_table -> setTabNav($this -> tab_ext['nav'][0], $this -> tab_ext['nav'][1]);
		}
		
		//分组显示表单
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['group'])) {
		    $form_table -> addGroup($this -> tab_ext['group']);
		}
		
		// 页面标题
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['page_title'])) {
		    $form_table -> setPageTitle($this -> tab_ext['page_title']);
		}
		// 引入JS文件
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['js_file'])) {
			$form_table -> js($this -> tab_ext['js_file']);
		} 
		
		// 增加按钮
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['addbtn'])) {
		    $form_table -> addBtn($this -> tab_ext['addbtn']);
		}
		
		// 隐藏按钮
		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['hidebtn'])) {
		    $form_table -> hideBtn($this -> tab_ext['hidebtn']);
		}
		
		// 页面提醒注意事项信息
		empty($this -> tab_ext['warn_msg']) || $form_table -> setPageTips($this -> tab_ext['warn_msg'], 'warning');

		if (!empty($this -> tab_ext) && !empty($this -> tab_ext['trigger'])) {
			foreach($this -> tab_ext['trigger'] AS $rs) {
				$rs[0] && $rs[2] && $form_table -> setTrigger($rs[0], $rs[1], $rs[2], isset($rs[3])?false:true);
			} 
		} 

		return $form_table -> fetch();
	} 
	
	/**
	 * 删除内容 可以用数据传值,同时删除多个
	 * @param unknown $ids
	 * @return boolean
	 */
	protected function deleteContent($ids) {
		if (empty($ids)) {
			$this -> error('ID有误');
		} 

		$ids = is_array($ids)?$ids:[$ids];
		if (empty($ids)) {
            return false;
        }
		if ($this -> model -> destroy($ids)) {
			return true;
		} else {
			return false;
		} 
	} 
    
	/**
	 * 默认列表页
	 * @return mixed|string
	 */
	public function index() {
	    if ($this->request->isPost()) {
	        //修改排序
	        return $this->edit_order();
	    }
		return $this -> getAdminTable(self::getListData($map = [], $order = []));
	}
	
	/**
	 * 列表页修改排序
	 */
	protected function edit_order(){
	    $data = $this->request->Post();
	    foreach($data['orderdb'] AS $id=>$list){
	        $map = [
	                'id'=>$id,
	                'list'=>$list
	        ];
	        $this->model->update($map);
	    }
	    $this->success('修改成功');
	}
    
	/**
	 * 默认发布页
	 * @return mixed|string
	 */
	public function add() {
		return $this -> addContent();
	} 
    
	/**
	 * 默认删除功能
	 * @param unknown $ids
	 */
	public function delete($ids = null) {
		if ($this -> deleteContent($ids)) {
			$this -> success('删除成功');
		} else {
			$this -> error('删除失败');
		} 
	} 
    
	/**
	 * 修改页
	 * @param unknown $id
	 * @return mixed|string
	 */
	public function edit($id = null) {
		if (empty($id)) $this -> error('缺少参数');
		$info = $this -> getInfoData($id);
		return $this -> editContent($info);
	} 
} 
