<?php
namespace app\common\controller\member;

use app\common\controller\MemberBase;
use app\common\traits\ModuleContent;

abstract class C extends MemberBase
{
    use ModuleContent;
    
    protected $validate = 'Content';
    protected $model;
    protected $m_model;
    protected $f_model;
    protected $s_model;
    protected $form_items;
    protected $list_items;
    protected $tab_ext;
    protected $mid;
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model        = get_model_class($dirname,'content');
        $this->s_model     = get_model_class($dirname,'sort');
        $this->m_model   = get_model_class($dirname,'module');
        $this->f_model     = get_model_class($dirname,'field');
    }
    
    /**
     * 按模型或栏目列出自己发布的信息
     * @param number $fid
     * @param number $mid
     * @return mixed|string
     */
    public function index($fid=0,$mid=0)
    {
        if(!$mid && !$fid){
            //没有指定栏目或模型的话， 就显示默认模型的内容
            $mid = $this->m_model->getId();
        }elseif($fid){
            $mid = $this->model->getMidByFid($fid);
        }
        
        $this->mid = $mid;
        $map = $fid ? ['fid'=>$fid] : ['mid'=>$mid];
        $map['uid'] = $this->user['uid'];
        
        //获取列表数据
        $data_list = $this->getListData($map,'',0,[],true);

	    $vars = [
	            'listdb'=>$data_list,
	            'field_db'=> $this->getEasyIndexItems(),   //列表显示哪些自定义字段
	            'pages'=> $data_list->render(),    //分页
	            'mid'=>$mid,
	            'fid'=>$fid,
	            'model_list' => $this->m_model->getTitleList(),
	    ];
	    
	    
	    //如果某个模型有个性模板的话，就不调用母模板
	    $template = getTemplate('index'.$mid)?:getTemplate('index');
	    return $this->fetch($template,$vars);
    }
    
    /**
     * 发布页，可以根据栏目ID或者模型ID，但不能为空，不然不知道调用什么字段
     * @param number $fid
     * @param number $mid
     * @return mixed|string
     */
    public function add($fid=0,$mid=0)
    {
        $data = $this->request->post();
        isset($data['fid']) && $fid = $data['fid'];
        
        if(!$mid && !$fid){
            $this->error('参数有误！');
        }elseif($fid && !$mid){ //根据栏目选择发表内容
            $mid = $this->model->getMidByFid($fid);
        }        
        $this->mid = $mid;

        //接口
        hook_listen('cms_add_begin',$data);
        if (($result=$this->add_check($mid,$fid,$data))!==true) {
            $this->error($result);
        }
        
        // 保存数据
        if ($this -> request -> isPost()) {
            input('?get.ext_id') && $this->request->post(['ext_id'=>input('get.ext_id')]);
            $this->saveAdd($mid,$fid,$data);
            
        }
        
        //发表时可选择的栏目
        $sort_array = $this->s_model->getTreeTitle(0,$mid);
        //发布页要填写的字段
        $this->form_items = $this->getEasyFormItems();     //发布表单里的自定义字段
        //如果栏目存在才显示栏目选择项
        if(count($sort_array)>1){
            $this->form_items = array_merge(
                    [
                            [ 'select','fid','所属栏目','',$sort_array,$fid],
                    ],
                    $this->getEasyFormItems()
                    );
        }
        
        //联动字段
       $this->tab_ext['trigger'] = $this->getEasyFieldTrigger();
       
       $this->tab_ext['area'] = config('use_area'); //是否启用地区
       
        //分组显示处理
        $this->tab_ext['group'] = $this->get_group_form($this->form_items);
        if( $this->tab_ext['group'] ){
            unset($this->form_items);
        }
        
        $this->tab_ext['page_title'] = '发布 '.$this->m_model->getNameById($this->mid);        
        return $this->addContent();
    }
    
    /**
     * 修改内容
     * @param unknown $id
     * @return mixed|string
     */
    public function edit($id)
    {
        $info = $this->getInfoData($id);
        if(empty($info)){
            $this->error('内容不存在!');
        }
        
		//表单数据
	    $data = $this->request->post();

        //接口
        hook_listen('cms_edit_begin',$data);
        if (($result=$this->edit_check($id,$info,$data))!==true) {
            $this->error($result);
        }
        
        $this->mid = $info['mid'];
        
        // 保存数据
        if ($this -> request -> isPost()) {
            $this->saveEdit($this->mid,$data);
        }
        
        //发表时可选择的栏目
        $sort_array = $this->s_model->getTreeTitle(0,$this->mid);
        //发布页要填写的字段
        $this->form_items = $this->getEasyFormItems();     //发布表单里的自定义字段
        //如果栏目存在才显示栏目选择项
        if(count($sort_array)>1){
            $this->form_items = array_merge(
                    [
                            [ 'select','fid','所属栏目','',$sort_array],
                    ],
                    $this->getEasyFormItems()
                    );
        }
        
        //联动字段
        $this->tab_ext['trigger'] = $this->getEasyFieldTrigger();
        
        $this->tab_ext['page_title'] = $this->m_model->getNameById($this->mid);
        
        $this->tab_ext['area'] = config('use_area'); //是否启用地区
        
        //分组显示
        $this->tab_ext['group'] = $this->get_group_form($this->form_items);
        if( $this->tab_ext['group'] ){
            unset($this->form_items);
        }
        
        //修改内容后，最好返回到模型列表页，因为有可能修改了栏目
        return $this->editContent($info , '' ,'member');
    }
    
    /**
     * 删除内容
     * @param unknown $ids
     */
    public function delete($ids=null)
    {
        if(empty($ids)){
            $this->error('ID有误');
        }
        $ids = is_array($ids) ? $ids : [$ids];
        $num = 0;
        foreach ($ids AS $id) {
            $info = $this->getInfoData($id);
            
            //接口
            hook_listen('cms_delete_begin',$id);
            if (($result=$this->delete_check($id,$info))!==true) {
                $this->error($result);
            }
            
            $this->deleteOne($id,$info['mid']) && $num++;
            
        }        
        if( $num>0 ){
            $this->success("成功删除 {$num} 条记录", auto_url('index',['mid'=>$this->mid]));
        }else{
            $this->error('删除失败');
        }
    }
}