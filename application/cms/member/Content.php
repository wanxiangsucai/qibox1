<?php
namespace app\cms\member;

use app\common\controller\member\C;
use app\cms\traits\Content AS TraitsContent;

class Content extends C
{	
    use TraitsContent;
	public function index($fid=0,$mid=0)
	{
	    if(count(model_config())>1&&!$mid&&!$fid){
	        return parent::listall();
	    }else{
	        return parent::index($fid,$mid);
	    }
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
	
	public function edit($id)
	{
	    return parent::edit($id);
	}
	
	public function delete($ids)
	{
	    return parent::delete($ids);
	}
	
}
