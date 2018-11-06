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
            [
                    'c_key'=>'getout_percent_money',
                    'title'=>'提现手续费',
                    'c_value'=>'0',
                    'options'=>"",
                    'c_descrip'=>'0即不收手续费,0.01即收取1个点的手续费',
                    'form_type'=>'text',
                    'ifsys'=>0,
                    'list'=>0,
            ],
            [
                    'c_key'=>'getout_need_join_mp',
                    'title'=>'是否要求先关注公众号才能提现',
                    'c_value'=>'',
                    'form_type'=>'radio',
                    'options'=>"0|不要求\r\n1|要求先关注公众号",
                    'ifsys'=>0,
                    'list'=>-1,
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

