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
        $m_array = ['_sys_'=>'系统全局'];
        foreach (modules_config() AS $rs){
            $m_array[$rs['keywords']] = $rs['name'];
        }
        
	    $type = intval($type);
	    $map = $sysname_array = [];
	    if ($type>0) {
	        $map = ['type'=>['in',[0,$type]]];
	        $_ar = WebmenuModel::where($map)->group('sysname')->column('sysname');
	        foreach($_ar AS $key){
	            $sysname_array[$key] = $m_array[$key];
	        }
	    }
	    if (input('search_field')!='' && input('keyword')!='') {
	        $map[input('search_field')] = input('keyword');
	    }
	    $listdb = Tree::config(['title' => 'name'])->toList(
	            WebmenuModel::where($map)->order('list desc,id desc')->column(true)
	            );
	    
	    $tab = [
	            //['id','ID','text'],	            
	        ['title_display','链接名称','text'],
	        ['sysname','归属频道','select2',$m_array],
	        ['style','图标','icon'],
	        ['type','所属','select2',['头部通用','PC头部菜单','wap头部菜单','wap底部菜单']],
	        ['list','排序值','text.edit'],
	        ['ifshow','是否显示','switch'],
	        ['target','新窗口打开','yesno'],
	        ['id','添加子菜单','callback',function($key,$rs){
	            $code = ' ';
	            if(!$rs['pid']){
	                $code = "<a href='".url('add',['pid'=>$key,'sysname'=>$rs['sysname']])."' class='fa fa-plus _pop' title='添加子菜单'></a>";
	            }
	            return $code;
	        }],
	            //['right_button', '操作', 'btn'],
// 	            ['right_button', '操作', 'callback',function($value,$rs){
// 	                if($rs['pid']>0)$value=str_replace('_tag="add"', 'style="display:none;"', $value);
// 	                return $value;
// 	            },'__data__'],
	    ];

	    $table = Tabel::make($listdb,$tab)
	    ->addTopButton('add',[
	        'title'=>'添加菜单',
	        'href'=>url('add',[
	            'type'=>$type,
	            'sysname'=>input('search_field')=='sysname'?input('keyword'):''
	        ]),
	        'class'=>'_pop',	        
	    ])
	    ->addTopButton('delete');
	    if ($type) {
	        $table->addTopButton('copy',[
	            'title'       => '复制',
	            'icon'        => '',
	            'class'       => 'ajax-post confirm',
	            'target-form' => 'ids',
	            'icon'        => 'fa fa-clone',
	            'href'        => auto_url('copy',['type'=>$type])
	        ]);
	    }
	    //->addRightButton('add',['title'=>'添加下级菜单','href'=>url('add',['pid'=>'__id__'])])
	    $table->addRightButton('delete')	    
	    ->addRightButton('edit',['class'=>'_pop'])
	    //->addPageTips('省份管理')
	    //->addOrder('id,list')
	    ->addPageTitle('网站菜单管理')
	    ->addNav( [
	            0=>['title' => '所有菜单', 'url' =>url('index',['type'=>'0'])],
	            1=>['title' => 'PC头部菜单', 'url' =>url('index',['type'=>1])],
	            2=>['title' => 'wap头部菜单', 'url' =>url('index',['type'=>2])],
	            3=>['title' => 'wap底部菜单', 'url' =>url('index',['type'=>3])],
	            
	    ],$type);
	    
	    //添加列筛选项
	    if ($sysname_array) {
	        $table->addFilter([
	            'sysname'=>$sysname_array,
	        ]);
	    }

        return $table::fetchs();
	}
	
	public function copy($type=0,$ids=[]){
	    if (!$type) {
	        $this->error('分类不存在!');
	    }elseif (count($ids)<1){
	        $this->error('必须选择一个链接!');
	    }
	    if ($this->request->isPost()) {
	        $data = $this->request->post();
	        if (empty($data['sysname'])) {
	            $this->error('请必须选择归属频道!');
	        }
	        $p_array = [];
	        foreach($ids AS $id){
	            $info = getArray(WebmenuModel::get($id));
	            $array = $info;
	            unset($array['id']);
	            $array['sysname'] = $data['sysname'];
	            $array['pid'] = ($info['pid']&&$p_array[$info['pid']]) ? $p_array[$info['pid']] : 0;
	            $result = WebmenuModel::create($array);
	            $_id = $result->id;
	            if (!$info['pid']) { //一级分类的情况
	                $p_array[$id] = $_id;
	            }
	        }
	        $this->success("操作成功",urls('index',['type'=>$type]));
	    }
	    $m_array = ['_sys_'=>'系统全局'];
	    foreach (modules_config() AS $rs){
	        $m_array[$rs['keywords']] = $rs['name'];
	    }
	    $form = Form::make()
	    ->addSelect('sysname','归属频道','',$m_array)
	    ->addPageTitle('复制菜单');
	    return $form::fetchs();
	}
	
	public function add($pid=0,$type=0,$sysname=''){
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
	    $m_array = ['_sys_'=>'系统全局'];
	    foreach (modules_config() AS $rs){
	        $m_array[$rs['keywords']] = $rs['name'];
	    }
	    //$array = WebmenuModel::where('type','in',[0,$info['type']])->where('pid',0)->column('id,name');
	    //$father_name = '';
	    
	    $form = Form::make();
	    if($pid){
	        $form->addHidden('sysname',$info['sysname']);
	    }else{
	        $form->addSelect('sysname','归属频道','',$m_array,$sysname?:'_sys_');
	    }
	    $form->addPageTips('父菜单为PC或WAP的话,子菜单设置通用无效')
	    //->addSelect('pid','父级菜单','',$array,$pid)
	    ->addHidden('pid',$pid)
	    ->addText('name','菜单名称')
	    ->addText('url','菜单链接')
	    ->addRadio('type','使用范围','',[1=>'PC头部菜单',2=>'wap头部菜单',3=>'wap底部菜单'],intval($info['type']?:3))
	    ->addRadio('target','是否新窗口打开','',['本窗口打开','新窗口打开'],0)
	    ->addIcon('style','图标','需要模板配合做样式')
	    ->addColor('fontcolor','字体颜色')
	    ->addColor('bgcolor','背景颜色')
	    ->addText('activate','选中,是否高亮显示','需要模板配合做样式,主页就输入index,商城就输入shop以此类推,不过对于频道而言最好是后面加个-横杠线因为对于栏目,可以定义为shop-18，另外对于独立页的话，就设置index-alonepage5,其中5就是相应的独立页id值<a href="http://help.php168.com/1841073" target="_blank">点击查看详细教程</a>')
	    ->addPageTitle('添加菜单');
	    return $form::fetchs();
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
	    $info = getArray(WebmenuModel::get($id));
	    $array = [];
	    if ($info['pid']) {
	        $array = WebmenuModel::where('type','in',[0,$info['type']])->where('pid',0)->where('sysname',$info['sysname'])->column('id,name');
	    }
	    
	    $form = Form::make([],$info)
	    ->addPageTips('父菜单为PC或WAP的话,子菜单设置通用无效')
	    ->addPageTitle('修改菜单');
	    if ($array) {
	        $form->addSelect('pid','父级菜单','',$array);
	    }	    
	    $form->addText('name','名称')	    
	    ->addText('url','菜单链接')
	    //->addRadio('type','使用范围','',[1=>'PC头部菜单',2=>'wap头部菜单',3=>'wap底部菜单'])
	    ->addRadio('target','是否新窗口打开','',['本窗口打开','新窗口打开'])
	    ->addRadio('ifshow','是否隐藏','',['隐藏','显示(不隐藏)'])
	    ->addNumber('list','排序值')
	    ->addIcon('style','图标')
	    ->addColor('fontcolor','字体颜色','是否有效果,需要模板配合做样式')
	    ->addColor('bgcolor','背景颜色','是否有效果,需要模板配合做样式')
	    ->addText('activate','选中,是否高亮显示','需要模板配合做样式,主页就输入index,商城就输入shop以此类推,不过对于频道而言最好是后面加个-横杠线因为对于栏目,可以定义为shop-18，另外对于独立页的话，就设置index-alonepage5,其中5就是相应的独立页id值<a href="http://help.php168.com/1841073" target="_blank">点击查看详细教程</a>')
	    ->addTextarea('script','脚本事件','要填写的话,需要补齐 &lt;script&gt;&lt;/script&gt;同理,也可以写css或html')
	    ->addHidden('id',$id);

	    return $form::fetchs();
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
