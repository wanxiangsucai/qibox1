<?php
namespace plugins\config_set\admin;

use app\common\controller\AdminBase;
use plugins\config_set\model\Group AS GroupModel;
use app\common\model\Config AS ConfigModel;

use app\common\traits\AddEditList;

class Config extends AdminBase
{
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext;
	protected $group = 'base';
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new ConfigModel();
	}
	
	/**
	 * 填写表单参数选项
	 * @param number $group
	 * @param array $info
	 */
	protected function set_items($group=0,$info=[]){	    
	    $array = [];	    
	    foreach ( GroupModel::getNavTitle(false,false,'id,title,sys_id') AS $key => $rs) {
	        if ($rs['sys_id']>0) {
	            $rs['title'] .= '('.modules_config($rs['sys_id'])['name'].')';
	        }elseif($rs['sys_id']<0){
	            $rs['title'] .= '('.plugins_config(abs($rs['sys_id']))['name'].')';
	        }
	        $array[$rs['id']] = $rs['title'];
	    }
	    
	    $this->form_items = [
	            ['hidden', 'id'],
	            ['text', 'title', '字段参数中文名称',''],
	            ['text', 'c_key', '字段变量名','创建后不要随意修改'],	            
	            ['radio', 'type', '所属分组','',$array,$group],
	            ['select', 'form_type', '表单类型','',config('form'),'text'],
	            ['textarea', 'options', '表单参数项','每条参数换一行,参数与名称用“|”线隔开，比如“1|正确”<br>如果取数据表的数据,格式如下:cms_sort@id,name@mid=1&pid=0 <a href="http://help.php168.com/1579362" target="_blank">点击查看教程</a>'],
	            ['textarea', 'addsorts', '添加分类数据'],
	            ['text', 'c_descrip', '介绍描述'],
	            ['radio', 'ifsys', '是否属于系统全局参数字段','变量值全站通用,不局限于某个插件模块',['否','是'],intval($info['ifsys'])],
	        ['radio', 'is_open', '是否裸露给外部接口','密钥等重要参数就不能裸露给外部接口调用',['否','是'],intval($info['ifsys'])],
	        ['radio', 'is_hide', '是否禁用','选择禁用的话，将不在系统参数那里显示，但不影响原来已设置的值',['否','是'],intval($info['is_hide'])],
	            //['textarea', 'htmlcode', '额外HTML代码'],
	    ];
	    
	    //对于插件而言可以选择设置为全局参数
	    $plugin_array = GroupModel::where('sys_id','<',0)->column('id');
	    
	    $this->tab_ext['trigger'] = [
	            ['form_type','checkbox,checkboxtree,radio,select','options'],
	        ['form_type','treeone,treemore','addsorts','',''],
	            ['type',implode(',',$plugin_array),'ifsys'],
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
	    $info = ConfigModel::get($ids);
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
	                        'title'   => 'require|max:90',
	                ]);
	        if($result !== true ){
	            $this->error($result);
	        }
	        $config_sort = GroupModel::get($data['type']);
	        $data['sys_id'] = $config_sort['sys_id'];      //0是核心系统,正数是频道ID,负数是插件ID
	        if ($data['sys_id']==0) {
	            $data['ifsys'] = 1;    //不是插件,也不模块的话,就默认为系统全局参数
	        }elseif($config_sort['sys_id']>0){
	            $data['ifsys'] = 0;    //频道参数不允许设置为全局变量
	        }
	        if($config_sort['sys_id']!=0){
	            //频道模块或插件的变量名不允许跟系统全局的有重复
	            $rs = $this->model->where(['c_key'=>$data['c_key'],'ifsys'=>1])->find();
	            if ($rs && $rs['id']!=$id) {
	                $this->error('系统全局变量名已经存在了，请换一个');
	            }
	        }
	        $map = [
	                'c_key'=>$data['c_key'],
	                'type'=>$data['type'],
	        ];
	        $rs = $this->model->where($map)->find();
	        if ($rs && $rs['id']!=$id) {
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
	    
	    $info = getArray($this->model->where('id',$id)->find());
	    
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
	        
	        $config_sort = GroupModel::get($data['type']);
	        $data['sys_id'] = $config_sort['sys_id'];      //0是核心系统,正数是频道ID,负数是插件ID
	        if ($data['sys_id']==0) {
	            $data['ifsys'] = 1;    //不是插件,也不模块的话,就默认为系统全局参数
	        }elseif($config_sort['sys_id']>0){
	            $data['ifsys'] = 0;    //频道参数不允许设置为全局变量
	        }
	        if($config_sort['sys_id']!=0){
	            //频道模块或插件的变量名不允许跟系统全局的有重复
	            if ($this->model->where(['c_key'=>$data['c_key'],'ifsys'=>1])->find()) {
	                $this->error('系统全局变量名已经存在了，请换一个');
	            }
	        }
	        $map = [
	                'c_key'=>$data['c_key'],
	                'type'=>$data['type'],
	        ];
	        
	        if ($this->model->where($map)->find()) {
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
	    $config_sort = GroupModel::get($group);
	    $this->set_items($group,['ifsys'=>$config_sort['ifsys']]);
	    return $this->addContent();
	}
	
	/**
	 * 列出所有分类
	 * @return string[][]
	 */
	private function nav(){
	    $tab_list = [];
	    foreach ( GroupModel::getNavTitle(false,false,'id,title,sys_id') AS $key => $rs) {
	        if ($rs['sys_id']>0) {
	            $rs['title'] .= '('.modules_config($rs['sys_id'])['name'].')';
	        }elseif($rs['sys_id']<0){
	            $rs['title'] .= '('.plugins_config(abs($rs['sys_id']))['name'].')';
	        }
	        $tab_list[$key]['title'] = $rs['title'];
	        $tab_list[$key]['url']   = auto_url('index', ['group' => $key]);
	    }	    
	    $tab_list[0]   = [
	            'title'=>'其它未分组',
	            'url'=>auto_url('index', ['group' => '0']),
	    ];	    
	    return $tab_list;
	}

	public function index($group=0,$sys_id=null,$excel=0)
    {
	    
        $map = $group!==null ? ['type'=>$group] : ['sys_id'=>$sys_id];
        
        if($excel==1){
            $data = $this->model->where($map)->order('list desc,id asc')->column(true);
            $this->down($data);
        }
        $data = $this->model->where($map)->order('list','desc')->paginate(50);
        
		$this->tab_ext = [
				'nav'=>[ self::nav() , $group],
				'help_msg'=>'系统字段管理',
				];

		$this->list_items = [
				['c_key', '关键字变量名', 'text'],  
		    ['is_hide', '禁用', 'switch'],
		    ['is_open', '裸露', 'yesno'],
				['title', '名称', 'text.edit'],
				['form_type', '表单类型', 'select2',config('form')],
		        ['type', '所属分组', 'select', GroupModel::getNavTitle() ],
		        ['list', '排序值', 'text.edit'],
			];
		
		$this->tab_ext['top_button'] =[
		        [
		                'title' => '新增字段',
		                'icon'  => 'fa fa-plus-circle',
		                'class' => 'btn btn-primary',
		                'href'  => auto_url('add',['group'=>$group])
		        ],
		    [
		        'title' => '导出下列字段',
		        'icon'  => 'fa fa-table',
		        'class' => 'btn btn-primary',
		        'href'  => auto_url('index',['group'=>$group,'excel'=>1])
		    ],
		];	
		
		$this->tab_ext['right_button'] =[
		    [
		        'type'=>'delete'
		    ],
		    [
		        'type'=>'edit',
		        'class' => '_pop',
		    ],
		];
				
		return $this->getAdminTable( $data );
    }
    
    protected function down($array=[]){
        
        if(!$array){
            $this->error('没有数据可导出!');
        }
        $outstr = '';
        foreach($array AS $rs){
            $rs['c_descrip'] = str_replace("\r\n", "\\r\\n", addslashes($rs['c_descrip']));
            $rs['title'] = addslashes($rs['title']);
            $rs['c_value'] = str_replace("\r\n", "\\r\\n", addslashes($rs['c_value']));
            $rs['htmlcode'] = str_replace("\r\n", "\\r\\n", addslashes($rs['htmlcode']));
            $rs['options'] = str_replace("\r\n", "\\r\\n", addslashes($rs['options']));
            $outstr .="INSERT INTO `qb_config` (`type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `ifsys`, `htmlcode`, `c_descrip`, `list`, `sys_id`, `is_open`) VALUES('-1','{$rs['title']}','{$rs['c_key']}','{$rs['c_value']}','{$rs['form_type']}','{$rs['options']}','{$rs['ifsys']}','{$rs['htmlcode']}','{$rs['c_descrip']}','{$rs['list']}','{$rs['sys_id']}','{$rs['is_open']}');\r\n";
        }
        ob_end_clean();
        header('Last-Modified: '.gmdate('D, d M Y H:i:s',time()).' GMT');
        header('Pragma: no-cache');
        header('Content-Encoding: none');
        header('Content-Disposition: attachment; filename=配置文件.sql');
        header('Content-type: text/sql');
        echo $outstr;
        exit;
    }

}
