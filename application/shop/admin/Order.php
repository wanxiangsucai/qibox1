<?php
namespace app\shop\admin;

use app\common\controller\AdminBase; 
use app\common\traits\AddEditList;
use app\shop\model\Order as OrderModel;

class Order extends AdminBase
{
    use AddEditList;
    protected $model;
    protected $list_items;
    protected $form_items = [
            ['text', 'linkman', '联系人'],
            ['text', 'telphone', '联系电话'],
            //['image', 'picurl', '分享图片'],
            ['radio', 'ifolpay', '支付类型', '', [1 => '在线付款', 0 => '货到付款'], 1],
    ];
    protected $tab_ext = [
            'page_title'=>'订单记录',
            'top_button'=>[ ['type'=>'delete']],
    ];
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new OrderModel();     
        
        $this->list_items = [
                ['linkman', '联系人', 'text'],
                ['create_time', '下单日期', 'text'],
                 
        ];
    }

    
    
}
