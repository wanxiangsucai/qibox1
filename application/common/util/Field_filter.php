<?php

namespace app\common\util;

class Field_filter{
    
    /**
     * 生成其它字段网址,不包含当前字段
     * @param string $field 字段变量名
     * @param number $mid 模型ID
     * @param string $dirname 频道目录名
     * @return string
     */
    public static function make_url($field='',$mid=0,$dirname=''){
        //$url = request()->url(true) . '?';
        $url = '';
        $input = input();
        $input['province_id'] && $url .= 'province_id=' . $input['province_id'] . '&';
        $input['city_id'] && $url .= 'city_id=' . $input['city_id'] . '&';
        $input['zone_id'] && $url .= 'zone_id=' . $input['zone_id'] . '&';
        $input['street_id'] && $url .= 'street_id=' . $input['street_id'] . '&';
        $array = self::get_field($mid,$dirname);
        foreach ($array AS $name=>$rs){
            if($field!=$name){
                if($input[$name]!==''&&$input[$name]!==null){
                    $url .= $name . '=' . $input[$name] . '&';
                }
            }
        }
        return $url;
    }
    
    /**
     * 取出筛选字段,参数已转为数组
     * @param number $mid 模型ID
     * @param string $dirname 频道目录名
     * @return array[]|string[]
     */
    public static function get_field($mid=0,$dirname=''){
        $data = [];
        $array = get_field($mid,$dirname);
        foreach ($array AS $rs){
            if(!$rs['ifsearch']){
                continue;
            }
            if(!in_array($rs['type'], ['select','radio','checkbox'])){
                continue ;      //只有下拉框,单选框 复选框才能有列表筛选
            }
            $rs['options'] = str_array($rs['options']);    //转义成数组
            $data[$rs['name']] = $rs;
        }
        return $data;
    }
}