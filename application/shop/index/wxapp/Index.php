<?php
namespace app\shop\index\wxapp;

use app\common\controller\IndexBase;
use app\shop\model\Content as ContentModel;

//小程序显示内容
class Index extends IndexBase
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
        $array = getArray( ContentModel::getListByMid(1,$map,$order,$rows) );
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
        $map = ['status'=>2];
        $map['ispic'] = 1;
        $rows = 4;
        $array = getArray( ContentModel::getListByMid(1,$map,'id desc',$rows) );
        $items = [];        
        foreach($array['data'] AS $rs){
            $items[] = [
                    'id' => $rs['id'],
                    'name' => $rs['title'],
                    'picurl' => $rs['picurl'],
            ];
        }        
        return $this->ok_js($items);
    }
}













