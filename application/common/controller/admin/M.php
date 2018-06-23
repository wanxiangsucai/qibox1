<?php
namespace app\common\controller\admin;

use app\common\controller\AdminBase;
use app\common\traits\AddEditList;

abstract class M extends AdminBase
{
    use AddEditList;
    
    protected $validate = 'Module';
    protected $model;
    protected $form_items;
    protected $list_items;
    protected $tab_ext;
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'module');
        $this->set_config();
    }
    
    protected function set_config(){
        
        $this->form_items = [
                ['text', 'title', '模型名称'],
                ['text', 'layout', '模板路径','一般请留空,否则必须放在/template/index_style/目录下,然后补全路径:比如:“qiboxxx/cms/content/list2.htm”'],                
        ];
        
        $this->list_items = [
                ['title', '模型名称', 'text'],
                //['keyword', '关键字', 'text'],
                ['create_time', '创建时间', 'text'],
        ];
        
        $this->tab_ext = [
                'page_title'=>'模型管理',
                'top_button'=>[
                        [
                                'title' => '创建模型',
                                'icon'  => 'fa fa-fw fa-cubes',
                                'class' => 'btn btn-primary',
                                'href'  => auto_url('add')
                        ],
                ],
                'right_button'=>[
                        [
                                'title' => '管理内容',
                                'icon'  => 'fa fa-fw fa-file-text-o',
                                'href'  => auto_url('content/index', ['mid' => '__id__'])
                        ],
                        [
                                'title' => '发布内容',
                                'icon'  => 'glyphicon glyphicon-plus',
                                'href'  => auto_url(config('post_need_sort')?'content/postnew':'content/add', ['mid' => '__id__'])
                        ],
                        [
                                'title' => '字段管理',
                                'icon'  => 'fa fa-fw fa-table',
                                'href'  => auto_url('field/index', ['mid' => '__id__'])
                        ],
                        ['type'=>'delete',],
                        ['type'=>'edit',],
                ],
        ];
    }
    
    public function add(){
        
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            
            if ($result = $this->saveAddContent()) {
                
                if ($this->model->createTable($result->id,$data['title'])) {
                    
                    $this->success('模型创建成功', auto_url('index'));
                }else{
                    $this->model->where('id','=',$result->id)->delete();
                    $this->error('模型数据表创建失败');
                }
            }
            
            $this->error('模型创建失败');
        }
        
        return $this->addContent();
        
    }
    
    public function delete($ids = null)
    {
        //删除对应的模型分表
        $this->model->deleteModule($ids);
        
        //模块表删除记录
        if( $this->deleteContent($ids) ){
            $this->success('卸载成功', auto_url('index') );
        }else{
            
            $this->error('卸载失败');
        }
    }
    
}