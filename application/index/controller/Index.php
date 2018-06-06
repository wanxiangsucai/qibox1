<?php
namespace app\index\controller;

use app\common\controller\IndexBase;


class Index extends IndexBase
{
    public function index()
    {
//         if(input('wxapp')==1||get_cookie('wxapp')){
//             set_cookie('wxapp', 1);
//             cache('web_menu_wapfoot',1);
//             $this->redirect(url('cms/index/index'),301);
//         }
        if( ($sysname = $this->webdb['set_module_index'])!='' ){
            //return $this->redirect($sysname.'/index/index');
            if(is_dir(APP_PATH.$sysname)){
                $this->redirect(url($sysname.'/index/index'),301);
            }            
        }
		return $this->fetch('../index');
    }
    
    public function test(){
    }
    

    
}

