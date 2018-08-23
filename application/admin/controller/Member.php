<?php
namespace app\admin\controller;

use app\common\controller\AdminBase; 
use app\common\traits\AddEditList;
use app\common\model\User as UserModel;

/**
 * 用户管理
 */
class Member extends AdminBase
{
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items;
	protected $list_items;
	protected $tab_ext = [
				'id'=>false,                //用户数据表非常特殊，没有用id而是用uid
				'page_title'=>'会员资料管理',
				];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new UserModel();
	}
	
	/**
	 * 用户列表
	 */
	public function index() {
	    $order = 'uid desc';
	    $map = [];
	    $this->list_items = [
	        //['uid', '用户UID', 'text'],
	        ['username', '用户名', ''],
	        ['groupid', '用户组', 'select',getGroupByid()],
	        ['regdate', '注册日期', 'datetime'],
	        ['regip', '注册IP', 'text'],
	        ['money', '积分', 'text'],
	        ['rmb', '余额', 'text'],
	        ['yz', '审核', 'switch'],
	    ];
	    $this -> tab_ext['search'] = ['username'=>'用户名','uid'=>'用户ID','regip'=>'注册IP'];    //支持搜索的字段
	    $this -> tab_ext['order'] = 'money,rmb,uid,regdate';   //排序选择
	    $this -> tab_ext['id'] = 'uid';    //用户数据表非常特殊，没有用id而是用uid ， 这里需要特别指定id为uid
	    
	    //筛选字段
	    $this -> tab_ext['filter_search'] = [
	        'groupid'=>getGroupByid(),
	    ];
	    $this -> tab_ext['top_button'] = [
	        [
	                'type'=>'add',
	                'title'=>'创建新用户',
	        ],
	    ];
	    if ($this->user['groupid']==3) {   //超管才有批量删除功能
	        $this -> tab_ext['top_button'][]=[
	                'type'=>'delete',
	                'title'=>'批量删除用户',	 
	        ];
	    }
	    
	    return $this -> getAdminTable(self::getListData($map, $order ));
	} 
	
	/**
	 * 修改用户资料
	 * @param number $id 用户UID
	 */
	public function edit($id = 0)
	{
	    if(empty($id)) $this->error('缺少参数');	    
    	if (IS_POST) {    	    
    	    $data = get_post('post');    	    
    	    if(SUPER_ADMIN!==true&&$data['groupid']==3&&$this->user['groupid']!=3){
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
	    
	    $this->form_items = [
	            ['hidden', 'uid'],
	            ['static', 'username', '用户名','用户名不可修改'],
	            ['text', 'password', '密码','留空则代表不修改密码,之前加密后的密码是：'.$info['password']],
	            ['select', 'groupid', '用户组','',getGroupByid()],
	            ['text', 'nickname', '昵称'],
	            ['text', 'email', '邮箱'],
	            ['text', 'weixin_api', '微信接口ID'],
	            ['text', 'qq_api', 'QQ接口ID'],
	            ['text', 'wxapp_api', '小程序接口ID'],
	            ['radio', 'sex', '性别','',[0=>'保密',1=>'男',2=>'女']],
	            ['jcrop', 'icon', '头像'],
	            ['number', 'money', '积分'],
	            ['money', 'rmb', '可用余额'],
	            ['money', 'rmb_freeze', '冻结余额'],
	           ['text', 'truename', '真实名'],
	        ['text', 'mobphone', '手机号'],
	        ['text', 'idcard', '证件号'],
	        ['image', 'idcardpic', '证件扫描件'],
	        ['radio', 'email_yz', '邮箱验证与否','',['未验证','已验证']],
	        ['radio', 'mob_yz', '手机验证与否','',['未验证','已验证']],
	        ['radio', 'idcard_yz', '证件验证与否','',['未验证','已验证']],
	    ];
	    $info['password']='';
	    return $this->editContent($info);
	}
	
	/**
	 * 后台快速添加新用户
	 */
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
	    
	    $this->form_items = [
	            ['text', 'username', '用户名','创建后不可修改'],
	            ['text', 'password', '密码'],
	            ['select', 'groupid', '用户组','',getGroupByid()],
	            ['text', 'email', '邮箱'],
	    ];
	    return $this->addContent();
	}
	
	/**
	 * 删除用户 用户的其它资料这里没做删除，需要 借助钩子做删除处理 user_delete_end
	 * @param unknown $ids
	 */
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
