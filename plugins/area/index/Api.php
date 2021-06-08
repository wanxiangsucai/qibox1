<?php
namespace plugins\area\index;
use app\common\controller\IndexBase; 
use plugins\area\model\Area AS AreaModel;


class Api extends IndexBase
{
    public function getlist($iftop=0,$pid=0,$fid=null){
        if ($fid && empty($pid)) {
            $pid = $fid;
        }
        $pid = intval($pid);
        if(empty($iftop) && empty($pid) && $fid===null){
            return $this->err_js('pid不存在');
        }
        $data = AreaModel::where(['pid'=>$pid])->column(true);
        if($data){
            return $this->ok_js(array_values($data));
        }else{
            return $this->err_js('数据不存在');
        }
    }
}
