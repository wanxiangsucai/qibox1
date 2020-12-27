<?php
namespace app\common\controller\index;

use app\common\controller\IndexBase;

//频道主页
class Index extends IndexBase
{
    /**
     * 频道主页
     * @return mixed|string
     */
    public function index(){
        define('PAGE_TYPE', 'index');
        $template = '';
        $this->assign('mid',current(model_config())['id']);
        
        //频道自定义模板
        if (IN_WAP===true) {
            if ($this->webdb['module_wap_index_template']!='') {
                $template = TEMPLATE_PATH.'index_style/'.$this->webdb['module_wap_index_template'];
                if (!is_file($template)) {
                    $template = '';
                }
            }            
        }elseif($this->webdb['module_pc_index_template']) {
            $template = TEMPLATE_PATH.'index_style/'.$this->webdb['module_pc_index_template'];
            if (!is_file($template)) {
                $template = '';
            }
        }
        $this->get_module_layout('index');   //重新定义布局模板
        return $this->fetch($template?:'index');
    }
    

//     public function sort($fid=0,$mid=0){
//         define('PAGE_TYPE', 'sort');
//         $mid && $this->assign('mid',$mid);
//         $fid && $this->assign('fid',$fid);
//         return $this->fetch();
//     }
    
    /**
     * 空方法 ,自适应
     * @param unknown $action
     * @return mixed|string
     */
    public function _empty($action)
    {
        if (!preg_match('/^[\w]+$/i', $action)) {
            $this->error('方法名有误!');
        }
        $mid = input('mid');
        $fid = input('fid');
        $mid && $this->assign('mid',intval($mid));
        $fid && $this->assign('fid',intval($fid));
        return $this->fetch();
    }
}
