<?php
namespace app\common\fun;

use think\Db;
use app\common\model\Groupcfg AS GroupcfgModel;

class Cfgfield{

    /**
     * 转义成输入表单的数组格式
     * @param array $list_data
     * @return string[][]|unknown[][]
     */
    public static function toForm($list_data=[]){
        $tab_list = [];
        foreach ($list_data as $key => $rs) {
            empty($rs['form_type']) && $rs['form_type'] = 'text';
            empty($rs['title']) && $rs['title'] = '未命名的字段：'.$rs['c_key'];
            if( in_array($rs['form_type'],['radio','select','checkbox','checkboxtree']) && !empty($rs['options']) ){
                if(preg_match('/^[a-z]+(\\\[_a-z]+)+@[_a-z]+/i',$rs['options'])){   //执行类
                    list($class_name,$action,$params) = explode('@',$rs['options']);
                    if(class_exists($class_name)&&method_exists($class_name, $action)){
                        $obj = new $class_name;
                        if ($params!='') {
                            $_params = json_decode($params,true)?:fun('label@where',$params);
                        }else{
                            $_params = [];
                        }
                        //$_params = $params!='' ? json_decode($params,true) : [] ;
                        //$rs['options'] = $obj->$action();
                        $rs['options'] = call_user_func_array([$obj, $action], isset($_params[0])?$_params:[$_params]);
                    }
                }elseif(preg_match('/^([\w]+)@([\w]+),([\w]+)/i',$rs['options'])){
                    list($table_name,$fields,$params) = explode('@',$rs['options']);
                    preg_match('/^qb_/i',$table_name) && $table_name = str_replace('qb_', '', $table_name);
                    if ($params!='') {
                        $map = json_decode($params,true)?:fun('label@where',$params);
                    }
                    is_array($map) || $map = [];
                    $rs['options'] = Db::name($table_name)->where($map)->column($fields);
                }else{
                    $rs['options'] = str_array($rs['options'],"\n");    //后台设置的下拉,多选,单选,都是用换行符做分割的
                }
            }
            $tab_list[]=[
                    $rs['form_type'],
                    $rs['c_key'],
                    $rs['title'],
                    $rs['c_descrip'],
                    $rs['options'],
                    '',
                    '',
                    $rs['htmlcode'],
                    
            ];
        }
        return $tab_list;
    }
    
    /**
     * 获取某个用户组的字段
     * @param number $groupid
     * @return mixed|\think\cache\Driver|boolean|unknown[][]|array[][]
     */
    public static function get_form_items($groupid=0){
        $list_data = cache('cfgfield_'.$groupid);
        if (empty($list_data)) {
            $list_data = GroupcfgModel::getListByGroup($groupid);
            $list_data = self::toForm($list_data);
            cache('cfgfield_'.$groupid,$list_data);
        }
        return $list_data;
    }
    
    /**
     * 转义成内容页显示的格式
     * @param array $info 数据库取出的自定义字段
     * @param number $groupid 用户组ID
     * @param string $pagetype
     * @return \app\common\field\string[]|\app\common\field\unknown[]|\app\common\field\mixed[]
     */
    public static function format($info=[],$groupid=0,$pagetype='show'){
        $field_array = self::get_form_items($groupid);
        if ($field_array) {
            $field_array = \app\common\field\Format::form_fields($field_array);
            foreach($field_array AS $name=>$rs){
                $info[$name] = \app\common\field\Index::get_field($rs,$info,$pagetype);
            }
        }        
        return $info;
    }
    
    
}