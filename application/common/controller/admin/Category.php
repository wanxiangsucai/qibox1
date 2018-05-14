<?php
namespace app\common\controller\admin;

use app\common\controller\AdminBase;
use app\common\traits\AdminSort;

//辅栏目
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
    
    protected function set_config(){
        $this->list_items = [
                ['name', '辅栏目名称', 'link',iurl('category/index',['fid'=>'__id__']),'_blank'],
                //['mid', '所属模型', 'select2',$this->m_model->getTitleList()],
        ];
        
        $this->form_items = [
                
                ['textarea', 'name', '辅栏目名称','同时添加多个栏目的话，每个换一行'],
                ['select', 'pid', '归属上级分类','不选择，则为顶级分类',$this->model->getTreeTitle()],
                //['select', 'mid', '所属模型','创建后不能随意修改',$this->m_model->getTitleList()],
        ];
        
        $this->tab_ext = [
                'page_title'=>'辅栏目管理',
                'top_button'=>[
                        [
                                'title' => '创建辅栏目',
                                'icon'  => 'fa fa-fw fa-th-list',
                                'class' => 'btn btn-primary',
                                'href'  => auto_url('add')
                        ],
                ],
                'right_button'=>[
                        [
                                'title' => '管理内容',
                                'icon'  => 'fa fa-fw fa-file-text-o',
                                'href'  => auto_url('info/index', ['cid' => '__id__'])
                        ],
                       /* [
                                'title' => '添加内容',
                                'icon'  => 'glyphicon glyphicon-plus',
                                'href'  => auto_url('content/add', ['fid' => '__id__'])
                        ],*/
                ],
        ];
    }
    

    
    public function edit($id = null)
    {
        $this->form_items = [
                
                ['text', 'name', '辅栏目名称'],
                ['select', 'pid', '归属上级分类','不选择，则为顶级分类',$this->model->getTreeTitle($id)],
               // ['select', 'mid', '所属模型','创建后不能随意修改',$this->m_model->getTitleList()],
        ];
        
        if(empty($id)) $this->error('缺少参数');
        
        $info = $this->getInfoData($id);
        
        return $this->editContent($info);
    }
}