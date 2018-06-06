<?php
namespace app\common\field;

/**
 * 把程序中定义的字段,转为数据库field表中的格式,方便后续统一处理
 */
class Format
{    
    /**
     * post表单字段的处理
     * @param array $data
     */
    public static function form_fields($data=[]){
        $array = [];
        foreach($data AS $rs){
            $array[$rs[1]] = [
                    'type'=>$rs['0'],
                    'name'=>$rs['1'],
                    'title'=>$rs['2'],
                    'about'=>$rs['3'],
                    'options'=>$rs['4'],
                    'value'=>$rs['5'],
            ];
        }
        return $array;
    }
    
    /**
     * table表单字段的处理
     * @param array $data
     */
    public static function table_fields($data=[]){
        
        $this->list_items = [
                ['title', '字段名称', 'text'],
                ['name', '字段变量名', 'text'],
                ['type', '表单类型', 'select',config('form')],
                ['list', '排序值', 'text.edit'],
        ];
        
        $array = [];
        foreach($data AS $rs){
            $array[$rs[1]] = [
                    'type'=>$rs['2'],
                    'name'=>$rs['0'],
                    'title'=>$rs['1'],
                    'options'=>$rs['3'],
                    'value'=>$rs['4'],
                    'config'=>$rs['5'],
            ];
        }
        return $array;
    }
    
}
