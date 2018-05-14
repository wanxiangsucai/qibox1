<?php
namespace plugins\comment\admin;
use app\common\controller\AdminBase; 
use plugins\comment\model\Content AS contentModel;
use app\common\util\Tabel;
use app\common\util\Form;

class Content extends AdminBase
{
    protected $validate = '';

    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new contentModel();
    }
    
	public function index($pid=0)
	{
	    $ids = $map = [];
	    
	    $listdb = contentModel::where($map)->order( $this->getOrder('list desc,id desc') )->select();

	    $tab = [
	            ['id','ID','text'],
	            ['content','评论内容','callback',function($value){
	                return get_word(del_html($value), 70);
	            }],
	            ['sysid','所属模块','text'],
	            //['pid','所属'.$this->cfg_fname,'select',$array],
	            ['list','排序值','text.edit'],
	            ['right_button', '操作', 'btn'],
	    ];
	    
	    $table = Tabel::make($listdb,$tab)
	    //->addTopButton('add',['title'=>'添加'.$this->cfg_name,'href'=>purl('add',['pid'=>$pid])])
	    ->addTopButton('delete')
	    //->addRightButton('edit')
	    ->addRightButton('delete')	    
	    //->addPageTips('省份管理')
	    ->addOrder('id,list')
	    ->addPageTitle('管理评论');

        return $table::fetch();
	}
	
	public function add($pid=0){
	    if ($this->request->isPost()) {
	        $data = $this->request->post();
	        if (!empty($this -> validate)) {   //验证数据
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }
	        if($this->cfg_level>1 && empty($pid)){
	            $this->error($this->cfg_fname.'不存在');
	        }
	        $detail = explode("\r\n",$data['name']);
	        $i=0;
	        foreach($detail AS $value){
	            if(empty($value)){
	                continue;	                
	            }
	            $array =['name'=>$value,'level'=>$this->cfg_level,'pid'=>$pid];
	            if(contentModel::create($array)){
	                $i++;
	            }
	        }
	        if ($i) {
	            $this->success('成功创建 '.$i.' 个'.$this->cfg_name, purl('index',['pid'=>$pid]) );
	        } else {
	            $this->error('创建失败');
	        }
	    }
	    $array = contentModel::getTitleList(['level'=>$this->cfg_flevel]);
	    $form = Form::make()
	    ->addTextarea('name',$this->cfg_name.'名称','同时添加多个'.$this->cfg_name.'，请每个'.$this->cfg_name.'换一行')
	    ->addPageTitle('创建'.$this->cfg_name);
	    if($this->cfg_level>1){
	        $form->addSelect('pid','所属'.$this->cfg_fname,'',$array,$pid);
	    }
	    
	    return $form::fetch();
	}

	public function edit($id=0){
	    $info = contentModel::get($id);
	    if ($this->request->isPost()) {
	        $data = $this -> request -> post();	        
	        if (!empty($this -> validate)) {   //验证数据
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }	        
	        if (contentModel::update($data)) {
	            $this->success('修改成功',purl('index',['pid'=>$pid]));
	        } else {
	            $this->error('修改失败');
	        }
	    }
	    $array = contentModel::getTitleList(['level'=>$this->cfg_flevel]);
	    $form = Form::make([],$info)
	    //->setPageTips('修改省份')
	    ->addPageTitle('修改'.$this->cfg_name)	    
	    ->addText('name','名称')	    
	    ->addHidden('id',$id);
	    if($this->cfg_level>1){
	        $form->addSelect('pid','所属'.$this->cfg_fname,'',$array);
	    }
	    return $form->fetch();
	}
	
	public function delete($ids){
	    if (empty($ids)) {
	        $this -> error('ID有误');
	    }
	    $ids = is_array($ids)?$ids:[$ids];
	    if (contentModel::destroy($ids)) {
	        $this->success('删除成功','index');
	    } else {
	        $this->error('删除失败');
	    }
	}
}
