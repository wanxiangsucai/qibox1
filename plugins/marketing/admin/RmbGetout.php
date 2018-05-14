<?php
namespace plugins\marketing\admin;

use app\common\controller\AdminBase;

use app\common\traits\AddEditList;

use plugins\marketing\model\RmbGetout as RmbGetoutModel;

class RmbGetout extends AdminBase
{
	
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
			'page_title'=>'会员提现管理',
	        'top_button'=>[ ['type'=>'delete']],
	        'hidden_edit'=>true,	
	];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new RmbGetoutModel();
		$this->list_items = [
				 
				['money', '提现金额', 'text'],                
				['ifpay', '支付与否', 'yesno'],
				['username', '会员帐号', 'text'],
				['postitme', '提交日期', 'datetime'],
				['', '操作员', 'text'],
				
			];
	}	

}
