<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\model\Groupcfg AS GroupcfgModel;

use app\common\traits\AddEditList;

class GroupCfg extends AdminBase
{
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext;
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new GroupcfgModel();
	}
	
	/**
	 * 填写表单参数选项
	 * @param number $group 用户组ID
	 */
	protected function set_items($group=0){	    
	    $array = [];
	    foreach ( getGroupByid() AS $key => $name) {
	        $array[$key] = $name;
	    }
	    
	    $this->form_items = [
	            ['hidden', 'id'],
	            ['text', 'title', '字段参数中文名称',''],
	            ['text', 'c_key', '字段变量名','创建后不要随意修改'],	            
	            ['radio', 'type', '所属分组','',$array,$group],
	            ['select', 'form_type', '表单类型','',config('form'),'text'],
	            ['textarea', 'options', '表单参数项','每条参数换一行,参数与名称用“|”线隔开，比如“1|正确”'],
	            ['text', 'c_descrip', '介绍描述'],
	            //['textarea', 'htmlcode', '额外HTML代码'],
	    ];
	    
	    $this->tab_ext['trigger'] = [
	            ['form_type','checkbox,checkboxtree,radio,select','options'],
	            //['type',implode(',',$plugin_array),'ifsys'],
	    ];
	}

	//删除，这是重写AddEditList的方法
	protected function deleteContent($ids){
	    
	    if(empty($ids)){
	        $this->error('ID有误');
	    }
	    
	    $ids = is_array($ids)?$ids:[$ids];
	    
	    //if ($this->model->destroy($ids)) {
	    if ($this->model->where('id','in',$ids)->delete()) {	    
	        return true;
	    } else {
	        return false;
	    }
	}
	
	public function delete($ids = null)
	{
	    //$data = get_post();
	    //$ids=$data['ids'];
	    $info = $this->model->get($ids);
	    if( $this->deleteContent($ids) ){
	        $this->success('删除成功',auto_url('index',['group'=>$info['type']]));
	    }else{	        
	        $this->error('删除失败');
	    }
	}
	
	public function edit($id = null)
	{
	    if (empty($id)) $this->error('缺少参数');
	    
	    if(IS_POST){
	        $data = get_post();
	        $result = $this->validate($data,
	                [
	                        'c_key'  => 'require|regex:^[_a-zA-Z]\w{0,39}$',
	                        'title'   => 'require|max:50',
	                ]);
	        if($result !== true ){
	            $this->error($result);
	        }
	        
	        $map = [
	                'c_key'=>$data['c_key'],
	                'type'=>$data['type'],
	        ];
	        $rs = $this->model->where($map)->find();
	        if ( ($rs && $rs['id']!=$id) || table_field('memberdata',$data['c_key']) ) {
	            $this->error('变量名已经存在了，请更换一个');
	        }
	        if (!empty($this -> validate)) {   // 验证表单
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }
	        if ($this -> model -> update($data)) {
	            $this->success('数据更新成功',auto_url('index',['group'=>$data['type']]));
	        } else {
	            $this->error('数据更新失败');
	        }
	    }
	    
	    $info = $this->model->where('id',$id)->find();
	    
	    $info['form_type'] || $info['form_type']='text';    
	    
	    $this->set_items();
	    
	    return $this->editContent($info,auto_url('index',['group'=>$info['type']]));
	}
	
	public function add($group=0)
	{
	    if(IS_POST){
	        $data = get_post();
	        
	        $result = $this->validate($data,
	                [
	                        'c_key'  => 'require|regex:^[a-z]\w{0,39}$',
	                        'title'   => 'require|max:50',
	                ]);
	        if($result !== true ){
	            $this->error($result);
	        }elseif(!$data['type']){
	            $this->error('请先选择一个分类');
	        }
	        
	        $map = [
	                'c_key'=>$data['c_key'],
	                'type'=>$data['type'],
	        ];
	        
	        if ($this->model->where($map)->find() || table_field('memberdata',$data['c_key'])) {
	            $this->error('变量名已经存在了，请更换一个');
	        }
	        if (!empty($this -> validate)) {   // 验证表单         
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }
	        if ( $this -> model -> create($data)) {
	            $this->success('字段创建成功',auto_url('index',['group'=>$data['type']]));
	        } else {
	            $this->error('数据插入失败');
	        }
	    }
	    $this->set_items($group);
	    return $this->addContent();
	}
	
	/**
	 * 列出所有分类
	 * @return string[][]
	 */
	private function nav(){
	    $tab_list = [];
	    foreach ( getGroupByid() AS $key => $name) {
	        $tab_list[$key]['title'] = $name;
	        $tab_list[$key]['url']   = auto_url('index', ['group' => $key]);
	    }	    
	    $tab_list[0]   = [
	            'title'=>'冗余的数据',
	            'url'=>auto_url('index', ['group' => '0']),
	    ];	    
	    return $tab_list;
	}

	public function index($group=0)
    {
		$this->tab_ext = [
				'nav'=>[ self::nav() , $group],
				'help_msg'=>'用户组字段管理',
				];

		$this->list_items = [
				['c_key', '关键字变量名', 'text'],              
				['title', '名称', 'text.edit'],
				['form_type', '表单类型', 'select2',config('form')],
		        ['list', '排序值', 'text.edit'],
			];
		
		$this->tab_ext['top_button'] =[
		        [
		                'title' => '新增字段',
		                'icon'  => 'fa fa-fw fa-th-list',
		                'class' => 'btn btn-primary',
		                'href'  => auto_url('add',['group'=>$group])
		        ],
		];
		
		$map = $group ? ['type'=>$group] : [];
		$data = $this->model->where($map)->order('list','desc')->paginate(50);		
		return $this->getAdminTable( $data );
    }

}
