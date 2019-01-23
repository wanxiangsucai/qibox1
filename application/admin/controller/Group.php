<?php
namespace app\admin\controller;
use app\common\controller\AdminBase; 
use app\common\model\Group AS GroupModel;
use app\common\util\Tabel;
use app\common\util\Form;
use app\common\util\Menu;

class Group extends AdminBase
{
    protected $validate;
    protected $grouplist;
    protected $group_nav;
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new GroupModel();
    }
    
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
    
    public function index()
    {
	    $listdb = GroupModel::where([])->order('type desc,level asc,id asc')->column(true);

	    $tab = [
//         	            ['id', '用户组ID', 'text'],
        	            ['title', '用户组名称', 'text.edit'],
        	            ['type', '用户组性质', 'select2',['会员组','系统组']],
        	            ['allowadmin', '后台权限','callback' ,function($key,$v){
        	                if($key){
        	                    $show = '<a title="设置后台权限" icon="fa fa-gear" class="btn btn-xs btn-default" href="'.url('admin_power',['id'=>$v['id']]).'"><i class="fa fa-gear"></i></a>';
        	                }
        	                return $key>0?$show:'';
        	            },'__data__'],
        	            ['level', '升级积分', 'text.edit'],
        	            ['daytime', '有效期', 'text.edit'],
	                   //['right_button', '操作', 'btn'],
	            ];
	    
	    $table = Tabel::make($listdb,$tab)
	    ->addTopButton('add',['title'=>'添加用户组'])
	    //->addTopButton('delete')
// 	    ->addRightButton('edit')
// 	    ->addRightButton('delete')	    
	    //->addPageTips('省份管理')
	    ->addPageTitle('用户组管理');

        return $table::fetchs();
	}
	
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

	    $form = Form::make()
	    ->addText('title','用户组名称')
	    ->addRadio('type','用户组类型','会员级可以自由升级,系统组不能自由升级',['会员组','系统组'],0)
	    ->addRadio('allowadmin','是否有后台权限','',['没权限','有后台权限'],0)
	    ->addNumber('level','升级所需积分')
	    ->addText('daytime','有效期(天)','针对认证的会员组而言的,系统组无效')
	    ->addJs('type','0','level')
	    ->addPageTitle('添加菜单');
	    return $form::fetchs();
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
	    $info = GroupModel::get($id);
	    $form = Form::make([],$info)
	    ->addPageTitle('修改用户组')
	    ->addText('title','用户组名称')
	    ->addRadio('type','用户组类型','会员级可以自由升级,系统组不能自由升级',['会员组','系统组'])
	    ->addRadio('allowadmin','是否有后台权限','',['没权限','有后台权限'])
	    ->addNumber('level','升级所需积分')
	    ->addText('daytime','有效期(天)','针对认证的会员组而言的,系统组无效')
	    ->addText('wap_page','wap个人主页模板','请输入详细路径,比如:“/member_style/default/xxx.htm”若在对应的模板/template/member_style/default/member/user/目录下，只输入文件名即可，比如“indexppp”，提醒:对于不同的用户组模板文件名如果为index3或index8等即用户组ID结尾的话,这里可以不输入')
	    ->addText('wap_member','wap会员中心模板','请输入详细路径,比如:“/member_style/default/xxx.htm”若在对应的模板/template/member_style/default/member/index/目录下，只输入文件名即可，比如“indexppp”')
	    ->addText('pc_page','pc个人主页模板','请输入详细路径,比如:“/member_style/default/xxx.htm”若在对应的模板/template/member_style/default/member/user/目录下，只输入文件名即可，比如“indexppp”')
	    ->addText('pc_member','pc会员中心模板','请输入详细路径,比如:“/member_style/default/xxx.htm”若在对应的模板/template/member_style/default/member/index/目录下，只输入文件名即可，比如“indexppp”')
	    ->addJs('type','0','level')
	    ->addHidden('id',$id);

	    return $form::fetchs();
	}
	
	public function delete($ids){
	    if (empty($ids)) {
	        $this -> error('ID有误');
	    }
	    $ids = is_array($ids)?$ids:[$ids];
	    if (GroupModel::destroy($ids)) {
	        $this->success('删除成功','index');
	    } else {
	        $this->error('删除失败');
	    }
	}
}
