<?php
namespace app\admin\controller;

use app\common\controller\AdminBase; 
use app\common\model\Group AS GroupModel;
use app\common\util\Menu;
use app\common\traits\AddEditList;

class Group extends AdminBase
{
    use AddEditList;
    protected $validate;
    protected $money_name = '积分';
//     protected $grouplist;
//     protected $group_nav;
    
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new GroupModel();
        if($this->webdb['up_group_use_rmb']){
            $this->money_name='金额';
        }
        
        $this->form_items = [
            ['text','title','用户组名称'],
            ['radio','allowadmin','是否有后台权限','',['没权限','有后台权限'],0],
            ['radio','type','用户组类型','会员级可以升级,系统组不能升级',['会员组','系统组'],0],
            ['number','level','升级所需'.$this->money_name],
            ['text','daytime','有效期(天)','针对会员组而言的，系统组无效，0则是长期有效'],
        ];
        
        $this->tab_ext['trigger'] = [
            ['type', 0, 'level,daytime'],
        ];
        
    }
    
    /**
     * 设置后台权限
     * @param number $id
     * @return mixed|string
     */
    public function admin_power($id=0){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $array = [
                    'id'=>$id,
                    'admindb'=>json_encode($data['powerdb']),
            ];
            if(GroupModel::update($array)){
                $this->success('更新成功','index');
            }else{
                $this->error('数据更新失败');
            }
        }
        $info = GroupModel::getById($id); 
        $info['admindb'] = json_decode($info['admindb'],true);
        
        $array = Menu::get_menu();
        
        unset($array['often']);        
        $this->assign('listdb', $array);
        $this->assign('info', $info);
        
        return $this->fetch();
    }
    
    /**
     * 用户组管理
     * @return mixed|string
     */
    public function index()
    {
	    $this->list_items = [
	        ['title', '用户组名称', 'text.edit'],
	        ['type', '用户组性质', 'select2',['会员组','系统组']],
	        ['allowadmin', '后台权限','callback' ,function($key,$v){
	            $show = '';
	            if($v['type']==1){
	                $show = "<script type='text/javascript'>$(function(){ $(\"input[name='level[{$v['id']}]']\").hide();$(\"input[name='daytime[{$v['id']}]']\").hide(); });</script>";
	            }
	            if($key==1){
	                $show .= '<a title="设置后台权限" icon="fa fa-gear" class="btn btn-xs btn-default" href="'.url('admin_power',['id'=>$v['id']]).'"><i class="fa fa-gear"></i></a>';
	            }
	            return $show;
	        }],
	        ['level', '升级'.$this->money_name, 'text.edit'],
	        ['daytime', '有效期', 'text.edit'],
	    ];
	    
	    $this->tab_ext['page_title'] = '用户组管理';
	    
	    $this->tab_ext['top_button'] = [
	        [
	            'type'=>'add',
	            'title'=>'新增用户组',
	        ],
	    ];
	    
	    $this->tab_ext['help_msg'] = '会员组才能自由升级，有效期为0则是永久有效，在系统设置那里可以切换升级方式是用积分还是金额';
	    
	    $listdb = GroupModel::where([])->order('type desc,level asc,id asc')->column(true);
	    return $this -> getAdminTable($listdb);

	}
	
	/**
	 * 新增用户组
	 * @return mixed|string
	 */
	public function add(){
	    if ($this->request->isPost()) {
	        $data = $this->request->post();
	        if (!empty($this -> validate)) {   //验证数据
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }
	        if($data['title']==''){
	            $this->error('用户组名称不能为空!');
	        }	        
	        if(GroupModel::create($data)){	                
	            $this->success('创建成功 ', url('index') );
	        } else {
	            $this->error('创建失败');
	        }
	    }
	    
	    $this->tab_ext['page_title'] = '新建用户组';
	    
	    return $this->addContent();
	}
	
	protected function check_tpl($file=''){
	    if (strstr($file,'/')) {
	        if (!is_file(TEMPLATE_PATH.$file)) {
	            $this->error('找不到此目录的文件:'.TEMPLATE_PATH.$file);
	        }	        
	    }elseif($file && strstr($file,'.htm')){
	        $this->error('文件名不要加.htm后缀:'.$file);
	    }
	}
    
	/**
	 * 修改用户组
	 * @param number $id
	 * @return unknown
	 */
	public function edit($id=0){
	    
	    if ($this->request->isPost()) {
	        $data = $this -> request -> post();
	        if (!empty($this -> validate)) {   //验证数据
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }
	        $this->check_tpl($data['wap_page']);
	        $this->check_tpl($data['wap_member']);
	        $this->check_tpl($data['pc_page']);
	        $this->check_tpl($data['pc_member']);
	        if (GroupModel::update($data)) {
	            $this->success('修改成功',url('index'));
	        } else {
	            $this->error('修改失败');
	        }
	    }
	    $array = [
	        ['text','wap_page','wap个人主页模板','请输入详细路径,比如:“/member_style/default/xxx.htm”若在对应的模板/template/member_style/default/member/user/目录下，只输入文件名即可，比如“indexppp”，提醒:对于不同的用户组模板文件名如果为index3或index8等即用户组ID结尾的话,这里可以不输入'],
	        ['text','wap_member','wap会员中心模板','请输入详细路径,比如:“/member_style/default/xxx.htm”若在对应的模板/template/member_style/default/member/index/目录下，只输入文件名即可，比如“indexppp”'],
	        ['text','pc_page','pc个人主页模板','请输入详细路径,比如:“/member_style/default/xxx.htm”若在对应的模板/template/member_style/default/member/user/目录下，只输入文件名即可，比如“indexppp”'],
	        ['text','pc_member','pc会员中心模板','请输入详细路径,比如:“/member_style/default/xxx.htm”若在对应的模板/template/member_style/default/member/index/目录下，只输入文件名即可，比如“indexppp”'],
	    ];
	    
	    $this->form_items = array_merge($this->form_items,$array);
	    
	    $info = GroupModel::get($id);
	    return $this->editContent($info);
	}
	
	/**
	 * 删除用户组
	 * @param unknown $ids
	 */
	public function delete($ids){
	    if (empty($ids)) {
	        $this -> error('ID有误');
	    }
	    if ($ids==3||$ids==2||$ids==8) {
	        $this -> error('内置用户组,不能删除');
	    }
	    $ids = is_array($ids)?$ids:[$ids];
	    if (GroupModel::destroy($ids)) {
	        $this->success('删除成功','index');
	    } else {
	        $this->error('删除失败');
	    }
	}
}
