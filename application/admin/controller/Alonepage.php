<?php
namespace app\admin\controller;

use app\common\controller\AdminBase; 

use app\common\traits\AddEditList;

use app\admin\model\Alonepage as AlonepageModel;

class Alonepage extends AdminBase
{
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [
					['text', 'title', '标题'],
					['text', 'descrip', '分享描述'],
					['jcrop', 'picurl', '分享图片'],
	                ['text', 'tpl_main', '模板[:只能是绝对路径]',''],
	                //['image', 'picurl', '分享图片'],	
					['radio', 'ifclose', '是否启用', '', [1 => '文字链接', 0 => '禁用'], 1],
					['ueditor', 'content', '内容'],
					
				];
	protected $list_items;
	protected $tab_ext = [
			'page_title'=>'单篇文章独立页管理',
	        'top_button'=>[
	                ['type'=>'add'],
	                ['type'=>'delete'],
	        ]
	];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new AlonepageModel();
		$this->list_items = [				 
				['title', '单独页名称', 'link',iurl('index/alonepage/index',['id'=>'__id__']),'_target'],                
				['posttime', '发布时间', 'datetime'],				
			];
	}
}
