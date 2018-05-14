<?php
namespace app\shop\index\wxapp;

use app\common\controller\IndexBase; 
use app\shop\model\Sort as SortModel;

//小程序 获取栏目信息
class Sorts extends IndexBase
{
    public function index(){
        $array = getArray(SortModel::getList());
        $items = [];        
        foreach($array AS $rs){
            $items[] = [
                    'id' => $rs['id'],
                    'name' => $rs['name'],
            ];
        }
        return $this->ok_js($items);
    }
}













