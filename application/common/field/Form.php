<?php
namespace app\common\Field;

/**
 * 表单自定义字段
 */
class Form extends Base
{    
    /**
     * 取得某个字段的表单HTML代码
     * @param array $field 具体某个字段的配置参数
     * @param array $info 信息内容
     * @return string[]|unknown[]|mixed[]
     */
    public static function get_field($field=[],$info=[]){
        
        // 是否为必填选项
        if ($field['mustfill'] == '1') {
            $mustfill = '(<font color=red>*</font>)';
            $ifmust = " data-ifmust='1' ";
        }
        
        $_show = $show = '';
        $name = $field['name'];
        
        if(empty($info)){   //新发表,就用初始值
            $info[$name] = $field['value'];
        }
        
        if ( ($show = self::get_item($field['type'],$field,$info)) !='' ) {    //个性定义的表单模板,优先级最高
            
        }elseif ($field['type'] == 'textarea') {    // 多行文本框
            
            $field_inputwidth = 'width:90%;';
            $field_inputheight = 'height:300px;';
            $show = "<textarea $ifmust name='{$name}' id='atc_{$name}' class='c_{$name}' style='{$field_inputwidth}{$field_inputheight}'>{$info[$name]}</textarea>";
       
        }elseif ($field['type'] == 'select') {      // 下拉框
            
            $detail = parse_attr($field['options']);
            foreach ($detail as $key => $value) {
                $cked = $info[$name]==$key?' selected ':'';
                $_show .= "<option value='$key' $cked>$value</option>";
            }            
            $show = "<select $ifmust name='{$name}' id='atc_{$name}'><option value=''>请选择</option>$_show</select>";
        
        }elseif ($field['type'] == 'radio') {    // 单选按钮
            
            $detail = parse_attr($field['options']);
            foreach ($detail as $key => $value) {
                $cked = $info[$name]==$key?' checked ':'';
                $_show .= " <input $ifmust type='radio' name='{$name}' value='$key' {$cked}> $value ";
            }
            $show = $_show ;
       
        }elseif ($field['type'] == 'checkbox') {    // 多选按钮
            
            $_detail = explode(',',$info[$name]);
            $detail = parse_attr($field['options']);
            foreach ($detail as $key => $value) {
                $cked = in_array($key, $_detail)?' checked ':'';
                $_show .= " <input $ifmust type='checkbox' name='{$name}[]' value='$key' {$cked}> $value ";
            }            
            $show = "$_show ";
            
        }else{      // 全部归为单行文本框
            
            // 检验表单
            if ($field['js_check']) {
                $field['js_checkmsg'] || $field['js_checkmsg'] = '输入的内容不符合规则!';
                $jsck = 'onBlur="if(this.value!=\'\'&&' . $field['js_check'] .
                '.test(this.value)==false){alert(\'' .
                $field['js_checkmsg'] . '\');this.focus();}"';
            }
            
            $field_inputwidth = 'width:90%;';
            $show = " <input $ifmust $jsck type='text' name='{$name}' id='atc_{$name}' style='{$field_inputwidth}' class='c_{$name}' value='{$info[$name]}' />";
            
        }
        
        return [
                'value'=>$show,
                'title'=>$field['title'],
                'need'=>$mustfill,
                'about'=>$field['about'],
        ];        
    }
    
}
