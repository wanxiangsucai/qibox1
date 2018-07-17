<?php
namespace plugins\marketing\admin;

use app\common\controller\AdminBase;

use app\common\traits\AddEditList;

use plugins\marketing\model\RmbInfull as RmbInfullModel;

//人民币充值
class RmbInfull extends AdminBase
{
	
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
	        'page_title'=>'人民币充值管理',
	        'top_button'=>[['type'=>'delete']],
	        'right_button'=>[ ['type'=>'delete']],
// 	        'hidden_edit'=>true,	
	];
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new RmbInfullModel();
		$this->list_items = [
				['uid', '用户名', 'username',],
                ['money', '充值金额(元)', 'text'],
				['ifpay', '支付与否', 'callback', function($value){
                    return $value>0 ? '<span style="color:red">付款成功</span>' : '<span style="color:blue">付款失败</span>';
                }],
				['posttime', '支付时间', 'datetime'],
				['numcode', '支付单号', 'text'],
                ['banktype', '支付方式', 'text'],
				//['bank1', '收款帐号', 'text'],
                
			];
	}
	
	protected function getOrder($extra_order = '', $before = false)
	{
	    $order = parent::getOrder($extra_order, $before);
	    
	    if($order == ''){
	        return ' id desc ';
	    }else{
	        return $order;
	    }
	}
}
