<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\model\Config AS ConfigModel;
use plugins\config_set\model\Group AS GroupModel;
use app\common\traits\AddEditList;
use think\Cache;


class Setting extends AdminBase
{
    use AddEditList;
    protected $validate = '';
    protected $model;
    protected $form_items = [];
    protected $list_items;
    protected $tab_ext;
    protected $group = 'base';

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new ConfigModel();
        $this->tab_ext = [ 'help_msg'=>'系统参数配置',];
    }
    
    public function clearcache(){
        delete_dir(RUNTIME_PATH.'temp');
        Cache::clear();
        
        $this->success('清除成功','index/welcome');
    }
    
    /**
     * 设置分组导航
     * @param unknown $group
     */
    protected function setNav($group){
        $this->tab_ext = [
                'nav'=>[
                        GroupModel::getNav(true),   //分组导航
                        $group
                ],
        ];
    }
    
    
    /**
     * 参数设置
     * @param string $group 分组ID
     * @return mixed|string
     */
    public function index($group='1')
    {
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            if( $this->model->save_group_data($data,$data['group']?$data['group']:$group) ){
                $this->success('修改成功');
            }            
        }
        
        //某分类下的所有参数选项
        $list_data = empty($group) ? [] : $this->model->getListByGroup($group);
        
        //创建表格
        $this->setNav($group);
        $tab_list = [
                ['hidden','group',$group]
        ];
        foreach ($list_data as $key => $rs) {
            empty($rs['form_type']) && $rs['form_type'] = 'text';
            empty($rs['title']) && $rs['title'] = '未命名的字段：'.$rs['c_key'];
            if( in_array($rs['form_type'],['radio','select','checkbox','checkboxtree']) && !empty($rs['options']) ){
                if(preg_match('/^[a-z]+(\\\[_a-z]+)+@[_a-z]+/is',$rs['options'])){
                    list($class_name,$action,$params) = explode('@',$rs['options']);
                    if(class_exists($class_name)&&method_exists($class_name, $action)){
                        $obj = new $class_name;
                        $_params = $params ? json_decode($params,true) : [] ;
                        
                        //$rs['options'] = $obj->$action();
                        $rs['options'] = call_user_func_array([$obj, $action], $_params);
                    }
                }else{
                    $rs['options'] = str_array($rs['options']);
                }
            }
            $tab_list[]=[
                $rs['form_type'],
                $rs['c_key'],
                $rs['title'],
                $rs['c_descrip'],
                $rs['options'],
                    '',
                    '',
                    $rs['htmlcode'],
                    
            ];
        }

        $this->form_items = $tab_list;
        
        $data = [];
        foreach($list_data AS $rs){
            $data[$rs['c_key']] = $rs['c_value'];
        }
        		
		return $this->editContent($data);
    }

}
