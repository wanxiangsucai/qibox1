<?php
namespace app\common\fun;

class Sort{
    /**
     * 根据id获取栏目的名称 使用方法 fun('sort@name',$id)
     * @param number $id
     * @return void|number|number[]|array|unknown[]|unknown
     */
    public function name($id=0){
        return get_sort($id);
   }
}