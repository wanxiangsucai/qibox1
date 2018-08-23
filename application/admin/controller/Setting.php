<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\model\Config AS ConfigModel;
use plugins\config_set\model\Group AS GroupModel;
use app\common\traits\AddEditList;
use think\Cache;
use think\Db;


class Setting extends AdminBase
{
    use AddEditList;
    protected $validate = '';
    protected $model;
    protected $form_items = [];
    protected $list_items;
    protected $tab_ext;
    protected $group = 'base';
    protected $_config = [];    //系统强制要补上的字段
    protected $config = [];    //频道或插件强制要补上的字段

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new ConfigModel();
        $this->tab_ext = [ 'help_msg'=>'系统参数配置',];
        $this->add_module_config();
    }
    
    /**
     * 模块里要强制补上的配置参数
     */
    protected function add_module_config(){
        if ($this->config || defined('IN_PLUGIN') || empty(config('system_dirname'))) {
            return ;
        }
        $this->config = [
                [
                        'c_key'=>'module_pc_index_template',
                        'title'=>'频道主页PC版风格模板',
                        'c_descrip'=>'请把模板放在此目录下: /template/index_style/ 然后输入相对路径,比如 default/abc.htm',
                        'form_type'=>'text',
                        'ifsys'=>0,
                        'list'=>-1,
                ],
                [
                        'c_key'=>'module_wap_index_template',
                        'title'=>'频道主页WAP版风格模板',
                        'c_descrip'=>'请把模板放在此目录下: /template/index_style/ 然后输入相对路径,比如 default/abc.htm',
                        'form_type'=>'text',
                        'ifsys'=>0,
                        'list'=>-1,
                ],
        ];
    }
    
    /**
     * 补全系统强制要加上的字段
     * @param number $group 分组ID
     */
    protected function add_config($group=0){
        if (empty($group)) {
            return ;
        }
        $gdb = GroupModel::where('id',$group)->find();
        if($gdb['sys_id']==0){                  //分组属于系统,不属于任何频道或插件
            $array = $this->_config;
        }else{                                          //分组属于频道或插件
            $array = $this->config;
        }
        
        foreach ($array AS $rs){
            $realut = ConfigModel::where(['c_key'=>$rs['c_key'],'sys_id'=>$gdb['sys_id'],])->find();
            if(empty($realut)){     //数据表中不存在强制要加的字段,就强制补上
                $rs['sys_id'] = $gdb['sys_id'];
                $rs['type'] = $group;
                $rs['ifsys'] = $gdb['sys_id']>0 ? 0 : $rs['ifsys'];
                ConfigModel::create($rs);
            }
        }
    }
    
    /**
     * 清除缓存
     */
    public function clearcache(){
        delete_dir(RUNTIME_PATH.'temp');
        delete_dir(RUNTIME_PATH.'log');
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
        
        $this->add_config($group);      //补全字段
        
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
                if(preg_match('/^[a-z]+(\\\[_a-z]+)+@[_a-z]+/i',$rs['options'])){   //执行类
                    list($class_name,$action,$params) = explode('@',$rs['options']);
                    if(class_exists($class_name)&&method_exists($class_name, $action)){
                        $obj = new $class_name;
                        if ($params!='') {
                            $_params = json_decode($params,true)?:fun('label@where',$params);
                        }else{
                            $_params = [];
                        }
                        //$_params = $params!='' ? json_decode($params,true) : [] ;
                        //$rs['options'] = $obj->$action();
                        $rs['options'] = call_user_func_array([$obj, $action], isset($_params[0])?$_params:[$_params]);
                    }
                }elseif(preg_match('/^([\w]+)@([\w]+),([\w]+)/i',$rs['options'])){
                    list($table_name,$fields,$params) = explode('@',$rs['options']);
                    preg_match('/^qb_/i',$table_name) && $table_name = str_replace('qb_', '', $table_name);
                    if ($params!='') {
                        $map = json_decode($params,true)?:fun('label@where',$params);
                    }
                    is_array($map) || $map = [];
                    $rs['options'] = Db::name($table_name)->where($map)->column($fields);
                }else{
                    $rs['options'] = str_array($rs['options'],"\n");    //后台设置的下拉,多选,单选,都是用换行符做分割的
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
        $this->mid = $group;    //纯属为了模板考虑的
		return $this->editContent($data);
    }

}
