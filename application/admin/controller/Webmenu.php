<?php
namespace app\admin\controller;
use app\common\controller\AdminBase; 
use app\admin\model\Webmenu AS WebmenuModel;
use app\common\util\Tabel;
use app\common\util\Form;
use util\Tree;

class Webmenu extends AdminBase
{
    protected $validate = '';
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new WebmenuModel();
    }
    
    public function index($type=0)
    {
	    $type = intval($type);
	    $map = [];
	    if ($type>0) {
	        $map = ['type'=>['in',[0,$type]]];
	    }
	    $listdb = Tree::config(['title' => 'name'])->toList(
	            WebmenuModel::where($map)->order('list desc,id desc')->column(true)
	            );

	    $tab = [
	            //['id','ID','text'],
	            ['style','图标','icon'],
	            ['title_display','链接名称','text'],
	            ['type','所属','select2',['头部通用','PC头部菜单','wap头部菜单','wap底部菜单']],
	            ['list','排序值','text.edit'],
	            ['ifshow','是否显示','switch'],
	            ['target','新窗口打开','yesno'],
	            //['right_button', '操作', 'btn'],
	            ['right_button', '操作', 'callback',function($value,$rs){
	                if($rs['pid']>0)$value=str_replace('_tag="add"', 'style="display:none;"', $value);
	                return $value;
	            },'__data__'],
	    ];

	    $table = Tabel::make($listdb,$tab)
	    ->addTopButton('add',['title'=>'添加菜单','href'=>url('add',['type'=>$type])])
	    ->addTopButton('delete')
	    ->addRightButton('edit')
	    ->addRightButton('delete')	    
	    //->addPageTips('省份管理')
	    //->addOrder('id,list')
	    ->addPageTitle('网站菜单管理')
	    ->addNav( [
	            0=>['title' => '所有菜单', 'url' =>url('index',['type'=>'0'])],
	            1=>['title' => 'PC头部菜单', 'url' =>url('index',['type'=>1])],
	            2=>['title' => 'wap头部菜单', 'url' =>url('index',['type'=>2])],
	            3=>['title' => 'wap底部菜单', 'url' =>url('index',['type'=>3])],
	            
	    ],$type)
	    ->addRightButton('add',['title'=>'添加下级菜单','href'=>url('add',['pid'=>'__id__'])]);

        return $table::fetch();
	}
	
	public function add($pid=0,$type=0){
	    if ($this->request->isPost()) {
	        $data = $this->request->post();
	        if (!empty($this -> validate)) {   //验证数据
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }
	        if($data['name']==''){
	            $this->error('名称不能为空!');
	        }
	        
	        if(WebmenuModel::create($data)){	                
	            $this->success('创建成功 ', url('index',['type'=>$data['type']]) );
	        } else {
	            $this->error('创建失败');
	        }
	    }
	    if($pid){
	        $info = WebmenuModel::get($pid);
	    }else{
	        $info['type'] = intval($type);
	    }
	    $array = WebmenuModel::where('type','in',[0,$info['type']])->where('pid',0)->column('id,name');
	    
	    $form = Form::make()
	    ->addPageTips('父菜单为PC或WAP的话,子菜单设置通用无效')
	    ->addSelect('pid','父级菜单','',$array,$pid)
	    ->addText('name','菜单名称')
	    ->addText('url','菜单链接')
	    ->addRadio('type','使用范围','',['头部通用','PC头部菜单','wap头部菜单','wap底部菜单'],0)
	    ->addRadio('target','是否新窗口打开','',['本窗口打开','新窗口打开'],0)
	    ->addIcon('style','图标')
	    ->addText('activate','选中属性[:频道链接填上频道的目录名时,呆在该频道的话,菜单就是选中状态,一般留空]')
	    ->addPageTitle('添加菜单');
	    return $form::fetch();
	}

	public function edit($id=0){
	    
	    if ($this->request->isPost()) {
	        $data = $this -> request -> post();
	        if (!empty($this -> validate)) {   //验证数据
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }	        
	        if (WebmenuModel::update($data)) {
	            $this->success('修改成功',url('index',['type'=>$data['type']]));
	        } else {
	            $this->error('修改失败');
	        }
	    }
	    $info = WebmenuModel::get($id);
	    $array = WebmenuModel::where('type','in',[0,$info['type']])->where('pid',0)->column('id,name');
	    $form = Form::make([],$info)
	    ->addPageTips('父菜单为PC或WAP的话,子菜单设置通用无效')
	    ->addPageTitle('修改菜单')
	    ->addSelect('pid','父级菜单','',$array)
	    ->addText('name','名称')	    
	    ->addText('url','菜单链接')
	    ->addRadio('type','使用范围','',['头部通用','PC头部菜单','wap头部菜单','wap底部菜单'])
	    ->addRadio('target','是否新窗口打开','',['本窗口打开','新窗口打开'])
	    ->addRadio('ifshow','是否隐藏','',['隐藏','显示(不隐藏)'])
	    ->addNumber('list','排序值')
	    ->addIcon('style','图标')
	    ->addText('activate','选中属性[:频道链接填上频道的目录名时,呆在该频道的话,菜单就是选中状态,一般留空]','主页就输入index,商城就输入shop以此类推,不过最好是后面加个-横框线因为栏目的话,可以定义为shop-18')
	    ->addHidden('id',$id);

	    return $form->fetch();
	}
	
	public function delete($ids){
	    if (empty($ids)) {
	        $this -> error('ID有误');
	    }
	    $ids = is_array($ids)?$ids:[$ids];
	    if (WebmenuModel::destroy($ids)) {
	        $this->success('删除成功','index');
	    } else {
	        $this->error('删除失败');
	    }
	}
}
