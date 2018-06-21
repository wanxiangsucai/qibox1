<?php
namespace app\common\controller\admin;

use app\common\controller\AdminBase;
use app\common\traits\AdminSort;

abstract class S extends AdminBase
{
    use AdminSort;
    
    protected $validate = 'Sort';
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
        $this->model = get_model_class($dirname,'sort');
        $this->m_model = get_model_class($dirname,'module');
        $this->set_config();
    }
    
    protected function set_config(){
        $this->list_items = [
                ['name', '栏目名称', 'link',iurl('content/index',['fid'=>'__id__']),'_blank'],
                ['mid', '所属模型', 'select2',$this->m_model->getTitleList()],
                ['list', '排序值', 'text.edit'],
        ];
        
        $this->form_items = [                
                ['text', 'name', '栏目名称'],
                ['select', 'pid', '归属上级分类','不选择，则为顶级分类',$this->model->getTreeTitle()],
                ['select', 'mid', '所属模型','创建后不能随意修改',$this->m_model->getTitleList(),1],
        ];
        
        $this->tab_ext = [
                'page_title'=>'栏目管理',
                'top_button'=>[
                        [
                                'title' => '创建栏目',
                                'icon'  => 'fa fa-fw fa-th-list',
                                'class' => 'btn btn-primary',
                                'href'  => auto_url('add')
                        ],
                ],
                'right_button'=>[
                        [
                                'title' => '管理内容',
                                'icon'  => 'fa fa-fw fa-file-text-o',
                                'href'  => auto_url('content/index', ['fid' => '__id__'])
                        ],
                        [
                                'title' => '发布内容',
                                'icon'  => 'glyphicon glyphicon-plus',
                                'href'  => auto_url('content/add', ['fid' => '__id__'])
                        ],                        
                        ['type'=>'delete'],
                        ['type'=>'edit'],
                ],
        ];
    }
    
    protected function get_tpl($data){
        foreach($data['templates'] AS $key=>$value){
            if(empty($value)){
                unset($data['templates'][$key]);
            }elseif(!is_file(TEMPLATE_PATH.'index_style/'.$value)){
                $this->error('模板不存在，或者路径有误！请确认把模板放在以下目录'.TEMPLATE_PATH.'index_style/'.$value);
            }
        }
        if(!empty($data['templates'])){
            return serialize($data['templates']);
        }
    }
    
    /**
     * 修改栏目信息
     * @param unknown $id
     * @return mixed|string
     */
    public function edit($id = null)
    {
        if($this->request->isPost()){
            $data = $this -> request -> post();            
            if (!empty($this -> validate)) {    // 验证
                //$result = $this -> validate($data, $this -> validate);
                //if (true !== $result) $this -> error($result);
            } 
            
            $data['allowpost'] = implode(',', $data['allowpost']);  //允许发布内容的用户组
            $data['allowview'] = implode(',', $data['allowview']);  //允许查看内容的用户组
            $data['template'] = $this->get_tpl($data);                  //栏目自定义模板
            
            
            if ($this -> model -> update($data)) {
                $this -> success('修改成功', 'index');
            } else {
                $this -> error('修改失败');
            }
        }
        $this->form_items = [];
        
        $msg = '请把模板放在此目录下: '.TEMPLATE_PATH.'index_style/ 然后输入相对路径,比如 default/abc.htm';
        
        $this -> tab_ext['group'] = [
                '基础设置'=>[
                        ['text', 'name', '栏目名称'],
                        ['select', 'pid', '归属上级分类','不选择，则为顶级分类',$this->model->getTreeTitle($id)],
                        //['select', 'mid', '所属模型','创建后不能随意修改',$this->m_model->getTitleList()],
                        ['icon', 'logo', '图标',],
                        ['checkbox', 'allowpost', '允许发布内容的用户组','全留空,则不作限制',getGroupByid()],
                ],
                '模板设置'=>[
                        ['text', 'templates[waplist]', 'wap列表页模板[:可留空，将用默认的。点击右边图片选择模板]',$msg,'',['','<i class="fa fa-plus-square" onclick="alert(9)"></i>']],
                        ['text', 'templates[wapshow]', 'wap内容页模板[:可留空，将用默认的。点击右边图片选择模板]',$msg,'',['','<i class="fa fa-plus-square" onclick="alert(9)"></i>']],
                        ['text', 'templates[pclist]', 'PC列表页模板[:可留空，将用默认的。点击右边图片选择模板]',$msg,'',['','<i class="fa fa-plus-square" onclick="alert(9)"></i>']],
                        ['text', 'templates[pcshow]', 'PC内容页模板[:可留空，将用默认的。点击右边图片选择模板]',$msg,'',['','<a class="fa fa-plus-square pop" href="/"></a>']],
                        
                ],
                'SEO优化设置'=>[
                        ['text', 'seo_title', 'SEO标题'],
                        ['text', 'seo_keywords', 'SEO关键字'],
                        ['text', 'seo_description', 'SEO描述'],
                ],
        ];
        
        if(empty($id)) $this->error('缺少参数');
        
        $info = $this->getInfoData($id);
        
        if($info['template']){
            $array = unserialize($info['template']);
            
            if (is_array($array)){
                $info = array_merge($info,['templates'=>$array]);
            }
        }
        
        return $this->editContent($info);
    }
    
    public function delete($ids = null)
    {
        if( $this->deleteContent($ids) ){
            $this->success('删除成功', 'index');
        }else{
            
            $this->error('删除失败');
        }
    }
    
}