<?php
namespace app\shop\index;

use app\common\controller\index\Car AS _Car;

//购物车
class Car extends _Car
{
    public function index(){
        return parent::index();
    }
    
    public function act($shopid=0,$type=''){
        return parent::act($shopid,$type);
    }
}

