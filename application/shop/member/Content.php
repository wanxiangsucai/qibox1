<?php
namespace app\shop\member;

use app\common\controller\member\C;


class Content extends C
{	
	public function index($fid=0,$mid=0)
	{
	    $listdb = $this->model->getListByUid($this->user['uid']);
	    $pages = $listdb->render();
	    $this->assign('listdb',$listdb);
	    $this->assign('pages',$pages);
	    return $this->fetch();
	}
	
	public function edit($id)
	{
	    return parent::edit($id);
	}
	
	public function delete($ids)
	{
	    return parent::delete($ids);
	}
}
