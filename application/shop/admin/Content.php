<?php
namespace app\shop\admin;

use app\common\controller\admin\C;


class Content extends C
{
    protected function deleteOne($id=0,$mid=0){
        hook_listen('shop_delete_begin',$id);
        $result = parent::deleteOne($id,$mid);
        if($result){
            hook_listen('shop_delete_end',$id);
        }
        return $result;
    }
}
