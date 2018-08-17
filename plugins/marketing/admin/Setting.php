<?php
namespace plugins\marketing\admin;

use app\common\controller\admin\Setting AS _Setting;


class Setting extends _Setting
{    
    protected $config = [
            [
                    'c_key'=>'min_getout_money',
                    'title'=>'最低提现金额',
                    'c_value'=>'50',
                    'options'=>"",
                    'c_descrip'=>'',
                    'form_type'=>'money',
                    'ifsys'=>0,
                    'list'=>0,
            ],
    ];
    
    /**
     * 参数设置
     * {@inheritDoc}
     * @see \app\common\controller\admin\Setting::index()
     */
    public function index($group=null){
        return parent::index($group);
    }
}

