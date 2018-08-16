<?php
namespace plugins\marketing\admin;

use app\common\controller\AdminBase;

use app\common\traits\AddEditList;

use plugins\marketing\model\RmbConsume as RmbConsumeModel;

//人民币日志
class RmbConsume extends AdminBase
{
	
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
	        'page_title'=>'用户人民币消费记录与充值记录',
	        'top_button'=>[ ['type'=>'delete']],
	        'right_button'=>[ ['type'=>'delete']],
// 	        'hidden_edit'=>true,	
	];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new RmbConsumeModel();
		$this->list_items = [
                ['uid', '用户名','username'],
                ['money', '数额', 'text'],
				['money', '类型', 'callback', function($value){
                    return $value>0 ? '<span style="color:red">收入</span>' : '<span style="color:blue">支出</span>';
                }],
				['posttime', '时间', 'datetime'],
                ['about', '事项', 'text'],                
			];
		
		$this->tab_ext['search'] = [
		        'uid'=>'用户UID',
		];
	}
	

}
