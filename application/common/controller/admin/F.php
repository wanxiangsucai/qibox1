<?php
namespace app\common\controller\admin;

use app\common\controller\AdminBase;
use app\common\traits\AddEditList;

abstract class F extends AdminBase
{
    use AddEditList;
    
    protected $validate = 'Field';
    protected $model;
    protected $form_items = [];
    protected $list_items = [];
    protected $tab_ext = [];
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'field');
        $this->set_config();
    }
    
    protected function set_config(){
        
        $this->list_items = [
                ['title', '字段名称', 'text'],
                ['name', '字段变量名', 'text'],
                ['type', '表单类型', 'select',config('form')],
                ['list', '排序值', 'text.edit'],
        ];
        
        $this->form_items = [
                ['text', 'name', '字段变量名','创建后不能随意修改,否则会影响其它地方的数据调用,只能数字或字母及下画线，但必须要字母开头',"title_".rand(0,100)],
                ['text', 'title', '字段名称'],
                ['select', 'type', '表单字段类型','',config('form'),'text'],
                ['textarea', 'options', '参数选项', '用于单选、多选、下拉等类型,如果某项值要关联某个字段,格式如下:值|描述|字段名A,字段名B'],
                ['text', 'value', '字段默认值'],
                ['text', 'field_type', '数据库字段类型','','varchar(128) NOT NULL'],
                ['radio', 'listshow', '是否在列表显示', '', ['不在列表显示', '显示'], 0],
                ['radio', 'ifsearch', '是否作为内容搜索选项', '', ['否', '是'], 0],
                ['radio', 'ifmust', '是否属于必填项', '', ['可不填', '必填'], 0],
                ['text', 'about', '描述说明'],
                ['text', 'list', '排序值'],
                ['text', 'nav', '分组名[:对于不重要的字段,你可以添加组名,让他在更多那里显示]'],
                
        ];
        
        $this->tab_ext = [
                'js_file'=>'field',
                'warn_msg'=>'字段名称可随意修改，但字段变量名创建好后，就不要修改，不然其它地方的调用会受影响，只能字母或数字或下画线，并且只能字母开头',
                'page_title'=>'表单字段管理',
                'trigger'=>[
                        ['type', 'checkbox,radio,select', 'options'],
                ],
        ];
        
    }
    
    //添加字段
    public function add($mid=0)
    {
        
        $this->form_items[] = ['hidden','mid',$mid]; //记录一下往哪个模型加字段
        
        // 保存数据
        if ($this->request->isPost()) {
        
            // 表单数据
            $data = $this->request->post();
            
            if(!empty($this->validate)){
                // 验证
                $result = $this->validate($data, $this->validate);
                if(true !== $result) $this->error($result);
            }
            
            // 更新字段信息
            if ($this->model->newField($data['mid'],$data)) {
                if ( $this->saveAddContent() ) {
                    $this->success('字段添加成功', auto_url('index',['mid'=>$data['mid']]));
                }
            }
            $this->error('字段创建失败');
        }
        
        
        return $this->addContent();
    }
    
    //列出模型下的所有字段
    public function index($mid=0)
    {
        if(empty($mid)){
            $this->error('ID不存在');
        }
        $this->tab_ext['top_button']=[
                [
                        'type'=>'add',
                        'title'=>'添加字段',
                        'href' => auto_url('add', ['mid' => $mid])
                ],
                [
                        'type'=>'back',
                        'title'=>'返回模型管理',
                        'href' => auto_url('module/index')
                ],
        ];
       
		$data = self::getListData(['mid'=>$mid],['list'=>'desc']);
        return $this->getAdminTable($data);
    }
    
    //修改字段
    public function edit($id = null)
    {
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            
            if(!empty($this->validate)){
                // 验证
                $result = $this->validate($data, $this->validate);
                if(true !== $result) $this->error($result);
            }
            
            // 更新字段信息
            if ($this->model->updateField($id,$data)) {
                if ( $this->saveEditContent() ) {
                    $mid = $this->model->where('id',$id)->value('mid');
                    $this->success('字段修改成功', auto_url('index',['mid'=>$mid]) );
                }
            }
            $this->error('字段更新失败');
        }
        
        if(empty($id)) $this->error('缺少参数');
        
        $info = $this->getInfoData($id);
        
        return $this->editContent($info);
    }
    
    public function delete($ids = null)
    {
        
        $field_array = $this->getInfoData( $ids );
        $this->model->deleteField($field_array);
        if( $this->deleteContent($ids) ){
            $this->success('删除成功', auto_url('index',['mid'=>$field_array['mid']]) );
        }else{
            
            $this->error('删除失败');
        }
    }
    
}