<?php
namespace app\shop\member;

use app\common\controller\member\C;
use app\shop\traits\Content AS TraitsContent;

class Content extends C
{	
    use TraitsContent;
    
    /**
     * 我发布的内容
     * {@inheritDoc}
     * @see \app\common\controller\member\C::index()
     */
	public function index($fid=0,$mid=0)
	{
	    if(count(model_config())>1&&!$mid&&!$fid){
	        return parent::listall();
	    }else{
	        return parent::index($fid,$mid);
	    }	    
	}
	
	/**
	 * 发布信息
	 * {@inheritDoc}
	 * @see \app\common\controller\member\C::add()
	 */
	public function add($fid=0,$mid=0){
	    return parent::add($fid,$mid);
	}
	
	/**
	 * 修改信息
	 * {@inheritDoc}
	 * @see \app\common\controller\member\C::edit()
	 */
	public function edit($id){
	    return parent::edit($id);
	}
	
	/**
	 * 删除信息
	 * {@inheritDoc}
	 * @see \app\common\controller\member\C::delete()
	 */
	public function delete($ids)
	{
	    return parent::delete($ids);
	}
	
}
