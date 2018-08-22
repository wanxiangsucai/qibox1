<?php
namespace app\shop\member;

use app\common\controller\member\C;
use app\shop\traits\Content AS TraitsContent;

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
	
	public function edit($id)
	{
	    return parent::edit($id);
	}
	
	public function delete($ids)
	{
	    return parent::delete($ids);
	}
}
