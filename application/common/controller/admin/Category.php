<?php
namespace app\common\controller\admin;

use app\common\controller\AdminBase;
use app\common\traits\AdminSort;

//辅栏目管理
abstract class Category extends AdminBase
{
    use AdminSort;
    
    protected $validate = 'Category';
    protected $model;
    protected $m_model;
    protected $form_items;
    
    protected $list_items;
    protected $tab_ext;
    
    protected function _initialize()
    {
        parent::_initialize();        
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'category');
        $this->s_model = get_model_class($dirname,'sort');
        $this->m_model = get_model_class($dirname,'module');
        
        $this->set_config();
    }
    
    protected function check_tpl($value=''){
        if($value!='' && !is_file(TEMPLATE_PATH.'index_style/'.$value)){
            $this->error('模板不存在，或者路径有误！请确认把模板放在以下目录'.TEMPLATE_PATH.'index_style/'.$value);
        }
    }
    
    protected function set_config(){
        $this->list_items = [
                ['name', '辅栏目名称', 'link',iurl('category/index',['fid'=>'__id__']),'_blank'],
                //['mid', '所属模型', 'select2',$this->m_model->getTitleList()],
        ];
        
        $msg = '请把模板放在此目录下: '.TEMPLATE_PATH.'index_style/ 然后输入相对路径,比如 default/abc.htm';
        $this->form_items = [
            ['textarea', 'name', '辅栏目名称','同时添加多个栏目的话，每个换一行'],
            ['select', 'pid', '归属上级分类','不选择，则为顶级分类',$this->model->getTreeTitle()],
            //['select', 'mid', '所属模型','创建后不能随意修改',$this->m_model->getTitleList()],
            ['text', 'wap_list', 'wap列表页模板(可留空，将用默认的)',$msg],
            ['text', 'pc_list', 'PC列表页模板(可留空，将用默认的)',$msg],
        ];
        
        $this->tab_ext = [
                'page_title'=>'辅栏目管理',
                'top_button'=>[
                        [
                                'type' => 'add',
                                'title' => '创建辅栏目',
                                'icon'  => 'fa fa-fw fa-th-list',
                                'class' => '',
                                'href'  => auto_url('add')
                        ],
                ],
                'right_button'=>[
                        [
                                'title' => '管理内容',
                                'icon'  => 'fa fa-fw fa-file-text-o',
                                'href'  => auto_url('info/index', ['cid' => '__id__'])
                        ],
                        ['type'=>'delete'],
                        ['type'=>'edit'],
                       /* [
                                'title' => '添加内容',
                                'icon'  => 'glyphicon glyphicon-plus',
                                'href'  => auto_url('content/add', ['fid' => '__id__'])
                        ],*/
                ],
        ];
    }
    
    
    public function index() {
        $array = \app\common\field\Table::get_list_field(-3) ?: [];
        $this->list_items = array_merge(
                $this->list_items,
                $array
                ); 
        $listdb = $this->getListData($map = [], $order = '');
        return $this -> getAdminTable($listdb);
    }
    
    public function add() {
        if($this->request->isPost()){            
            $data = $this -> request -> post();
            $this->check_tpl($data['pc_list']);
            $this->check_tpl($data['wap_list']);
            $data = \app\common\field\Post::format_all_field($data,-3); //对一些特殊的字段进行处理,比如多选项,以数组的形式提交的
            $this -> request -> post($data);
        }
        
        $form_field =  \app\common\field\Form::get_all_field(-3);
        if ($form_field) {  //把用户自定义字段,追加到基础设置那里
            $this->form_items = array_merge($this->form_items,$form_field);
        }
        
        //联动字段,比如点击哪项就隐藏或者显示哪一项
        $this->tab_ext['trigger'] = \app\common\field\Form::getTrigger(-3);
        
        return $this -> addContent();
    }
    
    public function edit($id = null)
    {
        if($this->request->isPost()){
            if (!table_field(config('system_dirname').'_category','wap_list')) {
                into_sql("ALTER TABLE `qb_".config('system_dirname')."_category` ADD `wap_list` VARCHAR( 100 ) NOT NULL COMMENT 'WAP列表页模板',ADD `pc_list` VARCHAR( 100 ) NOT NULL COMMENT 'PC列表页模板';");
            }
            $data = $this -> request -> post();
            
            $this->check_tpl($data['pc_list']);
            $this->check_tpl($data['wap_list']);
            
            $data = \app\common\field\Post::format_all_field($data,-3); //对一些特殊的字段进行处理,比如多选项,以数组的形式提交的
            
            if ($this -> model -> update($data)) {
                $this -> success('修改成功', 'index');
            } else {
                $this -> error('修改失败');
            }
        }
        
        if(empty($id)) $this->error('缺少id');
        
        if (empty($this->form_items)) {
            $this->form_items = [
                    ['text', 'name', '辅栏目名称'],
                    ['select', 'pid', '归属上级分类','不选择，则为顶级分类',$this->model->getTreeTitle($id)],
            ];
        }
        
		$info = $this->getInfoData($id);

        $form_field =  \app\common\field\Form::get_all_field(-3,$info);
        if ($form_field) {  //把用户自定义字段,追加到基础设置那里
            $this->form_items = array_merge($this->form_items,$form_field);
        }
        
        //联动字段,比如点击哪项就隐藏或者显示哪一项
        $this->tab_ext['trigger'] = \app\common\field\Form::getTrigger(-3);

        return $this->editContent($info);
    }
}