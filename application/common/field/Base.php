<?php
namespace app\common\field;

/**
 * 自定义字段
 */
class Base
{
    protected static $pagetype = 'form';   //页面类型,表单页或内容页 form show
    
    /**
     * 自定义模板元素
     * @param string $type 表单类型
     * @param array $field 某个字段的配置参数
     * @param array $info 信息内容
     * @param string $pagetype 参数主要是show 或 list 哪个页面使用,主要是针对显示的时候,用在列表页或者是内容页 , 内容页会完全转义,列表页的话,可能只转义部分,或者干脆不转义
     * @return mixed
     */
    protected static function get_item($type='',$field=[],$info=[],$pagetype='show'){
        $file = __DIR__ . '/'  . $type . '/' . static::$pagetype . '.php';
        $string = '';
        $name = $field['name'];
        if(is_file($file)){
            $string = include($file);
        }
        $static = config('view_replace_str.__STATIC__');
        $string = str_replace(['__STATIC__'], [$static], $string);
        return $string;
    }
    
    
    /**
     * 取得某个字段转义后的HTML代码
     * @param array $field 具体某个字段的配置参数
     * @param array $info 信息内容
     * @param string $pagetype 参数主要是show 或 list 哪个页面使用,主要是针对显示的时候,用在列表页或者是内容页 , 内容页会完全转义,列表页的话,可能只转义部分,或者干脆不转义
     * @return string[]|unknown[]|mixed[]
     */
    public static function format_field($field=[],$info=[],$pagetype='list'){
        
        $name = $field['name'];
        $f_value = $info[$name];
        if($info[$name]===''||$info[$name]===null){
            return '';
        }
        if ( ($show = self::get_item($field['type'],$field,$info)) !='' ) {    //个性定义的表单模板,优先级最高
            
        }elseif(in_array($field['type'],['images','files','image','file','jcrop','images2'])){
            
            $show = self::format_url($field,$info);
            
        }elseif ($field['type'] == 'textarea') {    // 多行文本框
            
            $show = str_replace([' ',"\r\n"], ['&nbsp;','<br>'], $f_value);
            
        }elseif ($field['type'] == 'select' || $field['type'] == 'radio') {      // 下拉框 或 单选按钮
            
            $detail = parse_attr($field['options']);
            $show = $detail[$f_value];
            
        }elseif ($field['type'] == 'checkbox') {    //复选框
            
            $array = [];
            $detail = parse_attr($field['options']);
            foreach(explode(',',$f_value) AS $v){
                if($v===''){
                    continue ;
                }
                $array[] = $detail[$v];
            }
            $show = implode('、',$array);
            
        }elseif($field['type']=='date'){
            
            $show = date('Y-m-d',$info[$name]);
            
        }elseif($field['type']=='datetime'){
            
            $show = date('Y-m-d H:i',$info[$name]);
            
        }else{  //直接输出
            
            $show = $info[$name];
            
        }
        
        return $show;
    }
    
    /**
     * 对字段包含有附件的路径补全转义
     * @param array $field 某个字估的配置信息
     * @param array $info 内容原始数据
     * @return void|string|unknown|void[]|string[]|array[]
     */
    public static function format_url($field=[],$info=[]){
        $name = $field['name'];
        $f_value = $info[$name];
        
        if($field['type'] == 'images'||$field['type'] == 'files'){
            
            $detail = explode(',',$f_value);
            $value = [];
            foreach($detail AS $va){
                if($field['type'] == 'images'){
                    $va && $value[]['picurl'] = tempdir($va);
                }else{
                    $va && $value[]['url'] = tempdir($va);
                }
            }
            $f_value = $value;
            
        }elseif($field['type'] == 'image'||$field['type'] == 'file'||$field['type'] == 'jcrop'){
            
            $f_value && $f_value = tempdir($f_value);
            
        }elseif($field['type'] == 'images2'){
            
            $value = json_decode($f_value,true);
            foreach($value AS $k=>$vs){
                $vs['picurl'] = tempdir($vs['picurl']);
                $value[$k] = $vs;
            }
            $f_value = $value;
        }
        return $f_value;
    }
    
}
