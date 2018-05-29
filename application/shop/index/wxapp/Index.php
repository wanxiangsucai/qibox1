<?php
namespace app\shop\index\wxapp;

use app\common\controller\index\wxapp\Index AS _Index; 

//小程序显示内容
class Index extends _Index
{
    /**
     * 内容列表
     * @param number $fid
     * @param number $rows
     * @return \think\response\Json
     */
    public function index($fid=0 , $rows = 0){
        $map = [];
        $fid && $map = ['fid'=>$fid];
        $rows || $rows = 4;        
        $order = 'id desc';        
        $array = getArray( $this->model->getListByMid(1,$map,$order,$rows) );
        $items = [];
        foreach($array['data'] AS $key=>$rs){
            $rs['create_time'] = date('Y-m-d H:i',$rs['create_time']);
            unset($rs['content'],$rs['full_content']);
            $array['data'][$key] = $rs;
        }
        return $this->ok_js($array);
    }
    
    /**
     * 组图
     * @return \think\response\Json
     */
    public function banner(){
        $map = [];
        $map['ispic'] = 1;
        $rows = 4;
        $array = $this->model->getListByMid(1,$map,'id desc',$rows);
        $array->each(function(&$rs){
            unset($rs['content'],$rs['full_content']);
            return $rs;
        });
        return $this->ok_js($array);
    }
}













