<?php

namespace app\common\builder;

//use app\admin\model\Menu;
//use app\common\builder\ZBuilder;
//use app\user\model\Group;
//use think\Cache;

/**
 * 表格构建器
 * @package app\common\builder\table
 */
class Listpage extends Table
{
 
    
    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->_template   = APP_PATH. 'common/builder/listpage/layout.htm';

    }

    
}