<?php
namespace app\common\field;

/**
 * 把程序中定义的字段,转为数据库field表中的格式,方便后续统一处理
 */
class Format
{    
    /**
     * 把程序中定义的字段,转成跟数据库中的格式类似,程序中定义的是以数组下标为数字开始的 比如 [ ['text','title','标题'] ]
     * @param array $data
     */
    public static function form_fields($data=[]){
        $array = [];
        foreach($data AS $key=>$rs){
            if (is_numeric($key) && $rs[0]) {
                if($rs['0']=='callback'){  //回调函数
                    $arr = [
                        'type'=>$rs['0'],
                        'name'=>$rs['1'],
                        'title'=>$rs['2'],
                        'about'=>$rs['3'],
                        'fun'=>$rs['4'],
                        'value'=>$rs['5'],
                    ];
                }elseif($rs['0']=='hidden'){    //隐藏域有点特殊
                    $arr = [
                        'type'=>$rs['0'],
                        'name'=>$rs['1'],
                        'value'=>$rs['2'],
                    ];
                //}elseif(in_array($rs['0'], ['select','checkbox','checkboxtree','radio'])){
                }elseif(is_array($rs['4']) || (!in_array($rs['0'], ['images2','files2','array','array2','ueditor','text','textarea','shop_array','links','links','treeone','treemore','treemake'])&&strstr($rs['4'],'"')&&json_decode($rs['4'],true)) ){
                    $arr = [
                        'type'=>$rs['0'],
                        'name'=>$rs['1'],
                        'title'=>$rs['2'],
                        'about'=>$rs['3'],
                        'options'=>$rs['4'],
                        'value'=>$rs['5'],
                    ];
                }else{
                    $arr = [
                        'type'=>$rs['0'],
                        'name'=>$rs['1'],
                        'title'=>$rs['2'],
                        'about'=>$rs['3'],
                        'value'=>$rs['4'],
                        'options'=>$rs['5'],
                    ];
                }
                $array[$rs[1]] = $arr+$rs;
            }else{
                $array[$rs['name']] = $rs;
            }
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
