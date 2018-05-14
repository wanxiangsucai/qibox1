<?php
namespace app\shop\index;

//频道主页
class Index extends Content
{
	public function index(){
// 	    $list = Db::name('rmb_consume')->where([])->paginate();
// 	    print_r($list->render());exit;
	    $mid = $this->m_model-> getId();
	    $this->assign('mid',$mid);
	    return $this->fetch();
	}
	
}
