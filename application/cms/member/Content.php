<?php
namespace app\cms\member;

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
	
	/**
	 * 采集公众号的文章
	 * @param number $fid
	 * @return mixed|string
	 */
	public function copynews($fid=0){
	    $this->assign('fid',$fid);
	    return $this->fetch();
	}
	
}
