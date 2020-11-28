<?php
namespace plugins\area\admin;

use app\common\controller\admin\Setting AS _Setting;


class Setting extends _Setting
{    
    /**
     * 参数设置
     * {@inheritDoc}
     * @see \app\common\controller\admin\Setting::index()
     */
    public function index($group=null){
        //$this->tab_ext['area'] = 1; //启用地区
        return parent::index($group);
    }
}

