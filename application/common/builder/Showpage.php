<?php

namespace app\common\builder;

//use app\common\builder\ZBuilder;
use think\Exception;
use app\common\builder\aside\Builder;

/**
 * 表单构建器
 * @package app\common\builder\type
 */
class Showpage extends Form
{

    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->_template = APP_PATH. 'common/builder/showpage/layout.htm';
    }


}
