<?php

namespace app\common\controller;


/**
 * 前台总控制器
 */
class IndexBase extends Base
{
    /**
     * 初始化
     */
    protected function _initialize()
    {
        parent::_initialize();
        
        //自动模板的布局母模板
        $this->assign('auto_tpl_base_layout', APP_PATH.'member/view/default/layout.htm');
      
        //这里设置无效
        /*
        if($this->route[0]=='index'||empty($this->route[0])){
            
            if($this->route[1]=='plugin'){
                config('template.view_path', ROOT_PATH.'plugins/'. input('plugin_name'). '/view/index/default/');
            }else{
                
            }
            
        }else{
            //config('template.view_path', APP_PATH. $this->route[0]. '/view/index/default/');
        }
        */
        
        
    }
}
