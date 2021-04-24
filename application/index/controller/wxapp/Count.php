<?php
namespace app\index\controller\wxapp;

use app\common\controller\IndexBase;
use app\common\fun\Label;
use think\Db;

class Count extends IndexBase
{
    /**
     * 万能数据表统计
     * @param string $table
     * @param string $where
     * @return void|\think\response\Json
     */
    public function index($table='',$where=''){
        if (!$where) {
            return $this->err_js('条件不能为空！');
        }elseif (!$table) {
            return $this->err_js('数据表不能为空！');
        }elseif(!preg_match("/^([a-z0-9_]+)$/", $table)){
            return $this->err_js('数据表不符合规范！');
        }elseif(!preg_match("/^([_a-z0-9=]+)$/", $table)){
            return $this->err_js('数据表不符合规范！');
        }elseif(!preg_match("/^([_a-z0-9=<>!&*@]+)$/", $where)){
            return $this->err_js('查询条件不符合规范！');
        }
        $tabel_array = cache2('count_table')?:[];
        if (!$tabel_array || !$tabel_array[$table]) {
            if (!is_table($table)) {
                return $this->err_js('数据表不存在！');
            }
            $tabel_array[$table] = table_field($table);
            cache2('count_table',$tabel_array);
        }
        $map = Label::where($where);
        foreach($map AS $key=>$value){
            if (!in_array($key, $tabel_array[$table])) {
                return $this->err_js($key.'字段不存在！');
            }
        }
        $num = Db::name($table)->where($map)->count( $tabel_array[$table]['uid']?:current($tabel_array[$table]) );
        return $this->ok_js(intval($num));        
    }
}
