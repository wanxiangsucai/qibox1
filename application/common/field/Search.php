<?php
namespace app\common\Field;

/**
 * 自定义字段 搜索查找方式
 */
class Search
{
    public static function get_map($type=[],$value=''){
        if(in_array($type, ['radio','select'])){
            $map = ['=',$value];
        }elseif($type=='checkbox'){
            $map = ['like',"%,$value,%"];
        }else{
            $map = ['like',"%$value%"];
        }
        return $map;
    }
}
