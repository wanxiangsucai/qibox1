<?php
namespace app\shop\admin;

use app\common\controller\admin\Setting AS _Setting;

//频道参数设置
class Setting extends _Setting
{
    protected function getSysId(){
        $array = modules_config(config('system_dirname'));
        return $array['id'];
    }
    
    /**
     * 参数设置
     * {@inheritDoc}
     * @see \app\common\controller\admin\Setting::index()
     */
    public function index($group=null){
        return parent::index($group);
    }
}

