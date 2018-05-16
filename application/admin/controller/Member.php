<?php
namespace app\admin\controller;

use app\common\controller\AdminBase; 

use app\common\traits\AddEditList;

use app\common\model\User as UserModel;

use app\common\model\Group as GroupModel;

class Member extends AdminBase
{
	
	
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items;
	protected $list_items;
	protected $tab_ext = [
				'id'=>false,
				'page_title'=>'会员资料管理',
				];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new UserModel();
		$this->Gmodel = new GroupModel();
		
		$this->list_items = [
				['uid', '用户ID', 'text'],
                ['username', '用户名', ''],
		        ['groupid', '用户组', 'select',$this->Gmodel->getTitleList()],
				['regdate', '注册日期', 'datetime'],
				['regip', '注册IP', 'text'],
                ['money', '积分', 'text'],
				['rmb', '余额', 'text'],
		        ['yz', '审核', 'switch'],
			];
		$this -> tab_ext['search'] = ['username'=>'用户名','uid'=>'用户ID','regip'=>'注册IP'];
		$this -> tab_ext['order'] = 'money,rmb';
		$this -> tab_ext['filter_search'] = [
		        'groupid'=>getGroupByid(),
		];
	}
	
	//修改用户
	public function edit($id = null)
	{
	    if(empty($id)) $this->error('缺少参数');	    
    	if (IS_POST) {    	    
    	    $data = get_post('post');    	    
    	    if(!defined('SUPER_ADMIN')&&$data['groupid']==3&&$this->user['groupid']!=3){
    	        $this->error('你无权修改超管资料!');
    	    }
    	    // 验证
    	    if(!empty($this->validate)){
    	        // 验证
    	        $result = $this->validate($data, $this->validate);
    	        if(true !== $result) $this->error($result);
    	    }
    	    if ( $this->model->edit_user($data) ) {
    	        $this->success('修改成功', 'index');
    	    } else {
    	        $this->error('修改无效');
    	    }
    	}
	    
	    $info = $this->model->get_info($id);
	    
	    $listgroup = $this->Gmodel->getTitleList();
	    $this->form_items = [
	            ['hidden', 'uid'],
	            ['static', 'username', '用户名','用户名不可修改'],
	            ['text', 'password', '密码','留空则代表不修改密码,之前加密后的密码是：'.$info['password']],
	            ['select', 'groupid', '用户组','',$listgroup],
	            ['text', 'nickname', '昵称'],
	            ['text', 'email', '邮箱'],
	            ['radio', 'sex', '性别','',[0=>'保密',1=>'男',2=>'女']],
	            ['jcrop', 'icon', '头像'],
	            ['number', 'money', '积分'],
	            ['number', 'rmb', '可用余额'],
	    ];
	    $info['password']='';
	    return $this->editContent($info);
	}
	
	//新增加用户
	public function add()
	{
	    if (IS_POST) {	        
	        $data = get_post('post');
	        if($data['groupid']==3&&$this->user['groupid']!=3){
	            $this->error('你无权创建超管!');
	        }
	        // 验证
	        if(!empty($this->validate)){
	            // 验证
	            $result = $this->validate($data, $this->validate);
	            if(true !== $result) $this->error($result);
	        }	        
	        $uid = $this->model->register_user($data);
	        
	        if ( is_numeric($uid) ) {
	            $this->success('添加成功', 'index');
	        } else {
	            $this->error('添加失败:'.$uid);
	        }
	    }
	    $listgroup = $this->Gmodel->getTitleList();
	    $this->form_items = [
	            ['text', 'username', '用户名','创建后不可修改'],
	            ['text', 'password', '密码'],
	            ['select', 'groupid', '用户组','',$listgroup],
	            ['text', 'email', '邮箱'],
	    ];
	    return $this->addContent();
	}
	
	//删除用户
	public function delete($ids = null)
	{
	    if(empty($ids)) $this->error('缺少参数');	    
	    $ids = is_array($ids) ? $ids : [$ids];	    
	    $num = 0;
	    foreach($ids AS $uid){
	        $data = UserModel::get_info($uid);
	        if($data['groupid']==3&&$this->user['groupid']!=3){
	            $this->error('你无权删除超管!');
	        }
	        if($this->model->delete_user($uid)){
	            $num++;
	        }
	    }
	    
	    if( $num ){
	        $this->success('成功删除 '.$num.' 个用户', 'index');
	    }else{	        
	        $this->error('删除失败');
	    }
	}
}
