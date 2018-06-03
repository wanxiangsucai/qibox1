<?php
namespace app\common\field;

/**
 * 自定义字段 POST数据的转义
 */
class Post
{
    public static function format($field=[],$data=[]){
        $name = $field['name'];
        $type = $field['type'];
        if (!isset($data[$name])) {
            switch ($type) {
                // 开关
                case 'switch':
                    $data[$name] = 0;
                    break;
                case 'checkbox':
                    $data[$name] = '';
                    break;
            }
        } else {
            // 如果值是数组则转换成字符串，适用于复选框等类型
            if (is_array($data[$name])) {
                $data[$name] = implode(',', $data[$name]);
                $type == 'checkbox' && $data[$name] = ','.$data[$name] .',';   //方便搜索 like %,$value,%
            }
            switch ($type) {
                // 开关
                case 'switch':
                    $data[$name] = 1;
                    break;
                case 'images2':
                    //$data[$name] = json_encode(array_values($data['images2'][$name]));
                    break;
                    // 日期时间
                case 'date':
                case 'time':
                case 'datetime':
                    $data[$name] = strtotime($data[$name]);
                    break;
            }
        }
        return isset($data[$name])?$data[$name]:null;   //这里要做个判断,MYSQL高版本,不能任意字段随意插入null
    }
}
